<?php

namespace WeltPixel\SpeedOptimization\Helper;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context as HelperContext;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\Component\ComponentRegistrarInterface;
use Magento\Framework\Module\Dir\Reader as DirReader;

/**
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Bundling extends AbstractHelper
{
    /**
     * @var DirectoryList
     */
    protected $directoryList;

    /**
     * @var ProductMetadataInterface
     */
    protected $productMetadata;

    /**
     * @var ComponentRegistrarInterface
     */
    protected $componentRegistrar;

    /**
     * @var DirReader
     */
    protected $moduleDirReader;


    /**
     * Data constructor.
     * @param HelperContext $context
     * @param DirectoryList $directoryList
     * @param ProductMetadataInterface $productMetadata
     * @param ComponentRegistrarInterface $componentRegistrar
     * @param DirReader $moduleDirReader
     */
    public function __construct(
        HelperContext $context,
        DirectoryList $directoryList,
        ProductMetadataInterface $productMetadata,
        ComponentRegistrarInterface $componentRegistrar,
        DirReader $moduleDirReader
    ) {
        parent::__construct($context);
        $this->directoryList = $directoryList;
        $this->productMetadata = $productMetadata;
        $this->componentRegistrar = $componentRegistrar;
        $this->moduleDirReader = $moduleDirReader;
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function getFrontendPath()
    {
        return $this->directoryList->getPath(DirectoryList::STATIC_VIEW) . DIRECTORY_SEPARATOR . 'frontend' . DIRECTORY_SEPARATOR;
    }

    /**
     * @return string
     */
    public function getBuildJsFile()
    {
        $buildJsFile = 'advancedbundling_build.js';
        $magentoVersion = $this->productMetadata->getVersion();
        if (version_compare($magentoVersion, '2.3.5', '<')) {
            $buildJsFile = 'advancedbundling_build_2_3_4.js';
        }
        if (version_compare($magentoVersion, '2.3.4', '<')) {
            $buildJsFile = 'advancedbundling_build_2_3_3.js';
        }
        if (version_compare($magentoVersion, '2.3.3', '<')) {
            $buildJsFile = 'advancedbundling_build_2_3_2.js';
        }
        if (version_compare($magentoVersion, '2.3.0', '<')) {
            $buildJsFile = 'advancedbundling_build_2_2_x.js';
        }
        if (version_compare($magentoVersion, '2.2.6', '<=')) {
            $buildJsFile = 'advancedbundling_build_2_2_6.js';
        }

        return $buildJsFile;
    }

    public function getGenerationOptions($themesLocales)
    {
        $generationOptions = [];

        $buildJsFile = $this->getBuildJsFile();

        foreach ($themesLocales as $path) {
            $pathOptions = explode('/', $path);
            $themeComponentName = 'frontend' . DIRECTORY_SEPARATOR . $pathOptions[0] . DIRECTORY_SEPARATOR . $pathOptions[1];
            $themePath = $this->componentRegistrar->getPath(\Magento\Framework\Component\ComponentRegistrar::THEME, $themeComponentName);
            $buildFileName = 'advancedbundling_build.js';

            $buildJsPath = $this->moduleDirReader->getModuleDir('', 'WeltPixel_SpeedOptimization')
                . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . $buildJsFile;

            $customBuildJsPath = $themePath . DIRECTORY_SEPARATOR . 'WeltPixel_SpeedOptimization' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . $buildFileName;

            if (file_exists($customBuildJsPath)) {
                $buildFileName = 'advancedbundling_build_' . $pathOptions[0] . '_' . $pathOptions[1] . '.js';
                $buildJsPath = $customBuildJsPath;
            }

            $destinationPath = $this->directoryList->getPath(DirectoryList::STATIC_VIEW) . DIRECTORY_SEPARATOR . $buildFileName;

            $generationOptions[$path] = [
                'destinationPath'=> $destinationPath,
                'buildPath' => $buildJsPath,
                'buildFileName' => $buildFileName
            ];
        }

        return $generationOptions;

    }

    /**
     * @param $source
     * @param $dest
     * @return bool
     */
    public function copyDirectory($source, $dest)
    {
        $sourceHandle = opendir($source);

        if (!$sourceHandle) {
            return false;
        }

        while ($file = readdir($sourceHandle)) {
            if ($file == '.' || $file == '..') {
                continue;
            }

            if (is_dir($source . '/' . $file)) {
                if (!file_exists($dest . '/' . $file)) {
                    @mkdir($dest . '/' . $file, 0775);
                }
                $this->copyDirectory($source . '/' . $file, $dest . '/' . $file);
            } else {
                @copy($source . '/' . $file, $dest . '/' . $file);
            }
        }

        return true;
    }

}
