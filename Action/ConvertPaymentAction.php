<?php
namespace Payum\Payeezy\Action;

use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Model\PaymentInterface;
use Payum\Core\Request\Convert;

class ConvertPaymentAction extends Api\BaseApiAwareAction {

	/**
	 * {@inheritDoc}
	 *
	 * @param Convert $request
	 */
	public function execute($request) {
		RequestNotSupportedException::assertSupports($this, $request);
		/** @var PaymentInterface $payment */
		$payment = $request->getSource();
		$details = ArrayObject::ensureArrayObject($payment->getDetails());
		$details["merchant_ref"] = $payment->getNumber();
		$details["amount"] = $payment->getTotalAmount();
		$details["currency_code"] = $payment->getCurrencyCode();
		if ($card = $payment->getCreditCard()) {
			if ($card->getToken()) {
				$details["token"] = $card->getToken();
				$details["credit_card"] = [
					"token_type" => "FDToken",
					"token_data" => SensitiveValue::ensureSensitive([
						'value' => $card->getToken(),
						'exp_date' => $card->getExpireAt()->format('YYmm'),
						'cardholder_name' => $card->getHolder(),
						'type ' => $card->getBrand(),
					])];
			} else {
				$details["credit_card"] = SensitiveValue::ensureSensitive([
					'card_number' => $card->getNumber(),
					'exp_date' => $card->getExpireAt()->format('YYmm'),
					'cvv' => $card->getSecurityCode(),
					'cardholder_name' => $card->getHolder(),
					'type ' => $card->getBrand(),
				]);
			}
		}

		$request->setResult((array) $details);
	}

	/**
	 * {@inheritDoc}
	 */
	public function supports($request) {
		return
		$request instanceof Convert &&
		$request->getSource() instanceof PaymentInterface &&
		$request->getTo() == 'array'
		;
	}
}
