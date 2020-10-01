<?php

namespace Walish\Directory\Block\Checkout;

class CustomFieldLayoutProcessor implements \Magento\Checkout\Block\Checkout\LayoutProcessorInterface
{
    /**
     * @var \Magento\Customer\Model\AttributeMetadataDataProvider
     */
    private $attributeMetadataDataProvider;

    /**
     * @var \Magento\Ui\Component\Form\AttributeMapper
     */
    private $attributeMapper;

    /**
     * @var \Magento\Checkout\Block\Checkout\AttributeMerger
     */
    private $attributeMerger;

    public function __construct(
        \Magento\Customer\Model\AttributeMetadataDataProvider $attributeMetadataDataProvider,
        \Magento\Ui\Component\Form\AttributeMapper $attributeMapper,
        \Magento\Checkout\Block\Checkout\AttributeMerger $attributeMerger
    ) {
        $this->attributeMetadataDataProvider = $attributeMetadataDataProvider;
        $this->attributeMapper = $attributeMapper;
        $this->attributeMerger = $attributeMerger;
    }

    /**
     * Process js Layout of block
     *
     * @param array $jsLayout
     * @return array
     */
    public function process($jsLayout)
    {

        $elements = $this->getAddressAttributes();
        if (isset($jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
            ['children']['shippingAddress']['children']['shipping-address-fieldset']['children'])) {
            $fields = $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
            ['children']['shippingAddress']['children']['shipping-address-fieldset']['children'];
            $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
            ['children']['shippingAddress']['children']['shipping-address-fieldset']['children'] = $this->attributeMerger->merge(
                $elements,
                'checkoutProvider',
                'shippingAddress',
                $fields
            );
        }

        return $jsLayout;
    }

    private function getAddressAttributes()
    {
        $attributeCodes = [
            'district_id',
            'ward_id'
        ];

        $elements = [];
        foreach ($attributeCodes as $code) {
            $attribute = $this->attributeMetadataDataProvider->getAttribute('customer_address', $code);
            if ($attribute != false) {
                $code = $attribute->getAttributeCode();
                $elements[$code] = $this->attributeMapper->map($attribute);
                if (isset($elements[$code]['label'])) {
                    $label = $elements[$code]['label'];
                    $elements[$code]['label'] = __($label);
                }
            }

        }

        return $elements;
    }
}
