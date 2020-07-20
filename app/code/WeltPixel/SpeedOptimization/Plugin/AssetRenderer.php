<?php

namespace WeltPixel\SpeedOptimization\Plugin;

use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\HTTP\Header as HttpHeader;
use Magento\Framework\View\Page\Config\RendererInterface;
use WeltPixel\SpeedOptimization\Helper\Data as SpeedHelper;
use WeltPixel\SpeedOptimization\Model\Storage;

/**
 * Class AssetRenderer
 * @package WeltPixel\SpeedOptimization\Plugin
 */
class AssetRenderer
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
     * @var  HttpRequest
     */
    protected $request;

    /**
     * AssetRenderer constructor.
     * @param SpeedHelper $speedHelper
     * @param HttpRequest $request
     * @param Storage $storage
     */
    public function __construct(
        SpeedHelper $speedHelper,
        HttpRequest $request,
        Storage $storage
    ) {
        $this->speedHelper = $speedHelper;
        $this->request = $request;
        $this->storage = $storage;
    }

    /**
     * @param RendererInterface $subject
     * @param $result
     * @param array $resultGroups
     * @return string|string[]|null
     */
    public function afterRenderAssets(RendererInterface $subject, $result, $resultGroups = [])
    {
        $speedOptimizationsEabled = $this->speedHelper->isEnabled();
        $jsMoveToBottomEnabled = $this->speedHelper->isJsMoveToBottomEnabled();
        $cssPreloadEnabled = $this->speedHelper->isCssPreloadEnabled();
        $cssPreloadEnabledForAll = $this->speedHelper->isCssPreloadEnabledForAll();
        $isPreloadNotSupported = $this->speedHelper->checkifBrowserPreloadNotSupported();

        $result = preg_replace_callback('/<link.*?\/>/is', function ($matches) {
            return str_replace("&", '&amp;', $matches[0]);
        }, $result);

        try {
            $xml = new \SimpleXMLElement('<head>' . $result . '</head>');
        } catch (\Exception $ex) {
            return $result;
        }

        if (!$speedOptimizationsEabled) {
            return $result;
        }

        $ignoredPreloadRequestPaths  = [
            'weltpixel_quickview'
        ];

        if (!$isPreloadNotSupported && $cssPreloadEnabled &&
            !$this->request->isAjax() && !in_array($this->request->getModuleName(), $ignoredPreloadRequestPaths)) {
            $cssFilesResult = $this->getCssDeclarations($xml);
            $cssPreload = '';
            foreach ($cssFilesResult as $item) {
                if (!$cssPreloadEnabledForAll && ($item['media'] == 'all')) {
                    $cssPreload .= '<link  rel="stylesheet" type="text/css"  media="all" href="' . $item['css'] . '" />';
                } else {
                    $cssPreload .= '<link as="style" rel="preload" type="text/css" media="' . $item['media'] . '" href="' . $item['css'] . '" onload="this.rel=\'stylesheet\'" />';
                    $cssPreload .= '<noscript><link href="' . $item['css'] . '" media="' . $item['media'] . '" rel="stylesheet" type="text/css"></noscript>';
                }
            }
            $result = trim(preg_replace('/<link(.*)rel="stylesheet" .*?\/>/', '', $result));
            $result = $cssPreload . $result;
        }

        if ($jsMoveToBottomEnabled && !$this->request->isAjax()) {
            $jsFilesResult = $this->getJsDeclarations($xml);
            $this->storage->setJsFiles($jsFilesResult);
            $footerScripts = '';
            $result = preg_replace_callback('/<script.*?>.*?<\/script>/is', function ($matches) use (&$footerScripts) {
                $footerScripts .= $matches[0];
                return '';
            }, $result);
        }

        $result = $this->speedHelper->minifyHtml($result);

        return $result;
    }

    /**
     * @param $object
     * @param string $attribute
     * @return bool|string
     */
    public function getAttributeFromLink($object, $attribute)
    {
        if (isset($object[$attribute])) {
            return (string)$object[$attribute];
        }

        return false;
    }

    /**
     * @param \SimpleXMLElement $xml
     * @return array
     */
    protected function getCssDeclarations($xml)
    {
        $cssFilesResult = [];
        $cssFiles = $xml->xpath('link[@rel="stylesheet"]');
        foreach ($cssFiles as $node) {
            $css = $this->getAttributeFromLink($node, 'href');
            if (!$css) {
                $css = 'all';
            }
            $media = $this->getAttributeFromLink($node, 'media');
            if ($css) {
                $cssFilesResult[] = [
                    'css' => $css,
                    'media' => $media
                ];
            }
        }
        return $cssFilesResult;
    }

    /**
     * @param \SimpleXMLElement $xml
     * @return array
     */
    public function getJsDeclarations($xml)
    {
        $jsFilesResult = [];
        $jsFiles = $xml->xpath('script[@type="text/javascript"]');
        foreach ($jsFiles as $node) {
            $js = $this->getAttributeFromLink($node, 'src');
            $jsFilesResult[] = $js;
        }
        return $jsFilesResult;
    }
}
