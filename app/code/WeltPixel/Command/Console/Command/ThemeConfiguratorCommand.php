<?php
namespace WeltPixel\Command\Console\Command;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Framework\Component\ComponentRegistrar;

class ThemeConfiguratorCommand extends ImportAbstract
{
    const ARGUMENT_STORE = 'store';
    const ARGUMENT_HOME_PAGE = 'homePage';
    const ARGUMENT_HEADER = 'header';
    const ARGUMENT_CATEGORY_PAGE = 'categoryPage';
    const ARGUMENT_PRODUCT_PAGE = 'productPage';
    const ARGUMENT_PRE_FOOTER = 'preFooter';
    const ARGUMENT_FOOTER = 'footer';

    const HOME_PAGE_VERSIONS = [
        'v1',
        'v2',
        'v3',
        'v4',
        'v5',
        'v6',
        'v7',
        'v8',
        'v9',
        'v10',
        'v11',
        'v12',
        'v14',
        'v15'
    ];

    const HEADER_VERSIONS = [
        'v1',
        'v2',
        'v3',
        'v4'
    ];

    const CATEGORY_PAGE_VERSIONS = [
        '2columns',
        '3columns',
        '4columns',
        '5columns'
    ];

    const PRODUCT_PAGE_VERSIONS = [
        'v1',
        'v2',
        'v3',
        'v4'
    ];

    const PRE_FOOTER = [
      'yes',
      'no'
    ];

    const FOOTER_VERSIONS = [
        'v1',
        'v2',
        'v3',
        'v4'
    ];

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('weltpixel:theme:configurator')
            ->setDescription('Configure Theme')
            ->setDefinition([
                new InputOption(
                    self::ARGUMENT_STORE,
                    null,
                    InputOption::VALUE_REQUIRED,
                    'Store'
                ),
                new InputOption(
                    self::ARGUMENT_HOME_PAGE,
                    null,
                    InputOption::VALUE_OPTIONAL,
                    'Home Page Version'
                ),
                new InputOption(
                    self::ARGUMENT_HEADER,
                    null,
                    InputOption::VALUE_OPTIONAL,
                    'Header Version'
                ),
                new InputOption(
                    self::ARGUMENT_CATEGORY_PAGE,
                    null,
                    InputOption::VALUE_OPTIONAL,
                    'Category Page Columns'
                ),
                new InputOption(
                    self::ARGUMENT_PRODUCT_PAGE,
                    null,
                    InputOption::VALUE_OPTIONAL,
                    'Product Page Version'
                ),
                new InputOption(
                    self::ARGUMENT_PRE_FOOTER,
                    null,
                    InputOption::VALUE_OPTIONAL,
                    'Pre-footer'
                ),
                new InputOption(
                    self::ARGUMENT_FOOTER,
                    null,
                    InputOption::VALUE_OPTIONAL,
                    'Footer Version'
                )
            ]);;

        parent::configure();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $storeCode = $input->getOption(self::ARGUMENT_STORE);
        $homePageVersion = $input->getOption(self::ARGUMENT_HOME_PAGE);
        $headerVersion = $input->getOption(self::ARGUMENT_HEADER);
        $categoryColumns = $input->getOption(self::ARGUMENT_CATEGORY_PAGE);
        $productPageVersion = $input->getOption(self::ARGUMENT_PRODUCT_PAGE);
        $preFooter = $input->getOption(self::ARGUMENT_PRE_FOOTER);
        $footerVersion = $input->getOption(self::ARGUMENT_FOOTER);

        $result = $this->validateInputs($storeCode, $homePageVersion, $headerVersion,
            $categoryColumns, $productPageVersion, $preFooter, $footerVersion);

        $store = $result['store'];
        $csvData = $result['csvData'];

