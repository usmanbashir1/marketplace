<?php
namespace WeltPixel\Command\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Framework\ObjectManagerInterface;

class GenerateLessCommand extends Command
{
    /**
     * @var array
     */
    protected $generationContainer;

    /**
     * Object Manager
     *
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var \Magento\Framework\App\State
     */
    protected $state;

    /**
     * GenerateLessCommand constructor.
     * @param array $generationContainer
     * @param ObjectManagerInterface $objectManager
     * @param \Magento\Framework\App\State $state
     */
    public function __construct(
        array $generationContainer = [],
        ObjectManagerInterface $objectManager,
        \Magento\Framework\App\State $state
    )
    {
        $this->state = $state;
        try {
            $area = $this->state->getAreaCode();
            if (!$area) {
                $this->state->setAreaCode(\Magento\Framework\App\Area::AREA_ADMINHTML);
            }
        } catch (\Exception $ex) {

        }
        $this->generationContainer = $generationContainer;
        $this->objectManager = $objectManager;
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('weltpixel:less:generate')
            ->setDescription('Generate Less For Theme Modules');

        parent::configure();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Started Less Generation:');
        $output->writeln('...');

        $observer = $this->objectManager->get('\Magento\Framework\Event\Observer');
        foreach ($this->generationContainer as $key => $item) {
            try {
                $item->execute($observer);
                $output->writeln('<info>' . $key . ' module less was generated successfully.</info>');
            } catch (\Exception $ex) {
                $output->writeln('<error>' . $key . ' module less was not generated. ' . $ex->getMessage() . '</error>');
            }
        }

        $output->writeln('Finished Less Generation.');
    }

    /**
     * @return array
     */
    public function getGenerationContainer() {
        return $this->generationContainer;
    }
}