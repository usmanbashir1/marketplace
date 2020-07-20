<?php
namespace WeltPixel\Command\Model;

use Magento\Framework\View\Asset\PreProcessor\AlternativeSource\AssetBuilder;
use Magento\Deploy\Model\Filesystem as Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;

class GenerateCss
{
    /**
     * @var AssetBuilder
     */
    protected $assetBuilder;

    /** @var \Magento\Framework\View\Asset\Repository */
    protected $assetRepo;

    /**
     * @var \Magento\Framework\App\View\Asset\Publisher
     */
    protected $assetPublisher;

    /**
     * @var \Magento\Framework\App\Filesystem\DirectoryList
     */
    protected $directoryList;

    /**
     * @var \Magento\Framework\Filesystem\Driver\File
     */
    protected $file;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var \WeltPixel\Command\Model\Storage
     */
    protected $storage;

    /**
     * @param AssetBuilder $assetBuilder
     * @param \Magento\Framework\App\View\Asset\Publisher $assetPublisher
     * @param \Magento\Framework\View\Asset\Repository $assetRepo
     * @param \Magento\Framework\App\Filesystem\DirectoryList $directoryList
     * @param \Magento\Framework\Filesystem\Driver\File $file
     * @param Filesystem $filesystem
     * @param \WeltPixel\Command\Model\Storage $storage
     */
    public function __construct(
        AssetBuilder $assetBuilder,
        \Magento\Framework\App\View\Asset\Publisher $assetPublisher,
        \Magento\Framework\View\Asset\Repository $assetRepo,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList,
        \Magento\Framework\Filesystem\Driver\File $file,
        Filesystem $filesystem,
        \WeltPixel\Command\Model\Storage $storage
    ) {
        $this->assetBuilder = $assetBuilder;
        $this->assetRepo = $assetRepo;
        $this->assetPublisher = $assetPublisher;
        $this->directoryList = $directoryList;
        $this->file = $file;
        $this->filesystem = $filesystem;
        $this->storage = $storage;
    }

    /**
     * @param string $theme Pearl/weltpixel_custom
     * @param string $locale en_US
     * @param string $storeCode
     * @return bool
     */
    public function processContent($theme, $locale, $storeCode) {

        $this->storage->setData('generation_store_code', $storeCode);

        $filesToGenerate = [
            "css/styles-l-temp.css",
            "css/styles-m-temp.css",
            "WeltPixel_CategoryPage::css/weltpixel_category_store_" . $storeCode .".css",
            "WeltPixel_CustomHeader::css/weltpixel_custom_header_" . $storeCode .".css",
            "WeltPixel_CustomFooter::css/weltpixel_custom_footer_" . $storeCode .".css",
            "WeltPixel_ProductPage::css/weltpixel_product_store_" . $storeCode .".css"
        ];

        $cssDirectories =
            [
                '/frontend/' . $theme . '/' . $locale . '/css',
                '/frontend/' . $theme . '/' . $locale . '/WeltPixel_CategoryPage/css',
                '/frontend/' . $theme . '/' . $locale . '/WeltPixel_CustomHeader/css',
                '/frontend/' . $theme . '/' . $locale . '/WeltPixel_CustomFooter/css',
                '/frontend/' . $theme . '/' . $locale . '/WeltPixel_ProductPage/css',
                '/frontend/' . $theme . '/' . $locale . '/WeltPixel_FrontendOptions/css',
            ];

        $mainDirectoryPath = $this->directoryList->getPath(DirectoryList::TMP_MATERIALIZATION_DIR);
        foreach ($cssDirectories as $directory) {
            $directoryPath = $mainDirectoryPath . $directory;
            if ($this->file->isExists($directoryPath)) {
                $this->file->deleteDirectory($directoryPath);
            }
        }

        foreach ($filesToGenerate as $file) {
            $path = $file;
            $area = 'frontend';
            $theme = $theme;
            $locale = $locale;

            $asset = $this->assetRepo->createAsset($path, [
                'area' => $area,
                'theme' => $theme,
                'locale' => $locale,
                'module' => null
            ]);

            $filePath = $this->directoryList->getPath(\Magento\Framework\App\Filesystem\DirectoryList::STATIC_VIEW)
                . DIRECTORY_SEPARATOR .$asset->getRelativeSourceFilePath() ;


            if ($this->file->isExists($filePath)) {
                $this->file->deleteFile($filePath);
            }

            $minifiedfilePath = str_replace('.css', '.min.css', $filePath);
            if ($this->file->isExists($minifiedfilePath)) {
                $this->file->deleteFile($minifiedfilePath);
            }


            /** For production mode */
            $lessFile = rtrim($filePath, 'css');
            $lessFile .= 'less';
            if ($this->file->isExists($lessFile)) {
                $this->file->deleteFile($lessFile);
            }
            $minifiedlessFile = rtrim($minifiedfilePath, 'css');
            $minifiedlessFile .= 'less';
            if ($this->file->isExists($minifiedlessFile)) {
                $this->file->deleteFile($minifiedlessFile);
            }

            $this->assetPublisher->publish($asset);

            /** only for styles-l and styles-m, as they have temporary less files, their regeneration takes longer */
            if (strpos($filePath, '-temp.css') !== false ) {
                $newPath = str_replace('-temp.css', '', $filePath);
                $newPath .= '.css';
                /** Production mode without minified css */
                if ($this->file->isExists($filePath)) {
                    copy($filePath, $newPath);
                } else {
                    /** check the production mode minified version*/
                    $filePath = str_replace('.css', '.min.css', $filePath);
                    $newPath = str_replace('.css', '.min.css', $newPath);
                    copy($filePath, $newPath);
                }
            }

            /** Gzip compression on cloud */
            if (strpos($filePath, '-temp.css') !== false ) {
                $filePath = str_replace('-temp.css', '', $filePath);
                $filePath .= '.css';
            }

            $gzippedNormalfilePath = str_replace('.css', '.css.gz', $filePath);
            $gzippedMinifiedfilePath = str_replace('.css', '.css.gz', $minifiedfilePath);

            $gzippedFilesToCheck = [
                $filePath => $gzippedNormalfilePath,
                $minifiedfilePath => $gzippedMinifiedfilePath
            ];

            foreach ($gzippedFilesToCheck as $normalfilePath => $gzippedfilePath) {
                if ($this->file->isExists($gzippedfilePath)) {
                    $this->file->deleteFile($gzippedfilePath);

                    $fileContent = $this->file->fileGetContents($normalfilePath);
                    $gzContent = gzencode($fileContent, 9);
                    $this->file->filePutContents($gzippedfilePath, $gzContent);
                }
            }
        }

        return true;
    }



}