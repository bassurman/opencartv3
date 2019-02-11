<?php
class Helperbm {

    const SESSION_HASH_CODE = 'billmate_checkout_hash';

    const CART_ID_SEPARATOR = '-';

    /**
     * @var array
     */
    protected $mapperPaymentMethods = [
        1 => 'Invoice',
        2 => 'Invoiceservice',
        4 => 'Partpay',
        8 => 'Cardpay',
        16 => 'Bankpay'
    ];

    /**
     * HelperBillmate constructor.
     *
     * @param $registry
     */
    public function __construct($registry) {
        $this->config = $registry->get('config');
        $this->session = $registry->get('session');
        $this->cart = $registry->get('cart');
    }

    /**
     * @return Billmate
     */
    public function getBillmateConnection() {
        $id = $this->getBillmateId();
        $secret = $this->getBillmateSecret();
        $isTestMode = $this->isChekcoutTestMode();
        $billmateConnection = new Billmate($id, $secret, true, $isTestMode);
        return $billmateConnection;
    }

    /**
     * @return int
     */
    public function getBillmateId() {
        return $this->config->get('module_billmate_checkout_bm_id');
    }

    /**
     * @return string
     */
    public function getBillmateSecret() {
        return $this->config->get('module_billmate_checkout_secret');
    }

    /**
     * @return bool
     */
    public function isChekcoutTestMode() {
        return (bool)$this->config->get('module_billmate_checkout_test_mode');
    }

    /**
     * @return bool
     */
    public function isBmCheckoutEnabled() {
        return  (bool)$this->config->get('module_billmate_checkout_status');
    }

    /**
     * @return bool
     */
    public function getNewOrderStatusId() {
        return  $this->config->get('module_billmate_checkout_order_status_id');
    }

    /**
     * @param $hash string
     */
    public function setSessionBmHash($hash) {
        $this->session->data[self::SESSION_HASH_CODE] = $hash;
    }

    /**
     * @return string
     */
    public function getSessionBmHash() {
        if (isset($this->session->data[self::SESSION_HASH_CODE])) {
            return $this->session->data[self::SESSION_HASH_CODE];
        }
        return '';
    }

    /**
     * @param $code
     *
     * @return string
     */
    public function getPaymentMethodByCode($code) {
        if (isset($this->mapperPaymentMethods[$code])) {
            return $this->mapperPaymentMethods[$code];
        }
        return '';
    }

    public function resetSessionBmHash() {
        if (isset($this->session->data[self::SESSION_HASH_CODE])) {
            unset($this->session->data[self::SESSION_HASH_CODE]);
        }
    }

    public function log($data) {
        $log = new Log('billmate_checkout.log');
        $log->write($data);
    }

    /**
     * @param $cartIdRow
     *
     * @return int
     */
    public function getCartId($cartIdRow) {
        return $cartIdRow;
    }

    /**
     * @param string $url
     *
     * @return string
     */
    public function getHashFromUrl($url = '') {
        $parts = explode('/',$url);
        $sum = count($parts);
        $hash = ($parts[$sum-1] == 'test')
            ? str_replace('\\','',$parts[$sum-2])
            : str_replace('\\','',$parts[$sum-1]);
        return $hash;
    }

    /**
     * @return string
     */
    public function getLogoName() {
        return '';
    }

    /**
     * @return bool
     */
    public function isAddLog() {
        return $this->config->get('module_billmate_checkout_log_enabled');
    }

    /**
     * @return bool
     */
    public function unsetCart() {
        $this->cart->clear();

        unset($this->session->data['shipping_method']);
        unset($this->session->data['shipping_methods']);
        unset($this->session->data['payment_method']);
        unset($this->session->data['payment_methods']);
        unset($this->session->data['guest']);
        unset($this->session->data['comment']);
        unset($this->session->data['order_id']);
        unset($this->session->data['coupon']);
        unset($this->session->data['reward']);
        unset($this->session->data['voucher']);
        unset($this->session->data['vouchers']);
        unset($this->session->data['totals']);
    }
}