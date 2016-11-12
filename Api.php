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
		return
		$this->options['sandbox']
		? 'https://api-cert.payeezy.com/v1/transactions'
		: 'https://api.payeezy.com/v1/transactions'
		;
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
			'Content-Type' => 'application/json',
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
		$payload = json_encode($fields, JSON_FORCE_OBJECT);
		$headers = $this->hmacAuthorizationToken($payload);

		$client = new \GuzzleHttp\Client([
			'defaults' => [
				'headers' => $headers,
				'body' => $payload,
			],
		]);
		$request = new \GuzzleHttp\Psr7\Request('POST', $url);
		$response = $client->send($request);

		if (false == ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300)) {
			throw HttpException::factory($request, $response);
		}
		$result = json_decode($response->getBody()->getContents());
		if (null === $result) {
			throw new LogicException("Response content is not valid json: \n\n{$response->getBody()->getContents()}");
		}

		return $result;
	}
}
