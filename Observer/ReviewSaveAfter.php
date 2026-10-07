<?php

declare(strict_types=1);

namespace Magic\WebhookConnector\Observer;

use Magic\WebhookConnector\Model\WebhookPublisher;
use Magic\WebhookConnector\Model\ReviewWebhookSuppressor;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Store\Model\StoreManagerInterface;

class ReviewSaveAfter implements ObserverInterface
{
    public function __construct(
        private readonly WebhookPublisher $webhookPublisher,
        private readonly StoreManagerInterface $storeManager,
        private readonly ReviewWebhookSuppressor $webhookSuppressor
    ) {
    }

    public function execute(Observer $observer): void
    {
        if ($this->webhookSuppressor->isSuppressed()) {
            return;
        }
        $review = $observer->getEvent()->getObject()
            ?: $observer->getEvent()->getReview();
        if (!$review || !$review->getId()) {
            return;
        }

        $storeId = $this->resolveStoreId($review);
        $eventType = $review->getOrigData('review_id')
            ? 'review.updated'
            : 'review.created';
        $payload = [
            'event_type' => $eventType,
            'store_id' => $storeId,
            'store_url' => $this->resolveStoreUrl($storeId),
            'review_id' => (int)$review->getId(),
            'product_id' => (int)$review->getEntityPkValue(),
            'timestamp' => gmdate('c'),
        ];

        $this->webhookPublisher->publish($eventType, $payload, $storeId);
    }

    private function resolveStoreId(object $review): int
    {
        $stores = $review->getStores();
        if (is_array($stores) && $stores !== []) {
            return (int)reset($stores);
        }

        return (int)$this->storeManager->getStore()->getId();
    }

    private function resolveStoreUrl(int $storeId): string
    {
        try {
            return rtrim((string)$this->storeManager->getStore($storeId)->getBaseUrl(), '/');
        } catch (\Throwable) {
            return '';
        }
    }
}
