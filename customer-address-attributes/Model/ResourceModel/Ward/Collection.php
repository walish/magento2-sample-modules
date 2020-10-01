<?php

namespace Walish\Directory\Model\ResourceModel\Ward;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
        $this->_init(\Walish\Directory\Model\Ward::class, \Walish\Directory\Model\ResourceModel\Ward::class);
    }

    /**
     * Filter by district_id
     *
     * @param array $districtIds
     * @return $this
     */
    public function addDistrictFilter($districtIds)
    {
        if (!empty($districtIds)) {
            if (!is_array($districtIds)) {
                $districtIds = [$districtIds];
            }

            $this->addFieldToFilter('main_table.district_id', ['in' => $districtIds]);
        }
        return $this;
    }

    public function toOptionArray()
    {
        $options = [];
        $propertyMap = [
            'value' => 'ward_id',
            'title' => 'name',
            'district_id' => 'district_id',
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
                ['title' => "", 'value' => "", 'label' => __('Please select a ward.')]
            );
        }
        return $options;
    }
}
