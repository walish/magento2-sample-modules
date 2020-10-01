<?php

namespace Walish\Directory\Model\ResourceModel;

class Ward extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('directory_district_ward', 'ward_id');
    }
}