<?php
namespace WeltPixel\SampleData\Setup;

use Magento\Framework\Setup;

class Installer implements Setup\SampleData\InstallerInterface
{

    /**
     * @var \WeltPixel\SampleData\Model\Page
     */
    private $page;

    /**
     * @var \WeltPixel\SampleData\Model\Block
     */
    private $block;

    /**
     * @var \WeltPixel\SampleData\Model\Owl
     */
    private $owl;

    /**
     * @param \WeltPixel\SampleData\Model\Page $page
     * @param \WeltPixel\SampleData\Model\Block $block
     * @param \WeltPixel\SampleData\Model\Owl $owl
     */
    public function __construct(
        \WeltPixel\SampleData\Model\Page $page,
        \WeltPixel\SampleData\Model\Block $block,
        \WeltPixel\SampleData\Model\Owl $owl
    ) {
        $this->page = $page;
        $this->block = $block;
        $this->owl = $owl;
    }

    /**
     * {@inheritdoc}
     */
    public function install()
    {
        $result = $this->owl->install();
        $this->page->install(['WeltPixel_SampleData::fixtures/pages/pages.csv'], $result['slider_id']);
        $this->block->install(['WeltPixel_SampleData::fixtures/blocks/blocks.csv']);
    }
}