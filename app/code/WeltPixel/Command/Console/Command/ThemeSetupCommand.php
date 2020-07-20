<?php
namespace WeltPixel\Command\Console\Command;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Framework\Component\ComponentRegistrar;

class ThemeSetupCommand extends ImportAbstract
{
    const ARGUMENT_STEP = 'step';
    const ARGUMENT_STORE = 'store';
    // Step 2
    const ARGUMENT_HEADER = 'header';
    const ARGUMENT_HOME_PAGE = 'homePage';
    const ARGUMENT_FOOTER = 'footer';
    const ARGUMENT_PRE_FOOTER = 'preFooter';
    const HEADER_VERSIONS = [
        'v1',
        'v2',
        'v3',
        'v4'
    ];
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
    // Step 3
    const ARGUMENT_CATEGORY_PAGE = 'categoryPage';
    const CATEGORY_PAGE_VERSIONS = [
        '2columns',
        '3columns',
        '4columns',
        '5columns'
    ];
    // Step 4
    const ARGUMENT_PRODUCT_PAGE = 'productPage';
    const PRODUCT_PAGE_VERSIONS = [
        'v1',
        'v2',
        'v3',
        'v4'
    ];
    // Step 5
    const ARGUMENT_QUICK_VIEW = 'quickView';
    const ARGUMENT_QUICK_VIEW_BTN = 'quickViewBtn';
    const QUICK_VIEW = [
        'yes',
        'no'
    ];
    const QUICK_VIEW_BTN = [
        'v1',
        'v2'
    ];
    // Step 6
    const ARGUMENT_QUICK_CART = 'quickCart';
    const ARGUMENT_BACK_TO_TOP = 'backToTop';
    const QUICK_CART = [
        'pearlthemequickcart',
        'default'
    ];
    const BACK_TO_TOP = [
        'yes',
        'nobtt'
    ];
    // Step 7
    const ARGUMENT_PRIMARY_BTN_BG_COLOR = 'primaryBtnBgColor';
    const ARGUMENT_PRIMARY_BTN_TEXT_COLOR = 'primaryBtnTextColor';
    const ARGUMENT_PRIMARY_BTN_BORDER_COLOR = 'primaryBtnBorderColor';
    const PRIMARY_BTN_BG_COLOR = [
        'any valid hex color code'
    ];
    const PRIMARY_BTN_TEXT_COLOR = [
        'any valid hex color code'
    ];
    const PRIMARY_BTN_BORDER_COLOR = [
        'any valid hex color code'
    ];
    // Step 8
    const ARGUMENT_DEFAULT_BTN_BG_COLOR = 'defaultBtnBgColor';
    const ARGUMENT_DEFAULT_BTN_TEXT_COLOR = 'defaultBtnTextColor';
    const ARGUMENT_DEFAULT_BTN_BORDER_COLOR = 'defaultBtnBorderColor';
    const DEFAULT_BTN_BG_COLOR = [
        'any valid hex color code'
    ];
    const DEFAULT_BTN_TEXT_COLOR = [
        'any valid hex color code'
    ];
    const DEFAULT_BTN_BORDER_COLOR = [
        'any valid hex color code'
    ];

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('weltpixel:theme:setup')
            ->setDescription('Setup Theme')
            ->setDefinition([
                new InputOption(
                    self::ARGUMENT_STEP,
                    null,
                    InputOption::VALUE_REQUIRED,
                    'Step'
                ),
                // Step 2
                new InputOption(
                    self::ARGUMENT_STORE,
                    null,
                    InputOption::VALUE_REQUIRED,
                    'Store'
                ),
                new InputOption(
                    self::ARGUMENT_HEADER,
                    null,
                    InputOption::VALUE_OPTIONAL,
                    'Header Version'
                ),
                new InputOption(
                    self::ARGUMENT_HOME_PAGE,
                    null,
                    InputOption::VALUE_OPTIONAL,
                    'Home Page Version'
                ),
                new InputOption(
                    self::ARGUMENT_PRE_FOOTER,
                    null,
                    InputOption::VALUE_OPTIONAL,
                    'PreFooter'
                ),
                new InputOption(
                    self::ARGUMENT_FOOTER,
                    null,
                    InputOption::VALUE_OPTIONAL,
                    'Footer Version'
                ),
                // Step 3
                new InputOption(
                    self::ARGUMENT_CATEGORY_PAGE,
                    null,
                    InputOption::VALUE_OPTIONAL,
                    'Category Page Version'
                ),
                // Step 4
                new InputOption(
                    self::ARGUMENT_PRODUCT_PAGE,
                    null,
                    InputOption::VALUE_OPTIONAL,
                    'Product Page Version'
                ),
                // Step 5
                new InputOption(
                    self::ARGUMENT_QUICK_VIEW,
                    null,
                    InputOption::VALUE_OPTIONAL,
                    'Enable Quick View'
                ),
                new InputOption(
                    self::ARGUMENT_QUICK_VIEW_BTN,
                    null,
                    InputOption::VALUE_OPTIONAL,
                    'Quick View Button Style'
                ),
                // Step 6
                new InputOption(
                    self::ARGUMENT_QUICK_CART,
                    null,
                    InputOption::VALUE_OPTIONAL,
                    'Quick Cart Style'
                ),
                new InputOption(
                    self::ARGUMENT_BACK_TO_TOP,
                    null,
                    InputOption::VALUE_OPTIONAL,
                    'Back to Top Button'
                ),
                // Step 7
                new InputOption(
                    self::ARGUMENT_PRIMARY_BTN_BG_COLOR,
                    null,
                    InputOption::VALUE_OPTIONAL,
                    'Primary Button Background Color'
                ),
                new InputOption(
                    self::ARGUMENT_PRIMARY_BTN_TEXT_COLOR,
                    null,
                    InputOption::VALUE_OPTIONAL,
                    'Primary Button Text Color'
                ),
                new InputOption(
                    self::ARGUMENT_PRIMARY_BTN_BORDER_COLOR,
                    null,
                    InputOption::VALUE_OPTIONAL,
                    'Primary Button Border Color'
                ),
                // Step 8
                new InputOption(
                    self::ARGUMENT_DEFAULT_BTN_BG_COLOR,
                    null,
                    InputOption::VALUE_OPTIONAL,
                    'Primary Button Background Color'
                ),
                new InputOption(
                    self::ARGUMENT_DEFAULT_BTN_TEXT_COLOR,
                    null,
                    InputOption::VALUE_OPTIONAL,
                    'Primary Button Text Color'
                ),
                new InputOption(
                    self::ARGUMENT_DEFAULT_BTN_BORDER_COLOR,
                    null,
                    InputOption::VALUE_OPTIONAL,
                    'Primary Button Border Color'
                )
            ]);

