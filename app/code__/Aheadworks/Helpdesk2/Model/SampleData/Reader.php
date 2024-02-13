<?php
/**
 * Aheadworks Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://ecommerce.aheadworks.com/end-user-license-agreement/
 *
 * @package    Helpdesk2
 * @version    2.0.6
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Helpdesk2\Model\SampleData;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\SampleData\Context as SampleDataContext;
use Magento\Framework\Setup\SampleData\FixtureManager;
use Magento\Framework\File\Csv;

/**
 * Class Reader
 *
 * @package Aheadworks\Helpdesk2\Model\SampleData
 */
class Reader
{
    /**
     * @var FixtureManager
     */
    private $fixtureManager;

    /**
     * @var Csv
     */
    private $csvReader;

    /**
     * @param SampleDataContext $sampleDataContext
     */
    public function __construct(
        SampleDataContext $sampleDataContext
    ) {
        $this->fixtureManager = $sampleDataContext->getFixtureManager();
        $this->csvReader = $sampleDataContext->getCsvReader();
    }

    /**
     * Read file
     *
     * @param string $fileName
     * @return array
     * @throws \Exception
     */
    public function readFile($fileName)
    {
        $fileName = $this->fixtureManager->getFixture($fileName);
        //phpcs:ignore Magento2.Functions.DiscouragedFunction
        if (!file_exists($fileName)) {
            throw new LocalizedException(__('File %1 not found.', $fileName));
        }

        return $this->convertRowData($this->csvReader->getData($fileName));
    }

    /**
     * Convert row data
     *
     * @param array $rows
     * @return array
     */
    private function convertRowData($rows)
    {
        $header = array_shift($rows);
        foreach ($rows as &$row) {
            $data = [];
            foreach ($row as $key => $value) {
                $data[$header[$key]] = $value;
            }
            $row = $data;
        }

        return $rows;
    }
}