        $this->importCsvData($csvData, $store);
        $output->writeln('Import was finished successfully.');
    }

    /**
     * @param string $storeCode
     * @param string $homePageVersion
     * @param string $headerVersion
     * @param string $categoryColumns
     * @param string $productPageVersion
     * @param string $preFooter
     * @param string $footerVersion
     * @return array
     * @throws \Exception
     */
    protected function validateInputs($storeCode, $homePageVersion, $headerVersion,
                                      $categoryColumns, $productPageVersion, $preFooter, $footerVersion)
    {
        $demoCsvPath = $this->componentRegistrar->getPath(ComponentRegistrar::MODULE, 'WeltPixel_Command') .
        DIRECTORY_SEPARATOR . 'themeConfigurator';
        $noOptionSelected = true;
        $data = [];

        if (is_null($storeCode) || !trim($storeCode)) {
            throw new \InvalidArgumentException('Argument ' . self::ARGUMENT_STORE . ' is missing.');
        }

        /** homePage params verifications */
        if (!is_null($homePageVersion) && strlen($homePageVersion) != 0) {
            if (!in_array($homePageVersion, self::HOME_PAGE_VERSIONS)) {
                throw new \Exception('Argument ' . self::ARGUMENT_HOME_PAGE . ' must be one of the following: ' . implode(', ', self::HOME_PAGE_VERSIONS));
            }
            $noOptionSelected = false;
            $csvFile = $demoCsvPath . DIRECTORY_SEPARATOR . 'homePage' .
                DIRECTORY_SEPARATOR . $homePageVersion . '.csv';
            $csvData = $this->csvFile->getData($csvFile);
            $data = array_merge($data, $csvData);
        }

        /** header params verifications */
        if (!is_null($headerVersion) && strlen($headerVersion) != 0) {
            if (!in_array($headerVersion, self::HEADER_VERSIONS)) {
                throw new \Exception('Argument ' . self::ARGUMENT_HEADER . ' must be one of the following: ' . implode(', ', self::HEADER_VERSIONS));
            }
            $noOptionSelected = false;
            $csvFile = $demoCsvPath . DIRECTORY_SEPARATOR . 'header' .
                DIRECTORY_SEPARATOR . $headerVersion . '.csv';
            $csvData = $this->csvFile->getData($csvFile);
            $data = array_merge($data, $csvData);
        }

        /** categoryPage params verifications */
        if (!is_null($categoryColumns) && strlen($categoryColumns) != 0) {
            if (!in_array($categoryColumns, self::CATEGORY_PAGE_VERSIONS)) {
                throw new \Exception('Argument ' . self::ARGUMENT_CATEGORY_PAGE . ' must be one of the following: ' . implode(', ', self::CATEGORY_PAGE_VERSIONS));
            }
            $noOptionSelected = false;
            $csvFile = $demoCsvPath . DIRECTORY_SEPARATOR . 'categoryPage' .
                DIRECTORY_SEPARATOR . $categoryColumns . '.csv';
            $csvData = $this->csvFile->getData($csvFile);
            $data = array_merge($data, $csvData);
        }

        /** productPage params verifications */
        if (!is_null($productPageVersion) && strlen($productPageVersion) != 0) {
            if (!in_array($productPageVersion, self::PRODUCT_PAGE_VERSIONS)) {
                throw new \Exception('Argument ' . self::ARGUMENT_PRODUCT_PAGE . ' must be one of the following: ' . implode(', ', self::PRODUCT_PAGE_VERSIONS));
            }
            $noOptionSelected = false;
            $csvFile = $demoCsvPath . DIRECTORY_SEPARATOR . 'productPage' .
                DIRECTORY_SEPARATOR . $productPageVersion . '.csv';
            $csvData = $this->csvFile->getData($csvFile);
            $data = array_merge($data, $csvData);
        }

        /** pre-footer params verifications */
        if (!is_null($preFooter) && strlen($preFooter) != '') {
            if (!in_array($preFooter, self::PRE_FOOTER)) {
                throw new \Exception('Argument ' . self::ARGUMENT_PRE_FOOTER . ' must be one of the following: ' . implode(', ', self::PRE_FOOTER));
            }
            $noOptionSelected = false;
            $csvFile = $demoCsvPath . DIRECTORY_SEPARATOR . 'preFooter' .
                DIRECTORY_SEPARATOR . $preFooter . '.csv';
            $csvData = $this->csvFile->getData($csvFile);
            $data = array_merge($data, $csvData);
        }

        /** footer params verifications */
        if (!is_null($footerVersion) && strlen($footerVersion) != 0) {
            if (!in_array($footerVersion, self::FOOTER_VERSIONS)) {
                throw new \Exception('Argument ' . self::ARGUMENT_FOOTER . ' must be one of the following: ' . implode(', ', self::FOOTER_VERSIONS));
            }
            $noOptionSelected = false;
            $csvFile = $demoCsvPath . DIRECTORY_SEPARATOR . 'footer' .
                DIRECTORY_SEPARATOR . $footerVersion . '.csv';
            $csvData = $this->csvFile->getData($csvFile);
            $data = array_merge($data, $csvData);
        }

        if ($noOptionSelected) {
            throw new \Exception('Please specify valid options for one of the following options: ' . implode(', ',
                    [self::ARGUMENT_HOME_PAGE, self::ARGUMENT_HEADER, self::ARGUMENT_CATEGORY_PAGE, self::ARGUMENT_PRODUCT_PAGE, self::ARGUMENT_FOOTER]));
        }

        /** Allow import of global configurations as well */
        if ($storeCode == 'GLOBAL') {
            $storeCode = 0;
        }

        try {
            $store = $this->storeManager->getStore($storeCode);
        } catch (\Exception $ex) {
            throw new \Exception('Store with id or code ' . $storeCode . ' not found.');
        }

        return [
            'store' => $store,
            'csvData' => $data
        ];
    }
}