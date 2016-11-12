<?php
namespace SlimDash\Payeezy\Action;

use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Request\Cancel;
use SlimDash\Payeezy\Action;

class CancelAction extends Api\BaseApiAwareAction
{
    /**
     * {@inheritDoc}
     *
     * @param Cancel $request
     */
    public function execute($request)
    {
        /* @var $request Cancel */
        RequestNotSupportedException::assertSupports($this, $request);
        $details = ArrayObject::ensureArrayObject($request->getModel());
        if (!isset($details['method'])) {
            $details['method'] = 'credit_card';
        }
        $details['transaction_type'] = 'void';

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
    public function supports($request)
    {
        return
        $request instanceof Cancel &&
        $request->getModel() instanceof \ArrayAccess
        ;
    }
}
