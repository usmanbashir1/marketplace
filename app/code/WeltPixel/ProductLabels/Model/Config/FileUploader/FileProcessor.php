<?php
namespace WeltPixel\ProductLabels\Model\Config\FileUploader;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Filesystem\Directory\WriteInterface;
use Magento\Framework\UrlInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class FileProcessor
{
    /**
     * @var UploaderFactory
     */
    protected $uploaderFactory;


    /**
     * Media Directory object (writable).
     *
     * @var WriteInterface
     */
    protected $mediaDirectory;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var string
     */
    const FILE_DIR = 'weltpixel/productlabels';

    /**
     * FileProcessor constructor.
     * @param UploaderFactory $uploaderFactory
     * @param Filesystem $filesystem
     * @param StoreManagerInterface $storeManager
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function __construct(
        UploaderFactory $uploaderFactory,
        Filesystem $filesystem,
        StoreManagerInterface $storeManager
    ) {
        $this->uploaderFactory = $uploaderFactory;
        $this->storeManager = $storeManager;
        $this->mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
    }

    /**
     * Save file to temp media directory
     *
     * @param  string $fileId
     * @return array
     */
    public function saveToTmp($fileId)
    {
        try {
            $result = $this->save($fileId, $this->getAbsoluteTmpMediaPath());
            $result['url'] = $this->getTmpMediaUrl($result['file']);
        } catch (\Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }
        return $result;
    }


    /**
     * @param $data
     * @param string $entity
     * @return string
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function saveToPath($data, $entity = 'product')
    {
        $tmpPath = $this->getAbsoluteTmpMediaPath() . DIRECTORY_SEPARATOR . $data['file'];
        $destinationPath = $this->getAbsoluteDestinationMediaPath() . DIRECTORY_SEPARATOR . $entity . DIRECTORY_SEPARATOR . $data['file'];
        $this->mediaDirectory->renameFile($tmpPath, $destinationPath);

        return self::FILE_DIR . DIRECTORY_SEPARATOR . $entity . DIRECTORY_SEPARATOR . $data['file'];
    }

    /**
     * @param $file
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getImageDetails($file) {
        $imageUrl = $this->getFinalMediaUrl($file);

        $imageDetails = @getimagesize($imageUrl);

        $result = [];
        $result['width'] = $imageDetails[0];
        $result['height'] = $imageDetails[1];
        $result['type'] = $imageDetails['mime'];
        $result['name'] = basename($file);
        $result['existingImage'] = $file;
        $result['url'] = $imageUrl;
        $result['previewType'] = 'image';
        $result['size'] = @filesize($this->mediaDirectory->getAbsolutePath() . $file);

        return $result;
    }

    /**
     * Retrieve absolute temp media path
     *
     * @return string
     */
    protected function getAbsoluteTmpMediaPath()
    {
        return $this->mediaDirectory->getAbsolutePath('tmp/' . self::FILE_DIR);
    }

    /**
     * Retrieve absolute destination media path
     *
     * @return string
     */
    protected function getAbsoluteDestinationMediaPath()
    {
        return $this->mediaDirectory->getAbsolutePath(self::FILE_DIR);
    }

    /**
     * @param $file
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function getTmpMediaUrl($file)
    {
        return $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA)
            . 'tmp/' . self::FILE_DIR . '/' . $this->prepareFile($file);
    }

    /**
     * @param $file
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getFinalMediaUrl($file)
    {
        return $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA)
         . $this->prepareFile($file);
    }

    /**
     * Prepare file
     *
     * @param string $file
     * @return string
     */
    protected function prepareFile($file)
    {
        return ltrim(str_replace('\\', '/', $file), '/');
    }

    /**
     * @param $fileId
     * @param $destination
     * @return array
     * @throws \Exception
     */
    protected function save($fileId, $destination)
    {
        $uploader = $this->uploaderFactory->create(['fileId' => $fileId]);
        $uploader->setAllowRenameFiles(true);
        $uploader->setFilesDispersion(false);
        $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);

        $result = $uploader->save($destination);

        return $result;
    }
}
