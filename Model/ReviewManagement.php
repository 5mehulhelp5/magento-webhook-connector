<?php

declare(strict_types=1);

namespace Magic\WebhookConnector\Model;

use Magic\WebhookConnector\Api\Data\ReviewInterface;
use Magic\WebhookConnector\Api\Data\ReviewInterfaceFactory;
use Magic\WebhookConnector\Api\ReviewManagementInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Review\Model\ResourceModel\Review\CollectionFactory;
use Magento\Review\Model\Review;
use Magento\Review\Model\ReviewFactory;
use Magento\Store\Model\StoreManagerInterface;

class ReviewManagement implements ReviewManagementInterface
{
    public function __construct(
        private readonly CollectionFactory $reviewCollectionFactory,
        private readonly ReviewFactory $reviewFactory,
        private readonly ReviewInterfaceFactory $reviewDataFactory,
        private readonly ProductRepositoryInterface $productRepository,
        private readonly CustomerRepositoryInterface $customerRepository,
        private readonly StoreManagerInterface $storeManager
    ) {
    }

    public function getList(int $page = 1, int $pageSize = 100): array
    {
        $page = max(1, $page);
        $pageSize = min(250, max(1, $pageSize));
        $collection = $this->reviewCollectionFactory->create();
        $collection->setOrder('review_id', 'ASC');
        $collection->setCurPage($page);
        $collection->setPageSize($pageSize);

        $ids = [];
        foreach ($collection as $review) {
            $ids[] = (int)$review->getId();
        }

        return $ids;
    }

    public function getById(int $reviewId): ReviewInterface
    {
        /** @var Review $review */
        $review = $this->reviewFactory->create()->load($reviewId);
        if (!$review->getId()) {
            throw new NoSuchEntityException(__('Review with ID "%1" does not exist.', $reviewId));
        }

        $productId = (int)$review->getEntityPkValue();
        try {
            $product = $this->productRepository->getById($productId);
        } catch (NoSuchEntityException) {
            throw new NoSuchEntityException(
                __('Product for review ID "%1" does not exist.', $reviewId)
            );
        }

        $customerEmail = null;
        $customerId = $review->getCustomerId();
        if ($customerId) {
            try {
                $customerEmail = $this->customerRepository
                    ->getById((int)$customerId)
                    ->getEmail();
            } catch (NoSuchEntityException) {
                $customerEmail = null;
            }
        }

        $ratings = [];
        foreach ($review->getRatingVotes() as $vote) {
            $ratings[] = (int)$vote->getValue();
        }

        $storeId = $this->resolveStoreId($review);

        return $this->reviewDataFactory->create()
            ->setId((int)$review->getId())
            ->setProductId($productId)
            ->setProductSku((string)$product->getSku())
            ->setProductName((string)$product->getName())
            ->setCustomerId($customerId ? (int)$customerId : null)
            ->setCustomerEmail($customerEmail)
            ->setCustomerName((string)$review->getNickname())
            ->setRatings($ratings)
            ->setTitle((string)$review->getTitle())
            ->setBody((string)$review->getDetail())
            ->setStatusId((int)$review->getStatusId())
            ->setStoreId($storeId)
            ->setStoreUrl(rtrim(
                (string)$this->storeManager->getStore($storeId)->getBaseUrl(),
                '/'
            ))
            ->setCreatedAt((string)$review->getCreatedAt())
            ->setUpdatedAt(
                (string)($review->getData('updated_at') ?: $review->getCreatedAt())
            );
    }

    private function resolveStoreId(Review $review): int
    {
        $stores = $review->getStores();
        if (is_array($stores) && $stores !== []) {
            return (int)reset($stores);
        }

        $storeId = $review->getData('store_id');
        if (is_array($storeId)) {
            $storeId = reset($storeId);
        }

        return $storeId !== null
            ? (int)$storeId
            : (int)$this->storeManager->getStore()->getId();
    }
}
