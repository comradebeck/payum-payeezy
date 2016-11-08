<?php
namespace SlimDash\Payeezy;

use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception;
use Payum\Core\HttpClientInterface;

class Api {
	/**
	 * @var array
	 */
	protected $options = array(
		'apiKey' => null,
		'apiSecret' => null,
		'merchantToken' => null,
		'sandbox' => null,
	);

	/**
	 * @param array               $options
	 * @param HttpClientInterface $client
	 *
	 * @throws \Payum\Core\Exception\InvalidArgumentException if an option is invalid
	 */
	public function __construct(array $options) {
		$options = ArrayObject::ensureArrayObject($options);
		$options->defaults($this->options);
		$options->validateNotEmpty(array(
			'apiKey',
			'apiSecret',
			'merchantToken',
		));
		if (false == is_bool($options['sandbox'])) {
			throw new LogicException('The boolean sandbox option must be set.');
		}
		$this->options = $options;
	}

	/**
	 * @return string
	 */
	protected function getApiEndpoint() {
		return $this->options['sandbox'] ? 'https://api-cert.payeezy.com/v1/transactions' : 'https://api.payeezy.com/v1/transactions';
	}

	/**
	 * Payeezy
	 *
	 * HMAC Authentication
	 */
	public function hmacAuthorizationToken($payload) {
		$nonce = strval(hexdec(bin2hex(openssl_random_pseudo_bytes(4, $cstrong))));
		$timestamp = strval(time() * 1000); //time stamp in milli seconds
		$data = $this->options['apiKey'] . $nonce . $timestamp . $this->options['merchantToken'] . $payload;
		$hashAlgorithm = "sha256";
		$hmac = hash_hmac($hashAlgorithm, $data, $this->options['apiSecret'], false); // HMAC Hash in hex
		$authorization = base64_encode($hmac);

		return array(
			'Authorization' => $authorization,
			'nonce' => $nonce,
			'timestamp' => $timestamp,
			'apikey' => $this->options['apiKey'],
			'token' => $this->options['merchantToken'],
		);
	}

	/**
	 * @param array   $fields
	 * @param string  $transaction_id
	 *
	 * @return array
	 */
	public function doRequest($fields = array(), $transaction_id = null) {
		$url = $this->getApiEndpoint();
		if (isset($transaction_id)) {
			$url = $url . '/' . $transaction_id;
		}
		$method = 'POST';
		$payload = json_encode($fields, JSON_FORCE_OBJECT);
		$headerArray = $this->hmacAuthorizationToken($payload);
		$headers = array(
			'Content-Type: application/json',
		);
		foreach ($headerArray as $key => $value) {
			array_push($headers, $key . ':' . $value);
		}
		$response = $this->doCurl($url, $headers, $payload);
		$statusCode = $response['http_code'];
		if (false == ($statusCode >= 200 && $statusCode < 300)) {
			throw new LogicException("Invalid response: \n\n$result");
		}

		$body = $response['response'];
		$result = json_decode($body);
		if (null === $result) {
			throw new LogicException("Response content is not valid json: \n\n{$body}");
		}

		return $result;
	}

	/**
	 * making curl request
	 * @param  string $url     the url
	 * @param  array  $headers array of string headers
	 * @param  string $payload payload json
	 * @return array           array of response data
	 */
	public function doCurl($url, $headers, $payload) {
		$request = curl_init();
		curl_setopt($request, CURLOPT_URL, $url);
		curl_setopt($request, CURLOPT_POST, true);
		curl_setopt($request, CURLOPT_POSTFIELDS, $payload);
		curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($request, CURLOPT_HEADER, false);
		//curl_setopt($request, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($request, CURLOPT_HTTPHEADER, $headers);
		$response = curl_exec($request);
		$result = array(
			'response' => $response,
			'curl_error' => '',
			'http_code' => '',
			'last_url' => '');

		$error = curl_error($request);
		if ($error != "") {
			$result['curl_error'] = $error;
			return $result;
		}

		$result['http_code'] = curl_getinfo($request, CURLINFO_HTTP_CODE);
		$result['last_url'] = curl_getinfo($request, CURLINFO_EFFECTIVE_URL);
		return $result;
	}
}
