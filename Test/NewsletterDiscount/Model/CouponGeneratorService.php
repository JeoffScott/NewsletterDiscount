<?php

declare(strict_types=1);

namespace Test\NewsletterDiscount\Model;

use Magento\SalesRule\Helper\Coupon;
use Magento\SalesRule\Model\CouponGenerator;

class CouponGeneratorService
{
    /**#@+
     * Coupon Code Length
     */
    public const LENGTH = 10;
    /**#@-*/

    /**
     * @var CouponGenerator
     */
    protected CouponGenerator $couponGenerator;

    /**
     * GenerateCouponCodesService constructor
     * @param CouponGenerator $couponGenerator
     */
    public function __construct(CouponGenerator $couponGenerator)
    {
        $this->couponGenerator = $couponGenerator;
    }

    /**
     * Generate coupon for specified cart price rule
     *
     * @param int $ruleId
     * @return array
     */
    public function execute(int $ruleId): array
    {
        return $this->couponGenerator->generateCodes(
            [
                'rule_id' => $ruleId,
                'qty' => 1,
                'length' => self::LENGTH,
                'format' => Coupon::COUPON_FORMAT_ALPHANUMERIC
            ]
        );
    }
}
