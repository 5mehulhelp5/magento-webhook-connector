<?php

declare(strict_types=1);

namespace Magic\WebhookConnector\Api;

use Magic\WebhookConnector\Api\Data\ReviewInputInterface;
use Magic\WebhookConnector\Api\Data\ReviewInterface;

interface ReviewWriteManagementInterface
{
    /**
     * @param \Magic\WebhookConnector\Api\Data\ReviewInputInterface $review
     * @return \Magic\WebhookConnector\Api\Data\ReviewInterface
     */
    public function create(ReviewInputInterface $review): ReviewInterface;

    /**
     * @param int $reviewId
     * @param \Magic\WebhookConnector\Api\Data\ReviewInputInterface $review
     * @return \Magic\WebhookConnector\Api\Data\ReviewInterface
     */
    public function update(
        int $reviewId,
        ReviewInputInterface $review
    ): ReviewInterface;

    /**
     * @param int $reviewId
     * @return bool
     */
    public function delete(int $reviewId): bool;
}
