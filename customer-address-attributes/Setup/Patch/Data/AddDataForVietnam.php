<?php

declare(strict_types=1);

namespace Walish\Directory\Setup\Patch\Data;

use Magento\Directory\Setup\DataInstaller;
use Magento\Directory\Setup\DataInstallerFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

/**
 * Insert Province data for Vietnam
 */
class AddDataForVietnam implements DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var DataInstallerFactory
     */
    private $dataInstallerFactory;

    /**
     * @var \Magento\Framework\Setup\SampleData\FixtureManager
     */
    private $fixtureManager;

    /**
     * @var \Magento\Framework\File\Csv
     */
    private $csvReader;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param DataInstallerFactory $dataInstallerFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        DataInstallerFactory $dataInstallerFactory,
        \Magento\Framework\Setup\SampleData\FixtureManager $fixtureManager,
        \Magento\Framework\File\Csv $csvReader
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->dataInstallerFactory = $dataInstallerFactory;
        $this->fixtureManager = $fixtureManager;
        $this->csvReader = $csvReader;
    }

    /**
     * @inheritdoc
     */
    public function apply()
    {
        /** @var DataInstaller $dataInstaller */
        $dataInstaller = $this->dataInstallerFactory->create();
        $dataInstaller->addCountryRegions(
            $this->moduleDataSetup->getConnection(),
            $this->getDataForVietnam()
        );
    }

    /**
     * Vietnam province data
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getDataForVietnam()
    {
        $fileName = 'Walish_Directory::Files/province.csv';
        $fileName = $this->fixtureManager->getFixture($fileName);
        if (!file_exists($fileName)) {
            return [];
        }

        $regionData = $this->csvReader->getData($fileName);
        array_shift($regionData);

        return $regionData;
    }

    /**
     * @inheritdoc
     */
    public static function getDependencies()
    {
        return [
            \Magento\Directory\Setup\Patch\Data\InitializeDirectoryData::class,
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
