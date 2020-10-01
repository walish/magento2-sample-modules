<?php

namespace Walish\Directory\Block\Checkout;

class AdditionalDirectoryDataProcessor implements \Magento\Checkout\Block\Checkout\LayoutProcessorInterface
{
    /**
     * @var \Walish\Directory\Model\ResourceModel\District\CollectionFactory
     */
    private $districtCollectionFactory;

    /**
     * @var \Walish\Directory\Model\ResourceModel\Ward\CollectionFactory
     */
    private $wardCollectionFactory;

    public function __construct(
        \Walish\Directory\Model\ResourceModel\District\CollectionFactory $districtCollectionFactory,
        \Walish\Directory\Model\ResourceModel\Ward\CollectionFactory $wardCollectionFactory
    ) {
        $this->districtCollectionFactory = $districtCollectionFactory;
        $this->wardCollectionFactory = $wardCollectionFactory;
    }


    /**
     * Process js Layout of block
     *
     * @param array $jsLayout
     * @return array
     */
    public function process($jsLayout)
    {
        if (isset($jsLayout['components']['checkoutProvider']['dictionaries'])) {
            $jsLayout['components']['checkoutProvider']['dictionaries']['district_id'] = $this->getDistrictOptions();
            $jsLayout['components']['checkoutProvider']['dictionaries']['ward_id'] = $this->getWardOptions();
        }

        return $jsLayout;
    }


    private function getDistrictOptions()
    {
        $districtCollection = $this->districtCollectionFactory->create()->toOptionArray();

        return $districtCollection;
    }

    private function getWardOptions()
    {
        $wardCollection = $this->wardCollectionFactory->create()->toOptionArray();

        return $wardCollection;
    }


}
