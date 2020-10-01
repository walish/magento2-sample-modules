<?php

namespace Walish\Directory\Model\ResourceModel\Address\Attribute\Source;

class District extends \Magento\Eav\Model\Entity\Attribute\Source\Table
{
    /**
     * @var \Walish\Directory\Model\ResourceModel\District\CollectionFactory
     */
    private $districtCollectionFactory;

    public function __construct(
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory $attrOptionCollectionFactory,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\OptionFactory $attrOptionFactory,
        \Walish\Directory\Model\ResourceModel\District\CollectionFactory $districtCollectionFactory
    ) {
        $this->districtCollectionFactory = $districtCollectionFactory;
        parent::__construct($attrOptionCollectionFactory, $attrOptionFactory);
    }

    /**
     * @inheritdoc
     */
    public function getAllOptions($withEmpty = true, $defaultValues = false)
    {
        if (!$this->_options) {
            $this->_options = $this->createDistrictCollection()->load()->toOptionArray();
        }
        return $this->_options;
    }

    /**
     * @return \Magento\Directory\Model\ResourceModel\Region\Collection
     */
    protected function createDistrictCollection()
    {
        return $this->districtCollectionFactory->create();
    }
}
