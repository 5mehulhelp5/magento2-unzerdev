<?php

namespace Heidelpay\Gateway2\Model;

use heidelpayPHP\Heidelpay;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class Config
{
    const KEY_PUBLIC_KEY = 'public_key';
    const KEY_PRIVATE_KEY = 'private_key';
    const KEY_WEBHOOKS_SOURCE_IPS = 'webhooks_source_ips';

    const METHOD_BASE = 'hpg2';
    const METHOD_CREDITCARD = 'hpg2_creditcard';
    const METHOD_DIRECT_DEBIT = 'hpg2_direct_debit';
    const METHOD_DIRECT_DEBIT_GUARANTEED = 'hpg2_direct_debit_guaranteed';
    const METHOD_FLEXIPAY_DIRECT = 'hpg2_flexipay_direct';
    const METHOD_IDEAL = 'hpg2_ideal';
    const METHOD_INVOICE = 'hpg2_invoice';
    const METHOD_INVOICE_GUARANTEED = 'hpg2_invoice_guaranteed';
    const METHOD_PAYPAL = 'hpg2_paypal';
    const METHOD_SOFORT = 'hpg2_sofort';

    /**
     * @var ScopeConfigInterface
     */
    private $_scopeConfig;

    /**
     * @var StoreManagerInterface
     */
    private $_storeManager;

    /**
     * Module constructor.
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager
    )
    {
        $this->_scopeConfig = $scopeConfig;
        $this->_storeManager = $storeManager;
    }

    /**
     * Returns the public key.
     *
     * @return string
     */
    public function getPublicKey()
    {
        return $this->getValue(self::KEY_PUBLIC_KEY);
    }

    /**
     * Retrieve information from payment configuration
     *
     * @param string $field
     * @param int|null $storeId
     *
     * @return mixed
     */
    protected function getValue($field, $storeId = null)
    {
        return $this->_scopeConfig->getValue('payment/hpg2/' . $field, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * Returns the private key.
     *
     * @return string
     */
    public function getPrivateKey()
    {
        return $this->getValue(self::KEY_PRIVATE_KEY);
    }

    /**
     * Returns the list of valid source IPs for webhook events.
     *
     * @return string[]
     */
    public function getWebhooksSourceIps()
    {
        return preg_split('/\s*,\s*/', $this->getValue(self::KEY_WEBHOOKS_SOURCE_IPS));
    }

    /**
     * Returns an API client using the configured private key.
     *
     * @param null $locale
     * @return Heidelpay
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getHeidelpayClient($locale = null)
    {
        if ($locale === null) {
            $locale = $this->_storeManager->getStore()->getLocaleCode();
        }

        return new Heidelpay($this->getPrivateKey(), $locale);
    }
}
