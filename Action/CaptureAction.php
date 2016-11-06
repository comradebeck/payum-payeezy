<?php
namespace Payum\Payeezy\Action;

use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Request\Capture;
use Payum\Payeezy;

class CaptureAction extends Api\BaseApiAwareAction {
	/**
	 * {@inheritDoc}
	 *
	 * @param Capture $request
	 */
	public function execute($request) {
		/* @var $request Capture */
		RequestNotSupportedException::assertSupports($this, $request);
		$details = ArrayObject::ensureArrayObject($request->getModel());
		$details['transaction_type'] = 'purchase';
		if (!isset($details['method'])) {
			$details['method'] = 'credit_card';
		}
		$transaction_id = null;

		if (isset($details['transaction_id'])) {
			$transaction_id = $details['transaction_id'];
			$details['transaction_type'] = 'capture';
			unset($details['transaction_id']);
		}

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
