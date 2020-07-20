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

class ExportConfigurationsCommand extends Command
{

    const ARGUMENT_STORE = 'store';

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
     * @var array
     */
    protected $sectionContainer;

    /**
     * ExportConfigurationsCommand constructor.
     * @param array $sectionContainer
     * @param StoreManagerInterface $storeManager
     * @param Csv $csvFile
     * @param ObjectManagerInterface $objectManager
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        array $sectionContainer = [],
        StoreManagerInterface $storeManager,
        Csv $csvFile,
        ObjectManagerInterface $objectManager,
        ScopeConfigInterface $scopeConfig
    )
    {
        $this->storeManager = $storeManager;
        $this->csvFile = $csvFile;
        $this->objectManager = $objectManager;
        $this->scopeConfig = $scopeConfig;
        $this->sectionContainer = $sectionContainer;
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('weltpixel:export:configurations')
            ->setDescription('Export Configurations For Theme')
            ->setDefinition([
                new InputOption(
                    self::ARGUMENT_STORE,
                    null,
                    InputOption::VALUE_REQUIRED,
                    'Store'
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
        if (is_null($storeCode) || !trim($storeCode)) {
            throw new \InvalidArgumentException('Argument ' . self::ARGUMENT_STORE . ' is missing.');
        }

        try {
            $store = $this->storeManager->getStore($storeCode);
        } catch (\Exception $ex) {
            throw new \Exception('Store with id or code ' . $storeCode . ' not found.');
        }

        $storeId = $store->getId();
        $storeCode = $store->getCode();
        $csvData = [];

        $state = $this->objectManager->get('\\Magento\\Framework\\App\\State');
        $state->setAreaCode('adminhtml');
        $configStructureData = $this->objectManager->get('\\Magento\\Config\\Model\\Config\\Structure\\Data');
        $data = $configStructureData->get();

        foreach ($data['sections'] as $sectionId => $section) {
            if (!in_array($sectionId, $this->sectionContainer)) {
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

        $exportCsvFileName = 'weltpixel_configurations_' . $storeCode . '.csv';
        $this->csvFile->saveData($exportCsvFileName, $csvData);
        $output->writeln($exportCsvFileName . ' generated successfully.');

    }
}