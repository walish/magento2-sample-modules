<?php
namespace Walish\CustomRenderAttributeField\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Setup\EavSetupFactory;

class InstallData implements InstallDataInterface
{
    /**
     * @var \Magento\Eav\Setup\EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * InstallData constructor.
     * @param \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory
     */
    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        $attributes = $this->_getSetupAttributes();
        if (count($attributes) > 0) {
            foreach ($attributes as $code => $attr) {
                $eavSetup->addAttribute(
                    'catalog_product',
                    $code,
                    $attr
                );
            }
        }
    }

    protected function _getSetupAttributes()
    {
        return [
            'search_words' => [
                'type' => 'static',
                'label' => 'Search Words',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'backend' => 'Walish\CustomRenderAttributeField\Model\Product\Attribute\Backend\SearchWords',
                'input_renderer' => '\Walish\CustomRenderAttributeField\Block\Adminhtml\Product\Helper\Form\SearchWords',
                'required' => false,
                'sort_order' => 10,
                'visible' => true,
                'group' => 'General',
                'is_used_in_grid' => false,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
            ]
        ];
    }
}