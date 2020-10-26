<?php

declare(strict_types=1);

namespace Test\NewsletterDiscount\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory;

class CartRule implements OptionSourceInterface
{
    /**
     * @var CollectionFactory
     */
    protected CollectionFactory $ruleCollectionFactory;

    /**
     * CartRule constructor.
     * @param CollectionFactory $ruleCollectionFactory
     */
    public function __construct(
        CollectionFactory $ruleCollectionFactory
    ) {
        $this->ruleCollectionFactory = $ruleCollectionFactory;
    }

    /**
     * @return array
     */
    public function toOptionArray(): array
    {
        $rules = $this->ruleCollectionFactory->create()
            ->addFieldToSelect(['rule_id', 'name'])
            ->addFieldToFilter('coupon_type', 2)
            ->addFieldToFilter('use_auto_generation', 1)
            ->addFieldToFilter('is_active', 1)
            ->load();

        $data[] = [
            'value' => '',
            'label' => __('Please select')
        ];

        foreach ($rules as $key => $rule) {
            $data[$key] = [
                'value' => $rule->getRuleId(),
                'label' => $rule->getName()
            ];
        }

        return $data;
    }
}
