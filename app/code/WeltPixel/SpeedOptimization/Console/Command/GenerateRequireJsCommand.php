<?php
namespace WeltPixel\SpeedOptimization\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use WeltPixel\Backend\Helper\Utility as UtilityHelper;
use WeltPixel\SpeedOptimization\Helper\Bundling as BundlingHelper;

class GenerateRequireJsCommand extends Command
{

    /**
     * @var UtilityHelper
     */
    protected $utilityHelper;

    /**
     * @var BundlingHelper
     */
    protected $bundlingHelper;

    /**
     * GenerateRequireJsCommand constructor.
     * @param UtilityHelper $utilityHelper
     * @param BundlingHelper $bundlingHelper
     */
    public function __construct(
        UtilityHelper $utilityHelper,
        BundlingHelper $bundlingHelper
    ) {
        parent::__construct();
        $this->utilityHelper = $utilityHelper;
        $this->bundlingHelper = $bundlingHelper;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('weltpixel:requirejs:generate')
            ->setDescription('Generate RequireJs Bundle File');

        parent::configure();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Started RequireJs Bundling Generation:');
        $output->writeln('...');

        $themesLocales = $this->utilityHelper->getStoreThemesLocales();
        $themesLocales = array_keys($themesLocales);

        $frontendPath = $this->bundlingHelper->getFrontendPath();
        foreach ($themesLocales as $path) {
            $sourceDir  = $frontendPath . $path;
            $destinationDir  = $sourceDir . '_tmp';
            if (!file_exists($sourceDir)) {
                $output->writeln(__('There was no static content found for: ' . $path));
                continue;
            }
            if (!file_exists($destinationDir)) {
                mkdir($destinationDir, 0775);
                $this->bundlingHelper->copyDirectory($sourceDir, $destinationDir);
                $output->writeln(__('Prepared content for: ' . $path));
            } else {
                $output->writeln(__('The content was already created for: ' . $path));
                continue;
            }
        }

        $output->writeln('...');

        $generationOptions = $this->bundlingHelper->getGenerationOptions($themesLocales);

        $result = [];
        foreach ($generationOptions as $options) {
            if (file_exists($options['destinationPath'])) {
                $result[] = __("The %1 file was already added.", $options['buildFileName']);
            } else {
                @copy($options['buildPath'], $options['destinationPath']);
                $result[] = __("The %1 file is ready.", $options['buildFileName']);
            }
            $result = array_values(array_unique($result));
        }
        foreach ($result as $msg) {
            $output->writeln($msg);
        }

        $output->writeln('...');

        $output->writeln('Execute the following CLI SSH commands from your project\'s root path. Note that require.js needs to be installed for the commands to work. More details about the requirements can be found in the Speed Optimization module documentation.');
        $output->writeln('');
        $commandIterator = 1;
        foreach ($themesLocales as $path) {
            $output->writeln('<info>' . __('Command') . ' #' . $commandIterator . '</info>');
            $output->writeln('<info>' . 'node_modules/requirejs/bin/r.js -o pub/static/' . $generationOptions[$path]['buildFileName'] . ' baseUrl=pub/static/frontend/'
                . $path . '_tmp dir=pub/static/frontend/' . $path . '</info>');
            $output->writeln('');
            $commandIterator +=1;
        }
    }

}
