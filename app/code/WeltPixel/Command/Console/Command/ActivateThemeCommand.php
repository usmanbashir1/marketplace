<?php
namespace WeltPixel\Command\Console\Command;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ActivateThemeCommand extends ImportAbstract
{
    const ARGUMENT_STORE = 'store';
    const ARGUMENT_THEME_PATH = 'themePath';

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('weltpixel:theme:activate')
            ->setDescription('Activate Theme For a store')
            ->setDefinition([
                new InputOption(
                    self::ARGUMENT_STORE,
                    null,
                    InputOption::VALUE_REQUIRED,
                    'Store'
                ),
                new InputOption(
                    self::ARGUMENT_THEME_PATH,
                    null,
                    InputOption::VALUE_REQUIRED,
                    'Theme Path'
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
        $themePath = $input->getOption(self::ARGUMENT_THEME_PATH);

        $result = $this->validateInputs($storeCode, $themePath);

        $store = $result['store'];
        $csvData = $result['csvData'];

        $this->importCsvData($csvData, $store);
        $output->writeln('Theme was activated.');
    }

    /**
     * @param string $storeCode
     * @param string $themePath
     * @return array
     * @throws \Exception
     */
    protected function validateInputs($storeCode, $themePath)
    {
        if (is_null($storeCode) || !trim($storeCode)) {
            throw new \InvalidArgumentException('Argument ' . self::ARGUMENT_STORE . ' is missing.');
        }

        if (is_null($themePath) || !trim($themePath)) {
            throw new \InvalidArgumentException('Argument ' . self::ARGUMENT_THEME_PATH . ' is missing.');
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

        /** @var \Magento\Theme\Model\ResourceModel\Theme\Collection $themeCollection */
        $themeCollection = $this->themeFactory->create();
        try {
            $theme = $themeCollection->getThemeByFullPath('frontend' . DIRECTORY_SEPARATOR . $themePath);
            if (!$theme->getId()) {
                throw new \Exception();
            }
        } catch (\Exception $ex) {
            throw new \Exception('Theme with the path ' . $themePath . ' not found.');
        }

        $data[] = [
            'design/theme/theme_id',
            $theme->getId(),
            'stores'
        ];

        return [
            'store' => $store,
            'csvData' => $data
        ];
    }
}