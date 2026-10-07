<?php

declare(strict_types=1);

namespace Magic\WebhookConnector\Model\Data;

use Magic\WebhookConnector\Api\Data\ReviewInputInterface;
use Magento\Framework\DataObject;

class ReviewInput extends DataObject implements ReviewInputInterface
{
    public function getProductSku(): ?string
    {
        $value = $this->getData('product_sku');
        return $value !== null ? (string)$value : null;
    }

    public function setProductSku(?string $productSku): ReviewInputInterface
    {
        $this->setData('product_sku', $productSku);
        return $this;
    }

    public function getCustomerName(): ?string
    {
        $value = $this->getData('customer_name');
        return $value !== null ? (string)$value : null;
    }

    public function setCustomerName(?string $customerName): ReviewInputInterface
    {
        $this->setData('customer_name', $customerName);
        return $this;
    }

    public function getTitle(): ?string
    {
        $value = $this->getData('title');
        return $value !== null ? (string)$value : null;
    }

    public function setTitle(?string $title): ReviewInputInterface
    {
        $this->setData('title', $title);
        return $this;
    }

    public function getBody(): ?string
    {
        $value = $this->getData('body');
        return $value !== null ? (string)$value : null;
    }

    public function setBody(?string $body): ReviewInputInterface
    {
        $this->setData('body', $body);
        return $this;
    }

    public function getRating(): ?int
    {
        $value = $this->getData('rating');
        return $value !== null ? (int)$value : null;
    }

    public function setRating(?int $rating): ReviewInputInterface
    {
        $this->setData('rating', $rating);
        return $this;
    }

    public function getStatus(): ?string
    {
        $value = $this->getData('status');
        return $value !== null ? (string)$value : null;
    }

    public function setStatus(?string $status): ReviewInputInterface
    {
        $this->setData('status', $status);
        return $this;
    }

    public function getStoreId(): ?int
    {
        $value = $this->getData('store_id');
        return $value !== null ? (int)$value : null;
    }

    public function setStoreId(?int $storeId): ReviewInputInterface
    {
        $this->setData('store_id', $storeId);
        return $this;
    }
}
