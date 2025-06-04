<?php

namespace Payplug\Responses;

class HostedFieldTransactionResource
{
	public $id;
	public $object;
	public $is_live = null;
	public $amount;
	public $amount_refunded = null;
	public $currency;
	public $created_at;
	public $description;
	public $is_paid;
	public $paid_at;
	public $is_refunded = null;
	public $is_3ds;
	public $save_card = false;

	public $card = [];
	public $hosted_payment = [
		'payment_url' => null,
		'return_url' => null,
		'cancel_url' => null,
		'paid_at' => null,
		'sent_by' => null,
	];

	public $notification = [
		'url' => null,
		'response_code' => null,
	];
	public $metadata = [
		'order_id' => null,
		'customer_id' => null,
		'domain' => null,
	];

	public $failure;
	public $installment_plan_id = null;
	public $authorization = null;
	public $refundable_after = null;
	public $refundable_until = null;
	public $integration = null;
	public $payment_method = [
		'type' => 'payplug',
		'transaction_flow' => null,
	];
	public $billing = [];
	public $shipping = [];

	public function __construct($data = [])
	{
		if (empty($data) || !is_array($data)) {
			return;
		}

		$payment_data = !empty($data['DATA'][0]) ? $data['DATA'][0] : null;
		$this->id = $payment_data['TRANSACTIONID'] ?? null;
		$this->object = $payment_data['OPERATIONTYPE'] ?? null;
		$this->amount = $payment_data['AMOUNT'] ?? 0;
		$this->currency = $payment_data['CURRENCY'] ?? 'EUR';
		$this->created_at = $payment_data['DATE'] ?? null;
		$this->description = $payment_data['DESCRIPTION'] ?? '';
		$this->is_paid = $payment_data['DATE'] ?? false;
		$this->paid_at = $payment_data['DATE'] ?? null;
		$this->is_3ds = $payment_data['3DSECURE'] ?? false;
		$this->card = [
			'last4' => null,
			'exp_month' => null,
			'exp_year' => null,
			'brand' => $payment_data['CARDTYPE'] ?? null,
			'country' => $payment_data['CARDCOUNTRY'] ?? null,
		];

		$this->notification['response_code'] = $payment_data['EXECCODE'] ?? false;
		$this->metadata["order_id"] = $payment_data['ORDERID'] ?? null;
		$this->metadata["customer_id"] = $payment_data['IDENTIFIER'] ?? null;
		$this->hosted_payment['paid_at'] = $payment_data['DATE'] ?? null;

		//handling error codes
		if($payment_data['EXECCODE'] != "0000" ){
			$this->failure = Array(
				'code' => $payment_data['EXECCODE'] ?? null,
				'message' => $payment_data['MESSAGE'] ?? null,
				'details' => $payment_data['DETAILS'] ?? null,
			);
		}
	}

}
