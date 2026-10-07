<?php

declare(strict_types=1);

namespace Magic\WebhookConnector\Api\Data;

interface ReviewInputInterface
{
    /** @return string|null */
    public function getProductSku(): ?string;

    /**
     * @param string|null $productSku
     * @return \Magic\WebhookConnector\Api\Data\ReviewInputInterface
     */
    public function setProductSku(?string $productSku): ReviewInputInterface;

    /** @return string|null */
    public function getCustomerName(): ?string;

    /**
     * @param string|null $customerName
     * @return \Magic\WebhookConnector\Api\Data\ReviewInputInterface
     */
    public function setCustomerName(?string $customerName): ReviewInputInterface;

    /** @return string|null */
    public function getTitle(): ?string;

    /**
     * @param string|null $title
     * @return \Magic\WebhookConnector\Api\Data\ReviewInputInterface
     */
    public function setTitle(?string $title): ReviewInputInterface;

    /** @return string|null */
    public function getBody(): ?string;

    /**
     * @param string|null $body
     * @return \Magic\WebhookConnector\Api\Data\ReviewInputInterface
     */
    public function setBody(?string $body): ReviewInputInterface;

    /** @return int|null */
    public function getRating(): ?int;

    /**
     * @param int|null $rating
     * @return \Magic\WebhookConnector\Api\Data\ReviewInputInterface
     */
    public function setRating(?int $rating): ReviewInputInterface;

    /** @return string|null */
    public function getStatus(): ?string;

    /**
     * @param string|null $status
     * @return \Magic\WebhookConnector\Api\Data\ReviewInputInterface
     */
    public function setStatus(?string $status): ReviewInputInterface;

    /** @return int|null */
    public function getStoreId(): ?int;

    /**
     * @param int|null $storeId
     * @return \Magic\WebhookConnector\Api\Data\ReviewInputInterface
     */
    public function setStoreId(?int $storeId): ReviewInputInterface;
}
