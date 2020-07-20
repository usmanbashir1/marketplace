<?php

namespace WeltPixel\SpeedOptimization\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use WeltPixel\SpeedOptimization\Helper\Data as SpeedHelper;
use WeltPixel\SpeedOptimization\Model\Storage;

class CoreLayoutRenderElementObserver implements ObserverInterface
{
    /**
     * @var Storage
     */
    protected $storage;

    /**
     * @var SpeedHelper
     */
    protected $speedHelper;

    /**
     * CoreLayoutRenderElementObserver constructor.
     * @param SpeedHelper $speedHelper
     * @param Storage $storage
     */
    public function __construct(
        SpeedHelper $speedHelper,
        Storage $storage
    ) {
        $this->speedHelper = $speedHelper;
        $this->storage = $storage;
    }

    /**
     * @param Observer $observer
     * @return self
     */
    public function execute(Observer $observer)
    {
        $speedOptimizationsEabled = $this->speedHelper->isEnabled();
        $jsMoveToBottomEnabled = $this->speedHelper->isJsMoveToBottomEnabled();

        if (!$speedOptimizationsEabled) {
            return $this;
        }

        if ($jsMoveToBottomEnabled) {
            $elementName = $observer->getData('element_name');

            if ($elementName != 'wp.speedoptimization.before.body.end') {
                return $this;
            }

            $transport = $observer->getData('transport');
            $html = $transport->getOutput();

            $jsFiles = [];
            if ($this->storage->getJsFiles()) {
                $jsFiles = $this->storage->getJsFiles();
            }
            $jsScripts = '';
            foreach ($jsFiles as $jsFile) {
                $jsScripts .= '<script type="text/javascript" src="' . $jsFile . '"></script>';
            }
            $inlineScripts = $inlineScripts = $this->storage->getInlineScripts();

            $html = str_replace(['##js_scripts##', '##inline_scripts##'], [$jsScripts, $inlineScripts], $html);

            $transport->setOutput($html);
        }

        return $this;
    }
}