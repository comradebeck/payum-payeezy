<?php
namespace SlimDash\Payeezy;

use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\GatewayFactory;
use SlimDash\Payeezy\Action;

class PayeezyGatewayFactory extends GatewayFactory {
	/**
	 * {@inheritDoc}
	 */
	protected function populateConfig(ArrayObject $config) {
		$config->defaults([
			'payum.factory_name' => 'payeezy',
			'payum.factory_title' => 'Payeezy',
			'payum.action.capture' => new \SlimDash\Payeezy\Action\CaptureAction(),
			'payum.action.authorize' => new \SlimDash\Payeezy\Action\AuthorizeAction(),
			'payum.action.refund' => new \SlimDash\Payeezy\Action\RefundAction(),
			'payum.action.cancel' => new \SlimDash\Payeezy\Action\CancelAction(),
			'payum.action.status' => new \SlimDash\Payeezy\Action\StatusAction(),
			'payum.action.convert_payment' => new \SlimDash\Payeezy\Action\ConvertPaymentAction(),
		]);

		if (false == $config['payum.api']) {
			$config['payum.default_options'] = array(
				'sandbox' => true,
			);
			$config->defaults($config['payum.default_options']);

			$config['payum.api'] = function (ArrayObject $config) {
				return new Api((array) $config);
			};
		}
	}
}
