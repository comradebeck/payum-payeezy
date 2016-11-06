<?php
namespace Payum\Payeezy\Action;

use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;

class RefundAction extends BaseApiAwareAction {
	/**
	 * {@inheritDoc}
	 *
	 * @param Capture $request
	 */
	public function execute($request) {
		/* @var $request Capture */
		RequestNotSupportedException::assertSupports($this, $request);
		$details = ArrayObject::ensureArrayObject($request->getModel());
		$transaction_id = $details['transaction_id'];
		if (!isset($details['method'])) {
			$details['method'] = 'credit_card';
		}
		$details['transaction_type'] = 'refund';
		unset($details['transaction_id']);

		$this->api->doRequest($details->toUnsafeArray(), $transaction_id);
		$model->replace((array) $result);
	}

	/**
	 * {@inheritDoc}
	 */
	public function supports($request) {
		return
		$request instanceof Capture &&
		$request->getModel() instanceof \ArrayAccess
		;
	}
}