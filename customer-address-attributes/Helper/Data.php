<?php

namespace Walish\Directory\Helper;

use Magento\Directory\Model\CurrencyFactory;
use Magento\Directory\Model\ResourceModel\Country\Collection;
use Magento\Directory\Model\ResourceModel\Region\CollectionFactory;
use Magento\Framework\App\Cache\Type\Config;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Json\Helper\Data as JsonData;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class Data extends \Magento\Directory\Helper\Data
{
    /**
     * @var \Walish\Directory\Model\ResourceModel\District\CollectionFactory
     */
    private $districtCollectionFactory;

    /**
     * @var \Walish\Directory\Model\ResourceModel\Ward\CollectionFactory
     */
    private $wardCollectionFactory;

    /**
     * Json representation of districts data
     *
     * @var string
     */
    protected $districtJson;

    /**
     * Json representation of wards data
     *
     * @var string
     */
    protected $wardJson;

    public function __construct(
        Context $context,
        Config $configCacheType,
        Collection $countryCollection,
        CollectionFactory $regCollectionFactory,
        JsonData $jsonHelper,
        StoreManagerInterface $storeManager,
        CurrencyFactory $currencyFactory,
        \Walish\Directory\Model\ResourceModel\District\CollectionFactory $districtCollectionFactory,
        \Walish\Directory\Model\ResourceModel\Ward\CollectionFactory $wardCollectionFactory
    ) {
        parent::__construct(
            $context,
            $configCacheType,
            $countryCollection,
            $regCollectionFactory,
            $jsonHelper,
            $storeManager,
            $currencyFactory
        );
        $this->districtCollectionFactory = $districtCollectionFactory;
        $this->wardCollectionFactory = $wardCollectionFactory;
    }

    /**
     * Retrieve district collection
     *
     * @param string|array $regionIds
     *
     * @return \Walish\Directory\Model\ResourceModel\District\Collection
     */
    public function getDistrictCollection($regionIds)
    {
        $districtCollection = $this->districtCollectionFactory->create();
        $districtCollection->addRegionFilter($regionIds)->load();

        return $districtCollection;
    }

    public function getWardCollection($districtIds)
    {
        $wardCollection = $this->wardCollectionFactory->create();
        return $wardCollection->addDistrictFilter($districtIds)->load();
    }

    /**
     * Retrieve regions data json
     *
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getDistrictJson()
    {
        \Magento\Framework\Profiler::start('TEST: ' . __METHOD__, ['group' => 'TEST', 'method' => __METHOD__]);
        if (!$this->districtJson) {
            $scope = $this->getCurrentScope();
            $scopeKey = $scope['value'] ? '_' . implode('_', $scope) : null;
            $cacheKey = 'DIRECTORY_DISTRICTS_JSON_STORE' . $scopeKey;
            $json = $this->_configCacheType->load($cacheKey);
            if (empty($json)) {
                $regions = $this->getDistrictData();
                $json = $this->jsonHelper->jsonEncode($regions);
                if ($json === false) {
                    $json = 'false';
                }
                $this->_configCacheType->save($json, $cacheKey);
            }
            $this->districtJson = $json;
        }

        \Magento\Framework\Profiler::stop('TEST: ' . __METHOD__);
        return $this->districtJson;
    }

    /**
     * Retrieve districts data
     *
     * @return array
     */
    public function getDistrictData()
    {
        $regionIds = [];
        foreach ($this->getRegionCollection() as $region) {
            $regionIds[] = $region->getRegionId();
        }

        /** @var \Walish\Directory\Model\ResourceModel\District\Collection $collection */
        $collection = $this->districtCollectionFactory->create();
        $collection->addRegionFilter($regionIds)->load();

        $districts = [
            'config' => [
                'show_all_regions' => true,
                'regions_required' => $regionIds,
            ],
        ];
        foreach ($collection as $district) {
            /** @var \Walish\Directory\Model\District $district */
            if (!$district->getDistrictId()) {
                continue;
            }
            $districts[$district->getRegionId()][$district->getDistrictId()] = [
                'code' => $district->getCode(),
                'name' => $district->getName(),
            ];
        }

        return $districts;
    }

    /**
     * Retrieve wards data json
     *
     * @param null $regionIds
     * @return string
     */
    public function getWardJson($regionIds = null)
    {
        if (!$this->wardJson) {
            $scope = $this->getCurrentScope();
            $scopeKey = $scope['value'] ? '_' . implode('_', $scope) : null;
            $cacheKey = 'DIRECTORY_WARDS_JSON_STORE' . $scopeKey;
            $json = $this->_configCacheType->load($cacheKey);
            if (empty($json)) {
                $wards = $this->getWardData($regionIds);
                $json = $this->jsonHelper->jsonEncode($wards);
                if ($json === false) {
                    $json = 'false';
                }
                $this->_configCacheType->save($json, $cacheKey);
            }
            $this->wardJson = $json;
        }

        return $this->wardJson;
    }

    public function getWardData($regionIds)
    {
        $districtIds = [];
        foreach ($this->getDistrictCollection($regionIds) as $district) {
            $districtIds[] = $district->getDistrictId();
        }

        /** @var \Walish\Directory\Model\ResourceModel\Ward\Collection $collection */
        $collection = $this->wardCollectionFactory->create();
        $collection->addDistrictFilter($districtIds)->load();
        $wards = [
            'config' => [
                'show_all_regions' => true,
                'regions_required' => $districtIds,
            ],
        ];
        foreach ($collection as $ward) {
            /** @var \Walish\Directory\Model\Ward $ward */
            if (!$ward->getWardId()) {
                continue;
            }
            $wards[$ward->getDistrictId()][$ward->getWardId()] = [
                'code' => $ward->getCode(),
                'name' => (string)__($ward->getName()),
            ];
        }
        return $wards;
    }

    /**
     * Get current scope from request
     *
     * @return array
     */
    private function getCurrentScope(): array
    {
        $scope = [
            'type' => ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
            'value' => null,
        ];
        $request = $this->_getRequest();
        if ($request->getParam(ScopeInterface::SCOPE_WEBSITE)) {
            $scope = [
                'type' => ScopeInterface::SCOPE_WEBSITE,
                'value' => $request->getParam(ScopeInterface::SCOPE_WEBSITE),
            ];
        } elseif ($request->getParam(ScopeInterface::SCOPE_STORE)) {
            $scope = [
                'type' => ScopeInterface::SCOPE_STORE,
                'value' => $request->getParam(ScopeInterface::SCOPE_STORE),
            ];
        }

        return $scope;
    }

    /**
     * Retrieve region collection
     *
     * @return \Magento\Directory\Model\ResourceModel\Region\Collection
     */
    public function getRegionCollection()
    {
        if (!$this->_regionCollection) {
            $this->_regionCollection = $this->_regCollectionFactory->create();
            $this->_regionCollection->load();
        }
        return $this->_regionCollection;
    }
}
