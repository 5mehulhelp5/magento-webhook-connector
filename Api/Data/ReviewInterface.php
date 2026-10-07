<?php

declare(strict_types=1);

namespace Magic\WebhookConnector\Api\Data;

interface ReviewInterface
{
    /** @return int */
    public function getId(): int;
    /** @param int $id @return \Magic\WebhookConnector\Api\Data\ReviewInterface */
    public function setId(int $id): ReviewInterface;
    /** @return int */
    public function getProductId(): int;
    /** @param int $productId @return \Magic\WebhookConnector\Api\Data\ReviewInterface */
    public function setProductId(int $productId): ReviewInterface;
    /** @return string */
    public function getProductSku(): string;
    /** @param string $productSku @return \Magic\WebhookConnector\Api\Data\ReviewInterface */
    public function setProductSku(string $productSku): ReviewInterface;
    /** @return string */
    public function getProductName(): string;
    /** @param string $productName @return \Magic\WebhookConnector\Api\Data\ReviewInterface */
    public function setProductName(string $productName): ReviewInterface;
    /** @return int|null */
    public function getCustomerId(): ?int;
    /** @param int|null $customerId @return \Magic\WebhookConnector\Api\Data\ReviewInterface */
    public function setCustomerId(?int $customerId): ReviewInterface;
    /** @return string|null */
    public function getCustomerEmail(): ?string;
    /** @param string|null $customerEmail @return \Magic\WebhookConnector\Api\Data\ReviewInterface */
    public function setCustomerEmail(?string $customerEmail): ReviewInterface;
    /** @return string */
    public function getCustomerName(): string;
    /** @param string $customerName @return \Magic\WebhookConnector\Api\Data\ReviewInterface */
    public function setCustomerName(string $customerName): ReviewInterface;

    /**
     * @return int[]
     */
    public function getRatings(): array;

    /**
     * @param int[] $ratings
     */
    public function setRatings(array $ratings): ReviewInterface;
    /** @return string */
    public function getTitle(): string;
    /** @param string $title @return \Magic\WebhookConnector\Api\Data\ReviewInterface */
    public function setTitle(string $title): ReviewInterface;
    /** @return string */
    public function getBody(): string;
    /** @param string $body @return \Magic\WebhookConnector\Api\Data\ReviewInterface */
    public function setBody(string $body): ReviewInterface;
    /** @return int */
    public function getStatusId(): int;
    /** @param int $statusId @return \Magic\WebhookConnector\Api\Data\ReviewInterface */
    public function setStatusId(int $statusId): ReviewInterface;
    /** @return int */
    public function getStoreId(): int;
    /** @param int $storeId @return \Magic\WebhookConnector\Api\Data\ReviewInterface */
    public function setStoreId(int $storeId): ReviewInterface;
    /** @return string */
    public function getStoreUrl(): string;
    /** @param string $storeUrl @return \Magic\WebhookConnector\Api\Data\ReviewInterface */
    public function setStoreUrl(string $storeUrl): ReviewInterface;
    /** @return string */
    public function getCreatedAt(): string;
    /** @param string $createdAt @return \Magic\WebhookConnector\Api\Data\ReviewInterface */
    public function setCreatedAt(string $createdAt): ReviewInterface;
    /** @return string */
    public function getUpdatedAt(): string;
    /** @param string $updatedAt @return \Magic\WebhookConnector\Api\Data\ReviewInterface */
    public function setUpdatedAt(string $updatedAt): ReviewInterface;
}
