<?php

declare(strict_types=1);

namespace Magic\WebhookConnector\Model;

use Magic\WebhookConnector\Api\Data\ReviewInputInterface;
use Magic\WebhookConnector\Api\Data\ReviewInterface;
use Magic\WebhookConnector\Api\ReviewManagementInterface;
use Magic\WebhookConnector\Api\ReviewWriteManagementInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Review\Model\Rating;
use Magento\Review\Model\ResourceModel\Rating\CollectionFactory as RatingCollectionFactory;
use Magento\Review\Model\ResourceModel\Rating\Option\Vote\CollectionFactory as VoteCollectionFactory;
use Magento\Review\Model\Review;
use Magento\Review\Model\ReviewFactory;
use Magento\Store\Model\StoreManagerInterface;

class ReviewWriteManagement implements ReviewWriteManagementInterface
{
    public function __construct(
        private readonly ReviewFactory $reviewFactory,
        private readonly ReviewManagementInterface $reviewManagement,
        private readonly ProductRepositoryInterface $productRepository,
        private readonly RatingCollectionFactory $ratingCollectionFactory,
        private readonly VoteCollectionFactory $voteCollectionFactory,
        private readonly StoreManagerInterface $storeManager,
        private readonly ReviewWebhookSuppressor $webhookSuppressor
    ) {
    }

    /**
     * @param \Magic\WebhookConnector\Api\Data\ReviewInputInterface $review
     * @return \Magic\WebhookConnector\Api\Data\ReviewInterface
     */
    public function create(ReviewInputInterface $review): ReviewInterface
    {
        $sku = trim((string)$review->getProductSku());
        $nickname = trim((string)$review->getCustomerName());
        $body = trim((string)$review->getBody());
        $title = trim((string)$review->getTitle());
        $rating = $review->getRating();
        if ($sku === '' || $nickname === '' || $body === '') {
            throw new InputException(
                __('product_sku, customer_name, and body are required.')
            );
        }
        $this->validateRating($rating);
        if ($title === '') {
            $title = mb_substr($body, 0, 80);
        }

        $storeId = $this->resolveStoreId($review->getStoreId());
        $product = $this->productRepository->get($sku, false, $storeId);
        /** @var Review $reviewModel */
        $reviewModel = $this->reviewFactory->create();
        $reviewModel->setData([
            'nickname' => $nickname,
            'title' => $title,
            'detail' => $body,
        ]);
        $reviewModel
            ->setEntityId(
                $reviewModel->getEntityIdByCode(Review::ENTITY_PRODUCT_CODE)
            )
            ->setEntityPkValue((int)$product->getId())
            ->setStatusId($this->statusId($review->getStatus()))
            ->setCustomerId(null)
            ->setStoreId($storeId)
            ->setStores([$storeId]);

        $this->webhookSuppressor->run(function () use (
            $reviewModel,
            $rating,
            $storeId
        ): void {
            $reviewModel->save();
            $this->replaceRatingVotes(
                $reviewModel,
                (int)$rating,
                $storeId
            );
            $reviewModel->aggregate();
        });

        return $this->reviewManagement->getById((int)$reviewModel->getId());
    }

