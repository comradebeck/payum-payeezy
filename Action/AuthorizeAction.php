<?php
namespace Payum\Payeezy\Action;

use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;

class AuthorizeAction extends BaseApiAwareAction {
	/**
	 * {@inheritDoc}
	 *
	 * @param Capture $request
	 */
	public function execute($request) {
		/* @var $request Capture */
		RequestNotSupportedException::assertSupports($this, $request);
		$details = ArrayObject::ensureArrayObject($request->getModel());
		$details['transaction_type'] = 'authorize';
		$details['method'] = 'credit_card';
		$this->api->doRequest($details->toUnsafeArray());
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