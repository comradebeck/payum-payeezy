<?php
namespace Payum\Payeezy\Action;

use Payum\Core\Action\ActionInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Request\GetStatusInterface;
use Payum\Payeezy;

class StatusAction implements ActionInterface {
	/**
	 * {@inheritDoc}
	 *
	 * @param GetStatusInterface $request
	 */
	public function execute($request) {
		RequestNotSupportedException::assertSupports($this, $request);
		$model = new ArrayObject($request->getModel());
		if (null === $model['transaction_status']) {
			$request->markNew();
			return;
		}
		// $tstat = ;
		// $vstat = $model['validation_status'];

		if ('approved' === $model['transaction_status']) {
			$ttype = $model['transaction_type'];
			if ($ttype === 'authorize') {
				$request->markAuthorized();
			} else if ($ttype === 'capture' || $ttype === 'purchase') {
				$request->markCaptured();
			} else if ($ttype === 'void') {
				$request->markCanceled();
			} else if ($ttype === 'refund') {
				$request->markRefunded();
			} else {
				$request->markUnknown();
			}
			return;
		}

		$request->markFailed();
	}

	/**
	 * {@inheritDoc}
	 */
	public function supports($request) {
		return
		$request instanceof GetStatusInterface &&
		$request->getModel() instanceof \ArrayAccess
		;
	}
}
