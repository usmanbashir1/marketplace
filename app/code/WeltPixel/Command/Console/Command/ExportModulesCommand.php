<?php
namespace WeltPixel\Command\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use \Magento\Store\Model\StoreManagerInterface;
use \Magento\Framework\File\Csv;
use \Magento\Framework\ObjectManagerInterface;
use \Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Module\ModuleListInterface;
use \Magento\Framework\Module\Dir\Reader;
use \Magento\Framework\Xml\Parser;


class ExportModulesCommand extends Command
{

    const ARGUMENT_STORE = 'store';
    const ARGUMENT_MODULES = 'modules';

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var Csv
     */
    protected $csvFile;

    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var ModuleListInterface
     */
    protected $moduleList;

    /**
     * @var Reader
     */
    protected $moduleDirReader;

    /**
     * @var Parser
     */
    protected $xmlParser;

    /**
     * ExportConfigurationsCommand constructor.
     * @param StoreManagerInterface $storeManager
     * @param Csv $csvFile
     * @param ObjectManagerInterface $objectManager
     * @param ScopeConfigInterface $scopeConfig
     * @param ModuleListInterface $moduleList
     * @param Reader $moduleDirReader
     * @param Parser $xmlParser
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        Csv $csvFile,
        ObjectManagerInterface $objectManager,
        ScopeConfigInterface $scopeConfig,
        ModuleListInterface $moduleList,
        Reader $moduleDirReader,
        Parser $xmlParser
    )
    {
        $this->storeManager = $storeManager;
        $this->csvFile = $csvFile;
        $this->objectManager = $objectManager;
        $this->scopeConfig = $scopeConfig;
        $this->moduleList = $moduleList;
        $this->moduleDirReader = $moduleDirReader;
        $this->xmlParser = $xmlParser;
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('weltpixel:export:modules')
            ->setDescription('Export Module(s) Configuration')
            ->setDefinition([
                new InputOption(
                    self::ARGUMENT_STORE,
                    null,
                    InputOption::VALUE_REQUIRED,
                    'Store'
                ),
                new InputOption(
                    self::ARGUMENT_MODULES,
                    null,
                    InputOption::VALUE_REQUIRED,
                    'Module(s)'
                )
            ]);

        parent::configure();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $storeCode = $input->getOption(self::ARGUMENT_STORE);
        $modulesList = $input->getOption(self::ARGUMENT_MODULES);

        $result = $this->validateInputs($storeCode, $modulesList);

        $store = $result['store'];
        $modules = $result['modules'];

        $exportedSections = $this->getSections($modules);
        $storeId = $store->getId();
        $storeCode = $store->getCode();
        $csvData = [];

        $state = $this->objectManager->get('\\Magento\\Framework\\App\\State');
        $state->setAreaCode('adminhtml');
        $configStructureData = $this->objectManager->get('\\Magento\\Config\\Model\\Config\\Structure\\Data');
        $data = $configStructureData->get();

        foreach ($data['sections'] as $sectionId => $section) {
            if (!in_array($sectionId, $exportedSections)) {
                continue;
            }
            if (!isset($section['children'])) continue;
            foreach ($section['children'] as $groupId => $group) {
                if (!isset($group['children'])) continue;
                foreach ($group['children'] as $fieldId => $field) {
                    $scope = \Magento\Framework\App\Config::SCOPE_TYPE_DEFAULT;
                    if (isset($field['showInStore']) && $field['showInStore']) {
                        $scope = \Magento\Store\Model\ScopeInterface::SCOPE_STORES;
                    } elseif (isset($field['showInWebsite']) && $field['showInWebsite']) {
                        $scope = \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITES;
                    }
                    $optionPath = $sectionId . '/' . $groupId . '/' . $fieldId;
                    $optionValue = $this->scopeConfig->getValue($optionPath, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
                    $csvData[] = [$optionPath, $optionValue, $scope];
                }
            }
        }

        $exportCsvFileName = 'weltpixel_configurations_modules_' . $storeCode . '.csv';
        $this->csvFile->saveData($exportCsvFileName, $csvData);
        $output->writeln($exportCsvFileName . ' generated successfully.');

    }

    /**
     * @param string $storeCode
     * @param string $modulesList
     * @return array
     * @throws \Exception
     */
    protected function validateInputs($storeCode, $modulesList)
    {
        if (is_null($storeCode) || !trim($storeCode)) {
            throw new \InvalidArgumentException('Argument ' . self::ARGUMENT_STORE . ' is missing.');
        }

        if (is_null($modulesList) || !trim($modulesList)) {
            throw new \InvalidArgumentException('Argument ' . self::ARGUMENT_MODULES . ' is missing.');
        }

        try {
            $store = $this->storeManager->getStore($storeCode);
        } catch (\Exception $ex) {
            throw new \Exception('Store with id or code ' . $storeCode . ' not found.');
        }

        $modules = explode(',', $modulesList);

        foreach ($modules as $module) {
            if (!$this->moduleList->has($module)) {
                throw new \Exception('Module ' . $module . ' not found.');
            }
        }

        return [
            'store' => $store,
            'modules' => $modules
        ];
    }

    /**
     * @param array $modulesList
     * @return array
     */
    protected function getSections($modulesList)
    {
        $sectionList = [];
        foreach ($modulesList as $moduleName) {
            $filePath = $this->moduleDirReader->getModuleDir('etc', $moduleName) .
                DIRECTORY_SEPARATOR . 'adminhtml' . DIRECTORY_SEPARATOR . 'system.xml';
            $domDocument = new \DOMDocument();
            $domDocument->load($filePath);
            $sections = $domDocument->getElementsByTagName('section');
            foreach ($sections as $section) {
                $sectionList[] = $section->getAttribute('id');
            }
            unset($domDocument);
        }

        return $sectionList;
    }
}