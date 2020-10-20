<?php

namespace Walish\RabbitMCute\Queue\Consumer;

class DeleteProduct
{
    /**
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @return void
     */
    public function processMessage(\Magento\Catalog\Api\Data\ProductInterface $product)
    {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/product-delete.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info($product->getId() . ' ' . $product->getSku());
    }
}