        parent::configure();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $result = [];
        $step = $input->getOption(self::ARGUMENT_STEP);
        $storeCode = $input->getOption(self::ARGUMENT_STORE);

        switch ($step) {
            case 'step-2':
                $headerVersion = $input->getOption(self::ARGUMENT_HEADER);
                $homePageVersion = $input->getOption(self::ARGUMENT_HOME_PAGE);
                $footerVersion = $input->getOption(self::ARGUMENT_FOOTER);
                $preFooter = $input->getOption(self::ARGUMENT_PRE_FOOTER);
                $result = $this->validateStep2Inputs($storeCode, $headerVersion, $homePageVersion, $footerVersion, $preFooter);

                break;
            case 'step-3':
                $categoryColumns = $input->getOption(self::ARGUMENT_CATEGORY_PAGE);
                $result = $this->validateStep3Inputs($storeCode, $categoryColumns);

                break;

            case 'step-4':
                $productPageVersion = $input->getOption(self::ARGUMENT_PRODUCT_PAGE);
                $result = $this->validateStep4Inputs($storeCode, $productPageVersion);

                break;

            case 'step-5':
                $quickView = $input->getOption(self::ARGUMENT_QUICK_VIEW);
                $quickViewBtn = $input->getOption(self::ARGUMENT_QUICK_VIEW_BTN);
                $result = $this->validateStep5Inputs($storeCode, $quickView, $quickViewBtn);

                break;

            case 'step-6':
                $quickCart = $input->getOption(self::ARGUMENT_QUICK_CART);
                $backToTop = $input->getOption(self::ARGUMENT_BACK_TO_TOP);
                $result = $this->validateStep6Inputs($storeCode, $quickCart, $backToTop);

                break;

            case 'step-7':
                $primaryBtnBgColor = $input->getOption(self::ARGUMENT_PRIMARY_BTN_BG_COLOR);
                $primaryBtnTextColor = $input->getOption(self::ARGUMENT_PRIMARY_BTN_TEXT_COLOR);
                $primaryBtnBorderColor = $input->getOption(self::ARGUMENT_PRIMARY_BTN_BORDER_COLOR);

                $result = $this->validateStep7Inputs($storeCode, $primaryBtnBgColor, $primaryBtnTextColor, $primaryBtnBorderColor);

                break;

            case 'step-8':
                $primaryBtnBgColor = $input->getOption(self::ARGUMENT_DEFAULT_BTN_BG_COLOR);
                $primaryBtnTextColor = $input->getOption(self::ARGUMENT_DEFAULT_BTN_TEXT_COLOR);
                $primaryBtnBorderColor = $input->getOption(self::ARGUMENT_DEFAULT_BTN_BORDER_COLOR);

                $result = $this->validateStep8Inputs($storeCode, $primaryBtnBgColor, $primaryBtnTextColor, $primaryBtnBorderColor);

                break;
        }

