<?php
namespace WeltPixel\Command\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\App\State;
use Magento\Deploy\Model\Filesystem as Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;

class CleanupCommand extends Command
{
    /**
     * Object Manager
     *
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var Filesystem
     */
    protected $filesystem;


    /**
     * GenerateLessCommand constructor.
     * @param ObjectManagerInterface $objectManager
     * @param Filesystem $filesystem
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        Filesystem $filesystem
    )
    {
        $this->objectManager = $objectManager;
        $this->filesystem = $filesystem;
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('weltpixel:cleanup')
            ->setDescription('Filesystem cleanup');

        parent::configure();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $mode = $this->objectManager->create(
            'Magento\Deploy\Model\Mode',
            [
                'input' => $input,
                'output' => $output,
            ]
        );
        $currentMode = $mode->getMode() ?: State::MODE_DEFAULT;
        if ($currentMode == State::MODE_PRODUCTION) {
            throw new \Exception('This command shouldn\'t be triggered in production mode.');
        }

        $output->writeln('Cache cleanup started:');
        $output->writeln('...');

        $this->filesystem->cleanupFilesystem(
            [
                DirectoryList::CACHE,
                DirectoryList::GENERATION,
                DirectoryList::DI,
                DirectoryList::TMP_MATERIALIZATION_DIR,
                DirectoryList::STATIC_VIEW,
            ]
        );

        $output->writeln('Cache cleanup finished.');
    }
}