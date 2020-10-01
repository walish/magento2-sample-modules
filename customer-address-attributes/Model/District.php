<?php

namespace Walish\Directory\Model;

class District extends \Magento\Framework\Model\AbstractModel
{
    protected function _construct()
    {
        $this->_init(\Walish\Directory\Model\ResourceModel\District::class);
    }
}