    /**
     * @param int $reviewId
     * @param \Magic\WebhookConnector\Api\Data\ReviewInputInterface $review
     * @return \Magic\WebhookConnector\Api\Data\ReviewInterface
     */
    public function update(
        int $reviewId,
        ReviewInputInterface $review
    ): ReviewInterface {
        $reviewModel = $this->loadReview($reviewId);
        $sku = trim((string)$review->getProductSku());
        if ($sku !== '') {
            $product = $this->productRepository->get($sku);
            if ((int)$product->getId() !== (int)$reviewModel->getEntityPkValue()) {
                throw new InputException(__('A review product cannot be changed.'));
            }
        }

        $changed = false;
        foreach (
            [
                'customer_name' => 'nickname',
                'title' => 'title',
                'body' => 'detail',
            ] as $inputField => $reviewField
        ) {
            $getter = 'get' . str_replace(
                ' ',
                '',
                ucwords(str_replace('_', ' ', $inputField))
            );
            $value = $review->{$getter}();
            if ($value === null) {
                continue;
            }
            $value = trim($value);
            if ($value === '') {
                throw new InputException(__('%1 cannot be empty.', $inputField));
            }
            $reviewModel->setData($reviewField, $value);
            $changed = true;
        }

        if ($review->getStatus() !== null) {
            $reviewModel->setStatusId($this->statusId($review->getStatus()));
            $changed = true;
        }
        $rating = $review->getRating();
        if ($rating !== null) {
            $this->validateRating($rating);
            $changed = true;
        }
        if (!$changed) {
            throw new InputException(__('At least one review field is required.'));
        }

        $storeId = $this->resolveReviewStoreId($reviewModel);
        $this->webhookSuppressor->run(function () use (
            $reviewModel,
            $rating,
            $storeId
        ): void {
            $reviewModel->save();
            if ($rating !== null) {
                $this->replaceRatingVotes($reviewModel, $rating, $storeId);
            }
            $reviewModel->aggregate();
        });

        return $this->reviewManagement->getById($reviewId);
    }

    /**
     * @param int $reviewId
     * @return bool
     */
    public function delete(int $reviewId): bool
    {
        $review = $this->loadReview($reviewId);
        $this->webhookSuppressor->run(
            static function () use ($review): void {
                $review->delete();
            }
        );
        return true;
    }

    private function loadReview(int $reviewId): Review
    {
        /** @var Review $review */
        $review = $this->reviewFactory->create()->load($reviewId);
        if (!$review->getId()) {
            throw new NoSuchEntityException(
                __('Review with ID "%1" does not exist.', $reviewId)
            );
        }
        return $review;
    }

    private function validateRating(?int $rating): void
    {
        if ($rating === null || $rating < 1 || $rating > 5) {
            throw new InputException(__('rating must be between 1 and 5.'));
        }
    }

    private function statusId(?string $status): int
    {
        return match (strtolower(trim((string)$status))) {
            'approved' => Review::STATUS_APPROVED,
            'rejected' => Review::STATUS_NOT_APPROVED,
            'pending', 'flagged', '' => Review::STATUS_PENDING,
            default => throw new InputException(__('Invalid review status.')),
        };
    }

    private function resolveStoreId(?int $storeId): int
    {
        if ($storeId !== null && $storeId > 0) {
            $this->storeManager->getStore($storeId);
            return $storeId;
        }
        $defaultStore = $this->storeManager->getDefaultStoreView();
        if ($defaultStore === null) {
            throw new InputException(__('Magento has no default store view.'));
        }
        return (int)$defaultStore->getId();
    }

    private function resolveReviewStoreId(Review $review): int
    {
        $stores = $review->getStores();
        if (is_array($stores) && $stores !== []) {
            return (int)reset($stores);
        }
        return $this->resolveStoreId(null);
    }

    private function replaceRatingVotes(
        Review $review,
        int $stars,
        int $storeId
    ): void {
        $ratings = $this->ratingCollectionFactory
            ->create()
            ->addEntityFilter(Rating::ENTITY_PRODUCT_CODE)
            ->setStoreFilter($storeId)
            ->addOptionToItems();
        $selections = [];
        foreach ($ratings as $rating) {
            foreach ($rating->getOptions() as $option) {
                if ((int)$option->getValue() === $stars) {
                    $selections[] = [$rating, (int)$option->getId()];
                    break;
                }
            }
        }
        if ($selections === []) {
            throw new InputException(
                __('No active product rating options are configured for this store.')
            );
        }

        $existingVotes = $this->voteCollectionFactory
            ->create()
            ->setReviewFilter((int)$review->getId());
        foreach ($existingVotes as $vote) {
            $vote->delete();
        }
        foreach ($selections as [$rating, $optionId]) {
            $rating
                ->setReviewId((int)$review->getId())
                ->setCustomerId(null)
                ->addOptionVote(
                    $optionId,
                    (int)$review->getEntityPkValue()
                );
        }
    }
}
