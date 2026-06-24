<?php

namespace Payplug\Responses;

class HostedFieldTransactionResource
{
    public $id;
    public $object;
    public $is_live = true;
    public $is_hosted_fields = true;
    public $amount;
    public $amount_refunded = null;
    public $currency;
    public $created_at;
    public $description;
    public $is_paid = false;
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
    public $capture_transaction_ids = [];

    /**
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        if (empty($data['DATA']) || !is_array($data['DATA'])) {
            return;
        }

        $unfilteredTransactions = $data['DATA'];
        $paymentTransaction = $this->getLatestPaymentTransaction($unfilteredTransactions);

        if (empty($paymentTransaction['TRANSACTIONID'])) {
            return;
        }

        $indexedTransactionsById = $this->indexTransactionsById($unfilteredTransactions);
        $filteredTransactions = $paymentTransaction ? $this->collectTransactionChain($paymentTransaction['TRANSACTIONID'], $indexedTransactionsById) : [];

        $executionCode = !empty($paymentTransaction['EXECCODE']) ? $paymentTransaction['EXECCODE'] : null;

        $this->id = !empty($paymentTransaction['TRANSACTIONID']) ? $paymentTransaction['TRANSACTIONID'] : null;
        $this->object = !empty($paymentTransaction['OPERATIONTYPE']) ? $paymentTransaction['OPERATIONTYPE'] : null;
        $this->amount = !empty($paymentTransaction['AMOUNT']) ? $paymentTransaction['AMOUNT'] * 100 : 0;
        $this->currency = !empty($paymentTransaction['CURRENCY']) ? $paymentTransaction['CURRENCY'] : 'EUR';
        $this->created_at = !empty($paymentTransaction['DATE']) ? strtotime($paymentTransaction['DATE']) : null;
        $this->description = !empty($paymentTransaction['DESCRIPTION']) ? $paymentTransaction['DESCRIPTION'] : '';
        $this->is_3ds = !empty($paymentTransaction['3DSECURE']) ? $paymentTransaction['3DSECURE'] === 'yes' : false;
        $this->card = [
            'id'=> !empty($paymentTransaction['ALIAS']) ? $paymentTransaction['ALIAS'] : null,
            'last4' => !empty($paymentTransaction['CARDCODE']) ? substr($paymentTransaction['CARDCODE'], -4) : null,
            'exp_month' => substr(!empty($paymentTransaction['CARDVALIDITYDATE']) ? $paymentTransaction['CARDVALIDITYDATE'] : '', 0, 2) ?: null,
            'exp_year' => substr(!empty($paymentTransaction['CARDVALIDITYDATE']) ? $paymentTransaction['CARDVALIDITYDATE'] : '', -2) ?: null,
            'brand' => !empty($paymentTransaction['CARDTYPE']) ? $paymentTransaction['CARDTYPE'] : null,
            'country' => !empty($paymentTransaction['CARDCOUNTRY']) ? $paymentTransaction['CARDCOUNTRY'] : null,
        ];

        $this->notification['response_code'] = $executionCode;
        $this->metadata["order_id"] = !empty($paymentTransaction['ORDERID']) ? $paymentTransaction['ORDERID'] : null;
        $this->metadata["customer_id"] = !empty($paymentTransaction['IDENTIFIER']) ? $paymentTransaction['IDENTIFIER'] : null;

        // FAILURE
        if ($executionCode !== '0000') {
            $this->failure = [
                'code' => $executionCode,
                'message' => !empty($paymentTransaction['MESSAGE']) ? $paymentTransaction['MESSAGE'] : null,
                'details' => !empty($paymentTransaction['DETAILS']) ? $paymentTransaction['DETAILS'] : null,
            ];
        }

        // PAID STATUS
        if ($this->object === 'payment') {
            $this->is_paid = $executionCode === '0000';
            $this->paid_at = $executionCode === '0000' && !empty($paymentTransaction['DATE'])
                ? strtotime($paymentTransaction['DATE']) : null;
            $this->hosted_payment['paid_at'] = !empty($paymentTransaction['DATE']) ? $paymentTransaction['DATE'] : null;
        }

        // AUTHORIZATION / CAPTURE
        if ($this->object === 'authorization') {
            $this->authorization = [
                'is_authorized' => true,
                'authorized_at' => $this->created_at,
                'expires_at' => null,
                'authorized_amount' => $this->amount,
            ];

            $captures = array_filter($filteredTransactions, static function (array $row) {
                $isCaptureTransaction = !empty($row['OPERATIONTYPE']) && $row['OPERATIONTYPE'] === 'capture';
                $isSuccessTransaction = !empty($row['EXECCODE']) && $row['EXECCODE'] === '0000';

                return $isCaptureTransaction === true && $isSuccessTransaction === true;
            });

            $totalCaptured = 0;
            $lastCaptureDate = null;

            foreach ($captures as $capture) {
                $captureDate = !empty($capture['DATE']) ? $capture['DATE'] : null;
                $captureAmount = (float) (!empty($capture['AMOUNT']) ? $capture['AMOUNT'] : 0);

                if ($captureDate !== null && $captureAmount > 0) {
                    $totalCaptured += (int) ($captureAmount * 100);
                    $lastCaptureDate = max($lastCaptureDate, strtotime($captureDate));

                    $this->capture_transaction_ids[] = $capture['TRANSACTIONID'];
                }
            }

            if ($totalCaptured >= $this->amount) {
                $this->is_paid = true;
                $this->paid_at = $lastCaptureDate;
            }
        }

        // REFUND
        $refunds = array_filter($filteredTransactions, static function (array $row) {
            $isRefundTransaction = !empty($row['OPERATIONTYPE']) && $row['OPERATIONTYPE'] === 'refund';
            $isSuccessTransaction = !empty($row['EXECCODE']) && $row['EXECCODE'] === '0000';

            return $isRefundTransaction === true && $isSuccessTransaction === true;
        });

        $totalRefunded = 0;
        $lastRefundDate = null;

        foreach ($refunds as $refund) {
            $refundDate = !empty($refund['DATE']) ? $refund['DATE'] : null;
            $refundAmount = (float) (!empty($refund['AMOUNT']) ? $refund['AMOUNT'] : 0);

            if ($refundDate !== null && $refundAmount > 0) {
                $totalRefunded += (int) ($refundAmount * 100);
                $lastRefundDate = max($lastRefundDate, strtotime($refundDate));
            }
        }

        if ($totalRefunded >= $this->amount) {
            $this->is_refunded = true;
        }
    }

    private function indexTransactionsById(array $transactions)
    {
        $index = [];
        foreach ($transactions as $transaction) {
            $index[$transaction['TRANSACTIONID']] = $transaction;
        }
        return $index;
    }

    private function getLatestPaymentTransaction(array $transactions)
    {
        $mainTypes = ['payment', 'authorization'];

        $mains = [];
        foreach ($transactions as $transaction) {
            if (in_array($transaction['OPERATIONTYPE'], $mainTypes, true)) {
                $mains[] = $transaction;
            }
        }

        // DATE au format Y-m-d H:i:s : le tri lexicographique = tri chronologique.
        usort($mains, function ($a, $b) {
            return strcmp($b['DATE'], $a['DATE']);
        });

        return isset($mains[0]) ? $mains[0] : null;
    }

    private function collectTransactionChain($rootId, array $transactionsById)
    {
        $childLinks = ['CAPTUREDBY', 'REFUNDEDBY'];

        $chain = [];
        $pending = [$rootId];

        while (!empty($pending)) {
            $id = array_pop($pending);

            if (!isset($transactionsById[$id]) || isset($chain[$id])) {
                continue;
            }

            $chain[$id] = $transactionsById[$id];

            foreach ($childLinks as $link) {
                if (!empty($transactionsById[$id][$link])) {
                    $pending[] = $transactionsById[$id][$link];
                }
            }
        }

        return array_values($chain);
    }
}
