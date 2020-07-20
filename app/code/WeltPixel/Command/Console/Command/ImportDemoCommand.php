<?php
namespace WeltPixel\Command\Console\Command;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Framework\Component\ComponentRegistrar;

class ImportDemoCommand extends ImportAbstract
{
    const ARGUMENT_STORE = 'store';
    const ARGUMENT_DEMO_VERSION = 'demoVersion';

    const DEMO_VERSIONS = [
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

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('weltpixel:import:demo')
            ->setDescription('Import a Demo version')
            ->setDefinition([
                new InputOption(
                    self::ARGUMENT_STORE,
                    null,
                    InputOption::VALUE_REQUIRED,
                    'Store'
                ),
                new InputOption(
                    self::ARGUMENT_DEMO_VERSION,
                    null,
                    InputOption::VALUE_REQUIRED,
                    'Demo Version'
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
        $demoVersion = $input->getOption(self::ARGUMENT_DEMO_VERSION);

        $result = $this->validateInputs($storeCode, $demoVersion);

        $store = $result['store'];
        $csvData = $result['csvData'];

        $this->importCsvData($csvData, $store);
        $output->writeln('Import was finished successfully.');
    }

    /**
     * @param string $storeCode
     * @param string $demoVersion
     * @return array
     * @throws \Exception
     */
    protected function validateInputs($storeCode, $demoVersion)
    {
        if (is_null($storeCode) || !trim($storeCode)) {
            throw new \InvalidArgumentException('Argument ' . self::ARGUMENT_STORE . ' is missing.');
        }

        if (is_null($demoVersion) || !trim($demoVersion)) {
            throw new \InvalidArgumentException('Argument ' . self::ARGUMENT_DEMO_VERSION . ' is missing.');
        }

        if (!in_array($demoVersion, self::DEMO_VERSIONS)) {
            throw new \Exception('Argument ' . self::ARGUMENT_DEMO_VERSION . ' must be one of the following: ' . implode(', ', self::DEMO_VERSIONS));
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

        $demoCsvPath = $this->componentRegistrar->getPath(ComponentRegistrar::MODULE, 'WeltPixel_Command');
        $demoCsvPathTheme = $demoCsvPath . DIRECTORY_SEPARATOR . 'demoConfigTheme' .
            DIRECTORY_SEPARATOR . 'demo_theme_' . $demoVersion . '.csv';

        $demoCsvPathModules = $demoCsvPath . DIRECTORY_SEPARATOR . 'demoConfigModules' .
            DIRECTORY_SEPARATOR . 'demo_modules_' . $demoVersion . '.csv';

        $dataTheme = $this->csvFile->getData($demoCsvPathTheme);
        $dataModules = $this->csvFile->getData($demoCsvPathModules);

        $data = array_merge($dataTheme, $dataModules);

        return [
            'store' => $store,
            'csvData' => $data
        ];
    }
}