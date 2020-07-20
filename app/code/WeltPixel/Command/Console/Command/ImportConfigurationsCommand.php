<?php
namespace WeltPixel\Command\Console\Command;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportConfigurationsCommand extends ImportAbstract
{
    const ARGUMENT_STORE = 'store';
    const ARGUMENT_FILE = 'file';

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('weltpixel:import:configurations')
            ->setDescription('Import Configurations From a file')
            ->setDefinition([
                new InputOption(
                    self::ARGUMENT_STORE,
                    null,
                    InputOption::VALUE_REQUIRED,
                    'Store'
                ),
                new InputOption(
                    self::ARGUMENT_FILE,
                    null,
                    InputOption::VALUE_REQUIRED,
                    'File'
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
        $csvFile = $input->getOption(self::ARGUMENT_FILE);

        $result = $this->validateInputs($storeCode, $csvFile);
        $store = $result['store'];
        $csvData = $result['csvData'];

        $this->importCsvData($csvData, $store);
        $output->writeln('Import was finished successfully.');
    }

    /**
     * @param string $storeCode
     * @param string $csvFile
     * @return array
     * @throws \Exception
     */
    protected function validateInputs($storeCode, $csvFile)
    {
        if (is_null($storeCode) || !trim($storeCode)) {
            throw new \InvalidArgumentException('Argument ' . self::ARGUMENT_STORE . ' is missing.');
        }

        if (is_null($csvFile) || !trim($csvFile)) {
            throw new \InvalidArgumentException('Argument ' . self::ARGUMENT_FILE . ' is missing.');
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

        $data = $this->csvFile->getData($csvFile);

        return [
            'store' => $store,
            'csvData' => $data
        ];
    }
}