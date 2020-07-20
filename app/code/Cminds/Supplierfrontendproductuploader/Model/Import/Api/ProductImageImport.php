<?php
namespace Cminds\Supplierfrontendproductuploader\Model\Import\Api;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem\Io\File;
use Cminds\Supplierfrontendproductuploader\Model\Config as Config;

/**
 * Class ProductImageImport
 * assign images to passed product by image URL
 */
class ProductImageImport
{
    /**
     * @var DirectoryList
     */
    protected $directoryList;

    /**
     * @var File
     */
    protected $file;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var int
     */
    protected $imageCount = 0;

    /**
     * @param DirectoryList $directoryList
     * @param File $file
     * @param Config $config
     */
    public function __construct(
        DirectoryList $directoryList,
        File $file,
        Config $config
    ) {
        $this->directoryList = $directoryList;
        $this->file = $file;
        $this->config = $config;
    }

    /**
     * @param $product
     * @param $imageUrl
     * @param bool $visible
     * @param array $imageType
     * @return bool|string
     * @throws \Exception
     */
    public function addImageByUrl($product, $imageUrl, $visible = false, $imageType )
    {
        /** @var string $tmpDir */
        $mediaDir = $this->getMediaDir();
        /** @var string $newFileName */
        $fileExt = pathinfo(baseName($imageUrl), PATHINFO_EXTENSION);
        $newFileName = $mediaDir . DIRECTORY_SEPARATOR . $product->getId() . '-' . time() . '.' . $fileExt;
        /** read file from URL and copy it to the new destination */
        $result = $this->file->read($imageUrl, $newFileName);

        if ($result) {
            /** add saved file to the $product gallery */
            $product->addImageToMediaGallery($newFileName, $imageType, true, $visible);
        }
        return $result;
    }

    /**
     * @param $product
     * @param array $images
     * @throws \Exception
     */
    public function addImagesToProduct($product, array $images = []){

        if(count($images)){

            // get actual product images count
            $productImages = $product->getMediaGalleryImages();
            $this->imageCount = count($productImages);

            foreach ($images as $imageUrl){
                if(false === $this->canAddImage()) break;
                if( $this->addImageByUrl($product, $imageUrl, true, ['image', 'small_image', 'thumbnail']) ){
                    $this->imageCount++;
                }
            }
        }
    }

    /**
     * Check if allowed maximum of uploaded images is reached.
     * If the maximum is reached, then return false - we don't allow to upload more images.
     * In other situation return true.
     *
     * @return bool
     */
    protected function canAddImage()
    {
        $allowedMax = $this->config
            ->getProductMaximumAllowedImagesCount();

        if (!$allowedMax || $allowedMax === 0) {
            return false;
        }

        if ($this->imageCount < $allowedMax) {
            return true;
        }

        return false;
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    protected function getMediaDir()
    {
        return $this->directoryList->getPath(DirectoryList::MEDIA);
    }
}
