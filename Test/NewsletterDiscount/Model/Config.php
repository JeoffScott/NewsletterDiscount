<?php

declare(strict_types=1);

namespace Test\NewsletterDiscount\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Config
{
    /**#@+
     * Configuration paths constants
     */
    public const XML_PATH_IS_ENABLED = 'newsletter_discount/general/enabled';
    public const XML_PATH_COUPON_TEMPLATE = 'newsletter_discount/general/success_coupon_email_template';
    public const XML_PATH_COUPON_IDENTITY = 'newsletter_discount/general/coupon_email_identity';
    public const XML_PATH_RULE_ID = 'newsletter_discount/general/rule_id';
    /**#@-*/

    /**
     * @var ScopeConfigInterface
     */
    private ScopeConfigInterface $config;

    /**
     * @param ScopeConfigInterface $config
     */
    public function __construct(ScopeConfigInterface $config)
    {
        $this->config = $config;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->config->isSetFlag(self::XML_PATH_IS_ENABLED, ScopeInterface::SCOPE_WEBSITE);
    }

    /**
     * @param int $storeId
     * @return string
     */
    public function getCouponEmailTemplate(int $storeId): string
    {
        return $this->config->getValue(self::XML_PATH_COUPON_TEMPLATE, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return string
     */
    public function getCouponEmailIdentity(int $storeId): string
    {
        return $this->config->getValue(self::XML_PATH_COUPON_IDENTITY, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return int
     */
    public function getRuleId(int $storeId): int
    {
        return (int)$this->config->getValue(self::XML_PATH_RULE_ID, ScopeInterface::SCOPE_STORE, $storeId);
    }
}
