<?php

declare(strict_types=1);

namespace Magic\WebhookConnector\Api;

use Magic\WebhookConnector\Api\Data\ReviewInterface;

interface ReviewManagementInterface
{
    /**
     * Return product reviews in a stable, paginated envelope.
     *
     * @param int $page
     * @param int $pageSize
     * @return int[]
     */
    public function getList(int $page = 1, int $pageSize = 100): array;

    /**
     * Return one product review.
     *
     * @param int $reviewId
     * @return \Magic\WebhookConnector\Api\Data\ReviewInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById(int $reviewId): ReviewInterface;
}