        $store = $result['store'];
        $csvData = $result['csvData'];

        $this->importCsvData($csvData, $store);
        $output->writeln('Import was finished successfully.');
    }

    /**
     * @param string $storeCode
     * @param string $headerVersion
     * @param string $homePageVersion
     * @param string $footerVersion
     * @param string $preFooter
     * @param string $categoryColumns
     * @param string $productPageVersion
     * @return array
     * @throws \Exception
     */
    protected function validateStep2Inputs($storeCode, $headerVersion, $homePageVersion, $footerVersion, $preFooter)
    {
        $demoCsvPath = $this->componentRegistrar->getPath(ComponentRegistrar::MODULE, 'WeltPixel_Command') .
            DIRECTORY_SEPARATOR . 'themeConfigurator';
        $noOptionSelected = true;
        $data = [];

        if (is_null($storeCode) || !trim($storeCode)) {
            throw new \InvalidArgumentException('Argument ' . self::ARGUMENT_STORE . ' is missing.');
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

        /** pre-footer params verifications */
        if (!is_null($preFooter) && strlen($preFooter) != 0) {
            if (!in_array($preFooter, self::PRE_FOOTER)) {
                throw new \Exception('Argument ' . self::PRE_FOOTER . ' must be one of the following: ' . implode(', ', self::PRE_FOOTER));
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
                    [self::ARGUMENT_HEADER, self::ARGUMENT_HOME_PAGE, self::ARGUMENT_PRE_FOOTER, self::ARGUMENT_FOOTER]));
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

    /**
     * @param string $storeCode
     * @param string $categoryColumns
     * @return array
     * @throws \Exception
     */
    public function validateStep3Inputs($storeCode, $categoryColumns)
    {
        $demoCsvPath = $this->componentRegistrar->getPath(ComponentRegistrar::MODULE, 'WeltPixel_Command') .
            DIRECTORY_SEPARATOR . 'themeConfigurator';
        $noOptionSelected = true;
        $data = [];

        if (is_null($storeCode) || !trim($storeCode)) {
            throw new \InvalidArgumentException('Argument ' . self::ARGUMENT_STORE . ' is missing.');
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

        if ($noOptionSelected) {
            throw new \Exception('Please specify valid options for one of the following options: ' . implode(', ',
                    [self::ARGUMENT_CATEGORY_PAGE]));
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

    /**
     * @param string $storeCode
     * @param string $productPageVersion
     * @return array
     * @throws \Exception
     */
    public function validateStep4Inputs($storeCode, $productPageVersion)
    {
        $demoCsvPath = $this->componentRegistrar->getPath(ComponentRegistrar::MODULE, 'WeltPixel_Command') .
            DIRECTORY_SEPARATOR . 'themeConfigurator';
        $noOptionSelected = true;
        $data = [];

        if (is_null($storeCode) || !trim($storeCode)) {
            throw new \InvalidArgumentException('Argument ' . self::ARGUMENT_STORE . ' is missing.');
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

        if ($noOptionSelected) {
            throw new \Exception('Please specify valid options for one of the following options: ' . implode(', ',
                    [self::ARGUMENT_PRODUCT_PAGE]));
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

    /**
     * @param string $storeCode
     * @param string $quickView
     * @param string $quickViewBtn
     * @return array
     * @throws \Exception
     */
    public function validateStep5Inputs($storeCode, $quickView, $quickViewBtn)
    {
        $demoCsvPath = $this->componentRegistrar->getPath(ComponentRegistrar::MODULE, 'WeltPixel_Command') .
            DIRECTORY_SEPARATOR . 'themeConfigurator';
        $noOptionSelected = true;
        $data = [];

        if (is_null($storeCode) || !trim($storeCode)) {
            throw new \InvalidArgumentException('Argument ' . self::ARGUMENT_STORE . ' is missing.');
        }

        /** quickView params verifications */
        if (!is_null($quickView) && strlen($quickView) != 0) {
            if (!in_array($quickView, self::QUICK_VIEW)) {
                throw new \Exception('Argument ' . self::ARGUMENT_QUICK_VIEW . ' must be one of the following: ' . implode(', ', self::QUICK_VIEW));
            }
            $noOptionSelected = false;
            $csvFile = $demoCsvPath . DIRECTORY_SEPARATOR . 'quickView' .
                DIRECTORY_SEPARATOR . $quickView . '.csv';
            $csvData = $this->csvFile->getData($csvFile);
            $data = array_merge($data, $csvData);
        }

        /** quickViewBtn params verifications */
        if (!is_null($quickViewBtn) && strlen($quickViewBtn) != 0) {
            if (!in_array($quickViewBtn, self::QUICK_VIEW_BTN)) {
                throw new \Exception('Argument ' . self::ARGUMENT_QUICK_VIEW_BTN . ' must be one of the following: ' . implode(', ', self::QUICK_VIEW_BTN));
            }
            $noOptionSelected = false;
            $csvFile = $demoCsvPath . DIRECTORY_SEPARATOR . 'quickViewBtn' .
                DIRECTORY_SEPARATOR . $quickViewBtn . '.csv';
            $csvData = $this->csvFile->getData($csvFile);
            $data = array_merge($data, $csvData);
        }

        if ($noOptionSelected) {
            throw new \Exception('Please specify valid options for one of the following options: ' . implode(', ',
                    [self::ARGUMENT_QUICK_VIEW, self::ARGUMENT_QUICK_VIEW_BTN]));
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

    /**
     * @param string $storeCode
     * @param string $quickCart
     * @param string $backToTop
     * @return array
     * @throws \Exception
     */
    public function validateStep6Inputs($storeCode, $quickCart, $backToTop)
    {
        $demoCsvPath = $this->componentRegistrar->getPath(ComponentRegistrar::MODULE, 'WeltPixel_Command') .
            DIRECTORY_SEPARATOR . 'themeConfigurator';
        $noOptionSelected = true;
        $data = [];

        if (is_null($storeCode) || !trim($storeCode)) {
            throw new \InvalidArgumentException('Argument ' . self::ARGUMENT_STORE . ' is missing.');
        }

        /** quickView params verifications */
        if (!is_null($quickCart) && strlen($quickCart) != 0) {
            if (!in_array($quickCart, self::QUICK_CART)) {
                throw new \Exception('Argument ' . self::ARGUMENT_QUICK_CART. ' must be one of the following: ' . implode(', ', self::QUICK_CART));
            }
            $noOptionSelected = false;
            $csvFile = $demoCsvPath . DIRECTORY_SEPARATOR . 'quickCart' .
                DIRECTORY_SEPARATOR . $quickCart . '.csv';
            $csvData = $this->csvFile->getData($csvFile);
            $data = array_merge($data, $csvData);
        }

        /** backToTop params verifications */
        if (!is_null($backToTop) && strlen($backToTop) != 0) {
            if (!in_array($backToTop, self::BACK_TO_TOP)) {
                throw new \Exception('Argument ' . self::ARGUMENT_BACK_TO_TOP. ' must be one of the following: ' . implode(', ', self::BACK_TO_TOP));
            }
            $noOptionSelected = false;
            $csvFile = $demoCsvPath . DIRECTORY_SEPARATOR . 'backToTop' .
                DIRECTORY_SEPARATOR . $backToTop . '.csv';
            $csvData = $this->csvFile->getData($csvFile);
            $data = array_merge($data, $csvData);
        }

        if ($noOptionSelected) {
            throw new \Exception('Please specify valid options for one of the following options: ' . implode(', ',
                    [self::ARGUMENT_QUICK_CART, self::ARGUMENT_BACK_TO_TOP]));
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

    /**
     * @param string $storeCode
     * @param string $primaryBtnBgColor
     * @param string $primaryBtnTextColor
     * @param string $primaryBtnBorderColor
     * @return array
     * @throws \Exception
     */
    public function validateStep7Inputs($storeCode, $primaryBtnBgColor, $primaryBtnTextColor, $primaryBtnBorderColor)
    {
        $demoCsvPath = $this->componentRegistrar->getPath(ComponentRegistrar::MODULE, 'WeltPixel_Command') .
            DIRECTORY_SEPARATOR . 'themeConfigurator';
        $noOptionSelected = true;
        $data = [];

        if (is_null($storeCode) || !trim($storeCode)) {
            throw new \InvalidArgumentException('Argument ' . self::ARGUMENT_STORE . ' is missing.');
        }

        /** primaryBtn colors params verifications */
        if (
            (!is_null($primaryBtnBgColor) && strlen($primaryBtnBgColor) != 0) &&
            (!is_null($primaryBtnTextColor) && strlen($primaryBtnTextColor) != 0) &&
            (!is_null($primaryBtnBorderColor) && strlen($primaryBtnBorderColor) != 0)
        ) {
            $noOptionSelected = false;
            $csvFile = $demoCsvPath . DIRECTORY_SEPARATOR . 'primaryBtnColors' .
                DIRECTORY_SEPARATOR . 'primaryBtnColors.csv';
            // write data into csv file
            $csvContent  = '';
            $csvContent .= 'weltpixel_frontend_options/primary_buttons/button____primary__background,' . $primaryBtnBgColor . ',stores' . "\n";
            $csvContent .= 'weltpixel_frontend_options/primary_buttons/button____primary__color,' . $primaryBtnTextColor . ',stores' . "\n";
            $csvContent .= 'weltpixel_frontend_options/primary_buttons/button____primary__border,' . $primaryBtnBorderColor . ',stores' . "\n";
            $csvContent .= 'weltpixel_frontend_options/primary_buttons/button____primary__hover__border,' . $primaryBtnBorderColor . ',stores' . "\n";
            // invert colors for hover
            $csvContent .= 'weltpixel_frontend_options/primary_buttons/button____primary__hover__background,' . $primaryBtnTextColor . ',stores' . "\n";
            $csvContent .= 'weltpixel_frontend_options/primary_buttons/button____primary__hover__color,' . $primaryBtnBgColor . ',stores' . "\n";

            $this->fileWrite($csvFile, $csvContent);

            $csvData = $this->csvFile->getData($csvFile);
            $data = array_merge($data, $csvData);
        }

        if ($noOptionSelected) {
            throw new \Exception('Please specify valid options for one of the following options: ' . implode(', ',
                    [self::ARGUMENT_PRIMARY_BTN_BG_COLOR, self::ARGUMENT_PRIMARY_BTN_TEXT_COLOR, self::ARGUMENT_PRIMARY_BTN_BORDER_COLOR]));
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

    /**
     * @param string $storeCode
     * @param string $defaultBtnBgColor
     * @param string $defaultBtnTextColor
     * @param string $defaultBtnBorderColor
     * @return array
     * @throws \Exception
     */
    public function validateStep8Inputs($storeCode, $defaultBtnBgColor, $defaultBtnTextColor, $defaultBtnBorderColor)
    {
        $demoCsvPath = $this->componentRegistrar->getPath(ComponentRegistrar::MODULE, 'WeltPixel_Command') .
            DIRECTORY_SEPARATOR . 'themeConfigurator';
        $noOptionSelected = true;
        $data = [];

        if (is_null($storeCode) || !trim($storeCode)) {
            throw new \InvalidArgumentException('Argument ' . self::ARGUMENT_STORE . ' is missing.');
        }

        /** defaultBtn colors params verifications */
        if (
            (!is_null($defaultBtnBgColor) && strlen($defaultBtnBgColor) != 0) &&
            (!is_null($defaultBtnTextColor) && strlen($defaultBtnTextColor) != 0) &&
            (!is_null($defaultBtnBorderColor) && strlen($defaultBtnBorderColor) != 0)
        ) {
            $noOptionSelected = false;
            $csvFile = $demoCsvPath . DIRECTORY_SEPARATOR . 'defaultBtnColors' .
                DIRECTORY_SEPARATOR . 'defaultBtnColors.csv';
            // write data into csv file
            $csvContent  = '';
            $csvContent .= 'weltpixel_frontend_options/default_buttons/button__background,' . $defaultBtnBgColor . ',stores' . "\n";
            $csvContent .= 'weltpixel_frontend_options/default_buttons/button__color,' . $defaultBtnTextColor . ',stores' . "\n";
            $csvContent .= 'weltpixel_frontend_options/default_buttons/button__border,' . $defaultBtnBorderColor . ',stores' . "\n";
            $csvContent .= 'weltpixel_frontend_options/default_buttons/button__hover__border,' . $defaultBtnBorderColor . ',stores' . "\n";
            // invert colors for hover
            $csvContent .= 'weltpixel_frontend_options/default_buttons/button__hover__background,' . $defaultBtnTextColor . ',stores' . "\n";
            $csvContent .= 'weltpixel_frontend_options/default_buttons/button__hover__color,' . $defaultBtnBgColor . ',stores' . "\n";

            $this->fileWrite($csvFile, $csvContent);

            $csvData = $this->csvFile->getData($csvFile);
            $data = array_merge($data, $csvData);
        }

        if ($noOptionSelected) {
            throw new \Exception('Please specify valid options for one of the following options: ' . implode(', ',
                    [self::ARGUMENT_DEFAULT_BTN_BG_COLOR, self::ARGUMENT_DEFAULT_BTN_TEXT_COLOR, self::ARGUMENT_DEFAULT_BTN_BORDER_COLOR]));
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

    public function fileWrite($fileName, $contents)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $filesystem = $objectManager->get('Magento\Framework\Filesystem\Directory\WriteFactory');
        $directoryCode = $this->componentRegistrar->getPath(ComponentRegistrar::MODULE, 'WeltPixel_Command');
        $writer = $filesystem->create($directoryCode, \Magento\Framework\Filesystem\DriverPool::FILE);
        $file = $writer->openFile($fileName, 'w');
        try {
            $file->lock();
            try {
                $file->write($contents);
            }
            finally {
                $file->unlock();
            }
        }
        finally {
            $file->close();
        }
    }
}