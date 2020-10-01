<?php

namespace Walish\Directory\Model\ResourceModel\District;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
        $this->_init(\Walish\Directory\Model\District::class, \Walish\Directory\Model\ResourceModel\District::class);
    }

    /**
     * Filter by region_id
     *
     * @param string|array $regionIds
     * @return $this
     */
    public function addRegionFilter($regionIds)
    {
        if (!empty($regionIds)) {
            if (!is_array($regionIds)) {
                $regionIds = [$regionIds];
            }

            $this->addFieldToFilter('main_table.region_id', ['in' => $regionIds]);
        }

        return $this;
    }

    /**
     * Convert collection items to select options array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];
        $propertyMap = [
            'value' => 'district_id',
            'title' => 'name',
            'region_id' => 'region_id',
        ];

        foreach ($this as $item) {
            $option = [];
            foreach ($propertyMap as $code => $field) {
                $option[$code] = $item->getData($field);
            }
            $option['label'] = $item->getName();
            $options[] = $option;
        }

        if (count($options) > 0) {
            array_unshift(
                $options,
                ['title' => "", 'value' => "", 'label' => __('Please select a district.')]
            );
        }
        return $options;
    }
}
