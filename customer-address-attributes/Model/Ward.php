<?php

namespace Walish\Directory\Model;

class Ward extends \Magento\Framework\Model\AbstractModel
{
    protected function _construct()
    {
        $this->_init(\Walish\Directory\Model\ResourceModel\Ward::class);
    }
}