<?php

namespace Walish\RabbitMCute\Plugin;

use Magento\Catalog\Model\ResourceModel\Product as ProductResource;

class ProductDelete
{
    /**
     * @var \Walish\RabbitMCute\Queue\Publisher\DeleteProduct
     */
    private $deleteProductPublisher;

    public function __construct(\Walish\RabbitMCute\Queue\Publisher\DeleteProduct $deleteProductPublisher)
    {
        $this->deleteProductPublisher = $deleteProductPublisher;
    }


    /**
     * @param ProductResource $subject
     * @param ProductResource $result
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @return ProductResource
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterDelete(
        ProductResource $subject,
        ProductResource $result,
        \Magento\Catalog\Api\Data\ProductInterface $product
    ) {
        $this->deleteProductPublisher->execute($product);
        return $result;
    }
}
