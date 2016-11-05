<?php
namespace Payum\Payeezy\Action\Api;

use Payum\Core\Action\ActionInterface;
use Payum\Core\ApiAwareInterface;
use Payum\Core\Exception\UnsupportedApiException;
use Payum\Payeezy\Api;

abstract class BaseApiAwareAction implements ActionInterface, ApiAwareInterface {
	/**
	 * @var \Payum\Payeezy\Api
	 */
	protected $api;

	/**
	 * {@inheritdoc}
	 */
	public function setApi($api) {
		if (false == $api instanceof Api) {
			throw new UnsupportedApiException('Not supported.');
		}
		$this->api = $api;
	}
}