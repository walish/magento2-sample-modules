<?php

declare(strict_types=1);

namespace Walish\Directory\Setup\Patch\Data;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class AddDistrictWardForVietnam implements DataPatchInterface
{
    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var \Magento\Framework\Setup\SampleData\FixtureManager
     */
    private $fixtureManager;

    /**
     * @var \Magento\Framework\File\Csv
     */
    private $csvReader;

    /**
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param \Magento\Framework\Setup\SampleData\FixtureManager $fixtureManager
     * @param \Magento\Framework\File\Csv $csvReader
     */
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        ModuleDataSetupInterface $moduleDataSetup,
        \Magento\Framework\Setup\SampleData\FixtureManager $fixtureManager,
        \Magento\Framework\File\Csv $csvReader
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->moduleDataSetup = $moduleDataSetup;
        $this->fixtureManager = $fixtureManager;
        $this->csvReader = $csvReader;
    }

    /**
     * @inheritdoc
     */
    public function apply()
    {
        $connection = $this->moduleDataSetup->getConnection();
        $connection->beginTransaction();

        try {
            $this->addRegionDistricts($connection);
            $this->addDistrictWards($connection);

            $connection->commit();
        } catch (\Magento\Framework\Exception\LocalizedException $exception) {
            $connection->rollBack();
        }
    }

    /**
     * @param AdapterInterface $adapter
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function addRegionDistricts(AdapterInterface $adapter)
    {
        $districtData = $this->getDistrictDataForVietnam();
        $adapter->insertArray('directory_region_district', ['code', 'name', 'region_id'], $districtData);

        // Update increment_id
        $adapter->query("UPDATE directory_region_district district
                                    SET district.region_id = (SELECT region.region_id
                                    FROM directory_country_region region
                                    WHERE region.country_id = 'VN' AND region.code = CONCAT('VN-', district.region_id))");
    }

    /**
     * Vietnam province data
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getDistrictDataForVietnam()
    {
        $fileName = 'Walish_Directory::Files/district.csv';
        $fileName = $this->fixtureManager->getFixture($fileName);
        if (!file_exists($fileName)) {
            return [];
        }

        $districtData = $this->csvReader->getData($fileName);
        array_shift($districtData);

        return $districtData;
    }

    private function addDistrictWards(AdapterInterface $adapter)
    {
        $districtData = $this->getWardDataForVietNam();
        $adapter->insertArray('directory_district_ward', ['code', 'name', 'district_id'], $districtData);

        $adapter->query("UPDATE directory_district_ward ward
                                      SET ward.district_id = (SELECT district.district_id
                                      FROM directory_region_district district
                                      WHERE ward.district_id = district.code)");
    }

    private function getWardDataForVietNam()
    {
        $fileName = 'Walish_Directory::Files/ward.csv';
        $fileName = $this->fixtureManager->getFixture($fileName);
        if (!file_exists($fileName)) {
            return [];
        }

        $wardData = $this->csvReader->getData($fileName);
        array_shift($wardData);

        return $wardData;
    }

    /**
     * @inheritdoc
     */
    public static function getDependencies()
    {
        return [
            \Walish\Directory\Setup\Patch\Data\AddDataForVietnam::class
        ];
    }

    /**
     * @inheritdoc
     */
    public function getAliases()
    {
        return [];
    }
}
