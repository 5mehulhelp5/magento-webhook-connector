<?php

declare(strict_types=1);

namespace Magic\WebhookConnector\Model\Data;

use Magic\WebhookConnector\Api\Data\ReviewInterface;

class Review implements ReviewInterface
{
    private int $id = 0;
    private int $productId = 0;
    private string $productSku = '';
    private string $productName = '';
    private ?int $customerId = null;
    private ?string $customerEmail = null;
    private string $customerName = '';
    /** @var int[] */
    private array $ratings = [];
    private string $title = '';
    private string $body = '';
    private int $statusId = 0;
    private int $storeId = 0;
    private string $storeUrl = '';
    private string $createdAt = '';
    private string $updatedAt = '';

    public function getId(): int { return $this->id; }
    public function setId(int $id): ReviewInterface { $this->id = $id; return $this; }
    public function getProductId(): int { return $this->productId; }
    public function setProductId(int $productId): ReviewInterface { $this->productId = $productId; return $this; }
    public function getProductSku(): string { return $this->productSku; }
    public function setProductSku(string $productSku): ReviewInterface { $this->productSku = $productSku; return $this; }
    public function getProductName(): string { return $this->productName; }
    public function setProductName(string $productName): ReviewInterface { $this->productName = $productName; return $this; }
    public function getCustomerId(): ?int { return $this->customerId; }
    public function setCustomerId(?int $customerId): ReviewInterface { $this->customerId = $customerId; return $this; }
    public function getCustomerEmail(): ?string { return $this->customerEmail; }
    public function setCustomerEmail(?string $customerEmail): ReviewInterface { $this->customerEmail = $customerEmail; return $this; }
    public function getCustomerName(): string { return $this->customerName; }
    public function setCustomerName(string $customerName): ReviewInterface { $this->customerName = $customerName; return $this; }
    public function getRatings(): array { return $this->ratings; }
    public function setRatings(array $ratings): ReviewInterface { $this->ratings = $ratings; return $this; }
    public function getTitle(): string { return $this->title; }
    public function setTitle(string $title): ReviewInterface { $this->title = $title; return $this; }
    public function getBody(): string { return $this->body; }
    public function setBody(string $body): ReviewInterface { $this->body = $body; return $this; }
    public function getStatusId(): int { return $this->statusId; }
    public function setStatusId(int $statusId): ReviewInterface { $this->statusId = $statusId; return $this; }
    public function getStoreId(): int { return $this->storeId; }
    public function setStoreId(int $storeId): ReviewInterface { $this->storeId = $storeId; return $this; }
    public function getStoreUrl(): string { return $this->storeUrl; }
    public function setStoreUrl(string $storeUrl): ReviewInterface { $this->storeUrl = $storeUrl; return $this; }
    public function getCreatedAt(): string { return $this->createdAt; }
    public function setCreatedAt(string $createdAt): ReviewInterface { $this->createdAt = $createdAt; return $this; }
    public function getUpdatedAt(): string { return $this->updatedAt; }
    public function setUpdatedAt(string $updatedAt): ReviewInterface { $this->updatedAt = $updatedAt; return $this; }
}
