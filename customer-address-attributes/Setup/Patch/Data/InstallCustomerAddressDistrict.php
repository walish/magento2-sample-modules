<?php

namespace Walish\Directory\Setup\Patch\Data;

use Magento\Customer\Setup\CustomerSetup;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchVersionInterface;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Eav\Model\Entity\Attribute\Set as AttributeSet;

class InstallCustomerAddressDistrict implements DataPatchInterface, PatchVersionInterface
{
    /**
     * @var CustomerSetupFactory
     */
    private $customerSetupFactory;

    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var AttributeSetFactory
     */
    private $attributeSetFactory;

    /**
     * DefaultCustomerGroupsAndAttributes constructor.
     * @param CustomerSetupFactory $customerSetupFactory
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param AttributeSetFactory $attributeSetFactory
     */
    public function __construct(
        CustomerSetupFactory $customerSetupFactory,
        \Magento\Framework\Setup\ModuleDataSetupInterface $moduleDataSetup,
        AttributeSetFactory $attributeSetFactory
    ) {
        $this->customerSetupFactory = $customerSetupFactory;
        $this->moduleDataSetup = $moduleDataSetup;
        $this->attributeSetFactory = $attributeSetFactory;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function apply()
    {
        /** @var CustomerSetup $customerSetup */
        $customerSetup = $this->customerSetupFactory->create(['setup' => $this->moduleDataSetup]);

        $customerSetup->installEntities($this->getEntitiesToInstall());
        $this->installCustomerForm($customerSetup, $this->getEntitiesToInstall());
    }

    /**
     * @param CustomerSetup $customerSetup
     * @param array $entities
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function installCustomerForm($customerSetup, $entities)
    {
        $customerEntity = $customerSetup->getEavConfig()->getEntityType('customer_address');
        $attributeSetId = $customerEntity->getDefaultAttributeSetId();

        /** @var AttributeSet $attributeSet */
        $attributeSet = $this->attributeSetFactory->create();
        $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);

        $attributesInfo = $entities['customer_address']['attributes'];
        foreach ($attributesInfo as $attributeCode => $attributeParams) {
            $attribute = $customerSetup->getEavConfig()->getAttribute('customer_address', $attributeCode);
            $attribute->addData([
                'attribute_set_id' => $attributeSetId,
                'attribute_group_id' => $attributeGroupId,
                'used_in_forms' => [
                    'adminhtml_customer_address',
                    'customer_address_edit',
                    'customer_register_address'
                ],
            ]);
            $attribute->save();
        }
    }

    /**
     * Retrieve default entities: customer, customer_address
     *
     * @return array
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function getEntitiesToInstall()
    {
        $entities = [
            'customer_address' => [
                'entity_type_id' => \Magento\Customer\Api\AddressMetadataInterface::ATTRIBUTE_SET_ID_ADDRESS,
                'entity_model' => \Magento\Customer\Model\ResourceModel\Address::class,
                'attribute_model' => \Magento\Customer\Model\Attribute::class,
                'table' => 'customer_address_entity',
                'additional_attribute_table' => 'customer_eav_attribute',
                'entity_attribute_collection' => \Magento\Customer\Model\ResourceModel\Address\Attribute\Collection::class,
                'attributes' => [
                    'district_id' => [
                        'type' => 'varchar',
                        'label' => 'District',
                        'input' => 'select',
                        'source' => \Walish\Directory\Model\ResourceModel\Address\Attribute\Source\District::class,
                        'required' => false,
                        'visible' => true,
                        'sort_order' => 111,
                        'position' => 111,
                        'system' => false,
                        'user_defined' => true
                    ],
                    'ward_id' => [
                        'type' => 'varchar',
                        'label' => 'Ward',
                        'input' => 'select',
                        'source' => \Walish\Directory\Model\ResourceModel\Address\Attribute\Source\Ward::class,
                        'required' => false,
                        'visible' => true,
                        'sort_order' => 112,
                        'position' => 112,
                        'system' => false,
                        'user_defined' => true
                    ]
                ],
            ],
        ];

        return $entities;
    }


    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public static function getVersion()
    {
        return '1.0.0';
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }
}
