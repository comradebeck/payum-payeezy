<?php
namespace SlimDash\Payeezy\Action;

use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Request\Capture;
use SlimDash\Payeezy\Action;

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
		if (isset($details['transaction_tag'])) {
			$transaction_id = $details['transaction_id'];
			unset($details['transaction_id']);
		}

		$result = $this->api->doRequest($details->toUnsafeArray(), $transaction_id);
		$details->replace(get_object_vars($result));
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
