<?php

namespace Walish\Directory\Model\ResourceModel;

class District extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('directory_region_district', 'district_id');
    }
}