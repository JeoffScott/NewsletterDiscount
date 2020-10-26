<?php

declare(strict_types=1);

namespace Test\NewsletterDiscount\Plugin;

use Magento\Framework\App\Area;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Newsletter\Model\Subscriber;
use Psr\Log\LoggerInterface;
use Test\NewsletterDiscount\Model\Config;
use Test\NewsletterDiscount\Model\CouponGeneratorService;

class SubscriberPlugin
{
    /**
     * @var Config
     */
    private Config $config;

    /**
     * @var TransportBuilder
     */
    private TransportBuilder $transportBuilder;

    /**
     * @var StateInterface
     */
    private StateInterface $inlineTranslation;

    /**
     * @var CouponGeneratorService
     */
    private CouponGeneratorService $couponGenerator;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * SubscriberPlugin constructor.
     * @param Config $config
     * @param TransportBuilder $transportBuilder
     * @param StateInterface $inlineTranslation
     * @param CouponGeneratorService $couponGenerator
     * @param LoggerInterface $logger
     */
    public function __construct(
        Config $config,
        TransportBuilder $transportBuilder,
        StateInterface $inlineTranslation,
        CouponGeneratorService $couponGenerator,
        LoggerInterface $logger
    ) {
        $this->config = $config;
        $this->transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
        $this->couponGenerator = $couponGenerator;
        $this->logger = $logger;
    }

    /**
     * @param Subscriber $subject
     * @param callable $proceed
     * @return mixed
     */
    public function aroundSendConfirmationSuccessEmail(Subscriber $subject, callable $proceed)
    {
        if (!$this->config->isEnabled() || $subject->getData('coupon_sent')) {
            return $proceed();
        }

        try {
            $this->sendCouponEmail($subject);
        } catch (LocalizedException $e) {
            $this->logger->error($e->getMessage());
            return $proceed();
        }

    }

    /**
     * @param Subscriber $subscriber
     * @throws LocalizedException
     * @return void
     */
    public function sendCouponEmail(Subscriber $subscriber): void
    {
        if ($subscriber->getImportMode()) {
            return;
        }

        $template = $this->config->getCouponEmailTemplate($subscriber->getStoreId());
        $identity = $this->config->getCouponEmailIdentity($subscriber->getStoreId());
        $ruleId = $this->config->getRuleId($subscriber->getStoreId());
        if (!$template || !$identity || !$ruleId) {
            throw new LocalizedException(__('Newsletter Discount configuration is not set correctly.'));
        }

        $templateVars = [
            'subscriber' => $subscriber,
            'coupon' => current($this->couponGenerator->execute($ruleId))
        ];
        $this->inlineTranslation->suspend();
        $this->transportBuilder->setTemplateIdentifier(
            $template
        )->setTemplateOptions(
            [
                'area' => Area::AREA_FRONTEND,
                'store' => $subscriber->getStoreId(),
            ]
        )->setTemplateVars(
            $templateVars
        )->setFrom(
            $identity
        )->addTo(
            $subscriber->getEmail(),
            $subscriber->getName()
        );
        $transport = $this->transportBuilder->getTransport();
        $transport->sendMessage();

        $this->inlineTranslation->resume();

        $subscriber->setData('coupon_sent', 1)->save();
    }
}
