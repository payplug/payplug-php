<?php
namespace Payplug\Responses;
class HostedFieldRefundTransaction
{
    public $id;
    public $object;
    public $payment_id;
    public $amount;
    public $currency;
    public $created_at;
    public $description = '';
    public $status = null;
    public $failure = null;

    public function __construct($data = [])
    {
        if (empty($data) || !is_array($data)) {
            return;
        }
        $this->id = !empty($data['TRANSACTIONID']) ? $data['TRANSACTIONID'] : null;
        $this->object = !empty($data['object']) ? $data['object'] : 'refund';
        $this->amount = !empty($data['AMOUNT']) ? $data['AMOUNT'] : 0;
        $this->currency = !empty($data['currency']) ? $data['currency'] : 'EUR';
        $this->created_at = !empty($data['created_at']) ? $data['created_at'] : null;
        $this->description = !empty($data['MESSAGE']) ? $data['MESSAGE'] : '';
        $this->status = !empty($data['EXECCODE']) ? $data['EXECCODE'] : null;

        if ($data['EXECCODE'] != "0000") {
            $this->failure = array(
                'code' => !empty($data['EXECCODE']) ? $data['EXECCODE'] : null,
                'message' => !empty($data['MESSAGE']) ? $data['MESSAGE'] : null,
                'details' => !empty($data['DETAILS']) ? $data['DETAILS'] : null,
            );
        }
    }
}
