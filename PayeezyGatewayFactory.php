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
			'payum.action.capture' => new CaptureAction(),
			'payum.action.authorize' => new AuthorizeAction(),
			'payum.action.refund' => new RefundAction(),
			'payum.action.cancel' => new CancelAction(),
			'payum.action.status' => new StatusAction(),
			'payum.action.convert_payment' => new ConvertPaymentAction(),
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
