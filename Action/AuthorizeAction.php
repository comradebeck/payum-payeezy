<?php
namespace SlimDash\Payeezy\Action;

use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Request\Authorize;
use SlimDash\Payeezy\Action;

class AuthorizeAction extends Api\BaseApiAwareAction {
	/**
	 * {@inheritDoc}
	 *
	 * @param Authorize $request
	 */
	public function execute($request) {
		/* @var $request Authorize */
		RequestNotSupportedException::assertSupports($this, $request);
		$details = ArrayObject::ensureArrayObject($request->getModel());
		$details['transaction_type'] = 'authorize';
		if (!isset($details['method'])) {
			$details['method'] = 'credit_card';
		}

		$result = $this->api->doRequest($details->toUnsafeArray());
		$details->replace(get_object_vars($result));
	}

	/**
	 * {@inheritDoc}
	 */
	public function supports($request) {
		return
		$request instanceof Authorize &&
		$request->getModel() instanceof \ArrayAccess
		;
	}
}