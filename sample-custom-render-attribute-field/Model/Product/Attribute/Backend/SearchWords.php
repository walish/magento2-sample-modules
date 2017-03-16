<?php
namespace Walish\CustomRenderAttributeField\Model\Product\Attribute\Backend;

class SearchWords extends \Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend
{
    /**
     * @param \Magento\Catalog\Model\Product $object
     * @return $this
     */
    public function afterLoad($object)
    {
        // Change Attribute Data from string to array
        $value = $object->getSearchWords();
        if ($value) {
            // Example data: 'abc,def,ghi'
            $object->setData($this->getAttribute()->getAttributeCode(), explode(',', $value));
        }
        
        return parent::afterLoad($object);
    }
}