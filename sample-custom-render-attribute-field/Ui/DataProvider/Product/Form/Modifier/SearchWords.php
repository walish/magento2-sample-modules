<?php
namespace Walish\CustomRenderAttributeField\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Ui\Component\Form\Field;

class SearchWords extends AbstractModifier
{

    /**
     * @var LocatorInterface
     */
    protected $locator;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var ArrayManager
     */
    protected $arrayManager;

    /**
     * @param LocatorInterface $locator
     * @param UrlInterface $urlBuilder
     * @param ArrayManager $arrayManager
     */
    public function __construct(
        LocatorInterface $locator,
        UrlInterface $urlBuilder,
        ArrayManager $arrayManager
    ) {
        $this->locator = $locator;
        $this->urlBuilder = $urlBuilder;
        $this->arrayManager = $arrayManager;
    }

    /**
     * @param array $data
     * @return array
     */
    public function modifyData(array $data)
    {
        return $data;
    }

    /**
     * @param array $meta
     * @return array
     */
    public function modifyMeta(array $meta)
    {
        $meta = $this->customizeField($meta);
        return $meta;
    }

    protected function customizeField(array $meta)
    {
        $fieldCode = 'search_words';

        if ($panelName = $this->getGeneralPanelName($meta)) {
            $meta[$panelName]['children'][$fieldCode]['arguments']['data']['config'] = [
                'component' => 'Magento_Catalog/js/components/new-category',
                'elementTmpl' => 'Walish_CustomRenderAttributeField/grid/filter/elements/ui-chips',
                'formElement' => 'select',
                'componentType' => Field::NAME,
                'visible' => 1,
                'options' => $this->getOptions(),
                'label' => __('Search Words'),
                'dataScope' => 'search_words',
                'sortOrder' => 10,
                'selectedPlaceholders' => ['defaultPlaceholder' => ''],
            ];
        }

        return $meta;
    }

    public function getOptions()
    {
        $options = [];
        $data = $this->locator->getProduct()->getData('search_words');

        if (!empty($data)) {
            foreach ($data as $option) {
                $options[] = ['value' => $option, 'label' => $option];
            }
        }

        return $options;
    }
}