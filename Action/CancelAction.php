<?php
namespace Payum\Payeezy\Action;

use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;

class CancelAction extends BaseApiAwareAction {
	/**
	 * {@inheritDoc}
	 *
	 * @param Cancel $request
	 */
	public function execute($request) {
		/* @var $request Cancel */
		RequestNotSupportedException::assertSupports($this, $request);
		$details = ArrayObject::ensureArrayObject($request->getModel());
		$transaction_id = $details['transaction_id'];
		if (!isset($details['method']])) {
			$details['method'] = 'credit_card';
		}
		$details['transaction_type'] = 'void';
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