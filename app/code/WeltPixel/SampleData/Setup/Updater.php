<?php
namespace WeltPixel\SampleData\Setup;

use Magento\Framework\Setup;

class Updater implements Setup\SampleData\InstallerInterface
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
     * @var array
     */
    private $sliderIds;

    /**
     * @var array
     */
    private $blockIds;

    /**
     * @var array
     */
    private $blocksToCreate;

    /**
     * @var array
     */
    private $pagesToCreate;

    /**
     * @param \WeltPixel\SampleData\Model\Page $page
     * @param \WeltPixel\SampleData\Model\Block $block
     */
    public function __construct(
        \WeltPixel\SampleData\Model\Page $page,
        \WeltPixel\SampleData\Model\Block $block
    ) {
        $this->page = $page;
        $this->block = $block;
        $this->blocksToCreate = [];
        $this->pagesToCreate = [];
        $this->sliderIds = null;
        $this->blockIds = null;
    }

    /**
     * @param string $blockCsv
     * @return $this
     */
    public function setBlocksToCreate($blockCsv) {
        array_push($this->blocksToCreate, $blockCsv);
        return $this;
    }

    /**
     * @param string $pagesCsv
     * @param array $sliderIds
     * @return $this
     */
    public function setPagesToCreate($pagesCsv, $sliderIds = null, $blockIds = null) {
        array_push($this->pagesToCreate, $pagesCsv);
        $this->sliderIds = $sliderIds;
        $this->blockIds = $blockIds;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function install()
    {
        if (count($this->pagesToCreate)) {
            $this->page->install($this->pagesToCreate, $this->sliderIds, $this->blockIds);
        }
        if (count($this->blocksToCreate)) {
            $this->block->install($this->blocksToCreate);
        }

        $this->pagesToCreate = [];
        $this->blocksToCreate = [];
        $this->sliderIds = null;
        $this->blockIds = null;
    }
}