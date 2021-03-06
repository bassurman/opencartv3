<?php
require_once(DIR_APPLICATION . 'model/billmate/service/processor/status.php');

class ModelBillmateServiceProcessorCancel extends ModelBillmateServiceProcessorStatus
{
    public function process($orderId)
    {
        $requestData = $this->getRequestData($orderId);
        if (!$requestData) {
            return;
        }

        $billmateConnection = $this->getBMConnection();
        $paymentData = $billmateConnection->getPaymentInfo($requestData);

        $bmRequestData = [
            'PaymentData' => $requestData
        ];

        if ($paymentData['PaymentData']['status'] == self::BILLMATE_CREATED_STATUS) {
           $this->cancelPayment($bmRequestData);
        }

        if ($paymentData['PaymentData']['status'] == self::BILLMATE_PAID_STATUS) {
            $this->creditPayment($bmRequestData);
        }
    }
}