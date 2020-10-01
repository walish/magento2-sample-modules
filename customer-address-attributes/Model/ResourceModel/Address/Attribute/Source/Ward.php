<?php

namespace Walish\Directory\Model\ResourceModel\Address\Attribute\Source;

class Ward extends \Magento\Eav\Model\Entity\Attribute\Source\Table
{
    /**
     * @var \Walish\Directory\Model\ResourceModel\Ward\CollectionFactory
     */
    private $wardCollectionFactory;

    public function __construct(
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory $attrOptionCollectionFactory,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\OptionFactory $attrOptionFactory,
        \Walish\Directory\Model\ResourceModel\Ward\CollectionFactory $wardCollectionFactory
    ) {
        $this->wardCollectionFactory = $wardCollectionFactory;
        parent::__construct($attrOptionCollectionFactory, $attrOptionFactory);
    }

    public function getAllOptions($withEmpty = true, $defaultValues = false)
    {
        if (!$this->_options) {
            $this->_options = $this->createWardCollection()->load()->toOptionArray();
        }

        return $this->_options;
    }

    /**
     * @return \Walish\Directory\Model\ResourceModel\Ward\Collection
     */
    protected function createWardCollection()
    {
        return $this->wardCollectionFactory->create();
    }
}