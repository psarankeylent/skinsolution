<?php
/**
 * Aheadworks Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://ecommerce.aheadworks.com/end-user-license-agreement/
 *
 * @package    Helpdesk2
 * @version    2.0.6
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Helpdesk2\Model\FileSystem;

use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem\Directory\WriteInterface;
use Magento\Framework\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\UrlInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class FileInfo
 *
 * @package Aheadworks\Helpdesk2\Model\FileSystem
 */
class FileInfo
{
    /**
     * @var string
     */
    const FILE_DIR = 'aw_helpdesk2/files';

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var WriteInterface
     */
    private $mediaDirectory;

    /**
     * @param Filesystem $filesystem
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Filesystem $filesystem,
        StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
        $this->filesystem = $filesystem;
    }

    /**
     * Get file url in media folder
     *
     * @param string $relativeFilePath
     * @return string
     * @throws NoSuchEntityException
     */
    public function getMediaUrl($relativeFilePath)
    {
        $file = ltrim(str_replace('\\', '/', $relativeFilePath), '/');
        $storeBaseUrl = $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);

        return $storeBaseUrl . self::FILE_DIR . '/' . $file;
    }

    /**
     * Get file size
     *
     * @param string $relativeFilePath
     * @return string
     * @throws FileSystemException
     */
    public function getFileSize($relativeFilePath)
    {
        $filePath = $this->getMediaFilePath($relativeFilePath);
        return $this->getMediaDirectory()->stat($filePath)['size'];
    }

    /**
     * Check if file exists
     *
     * @param string $relativeFilePath
     * @return bool
     * @throws FileSystemException
     */
    public function isFileExist($relativeFilePath)
    {
        $filePath = $this->getMediaFilePath($relativeFilePath);
        return $this->getMediaDirectory()->isExist($filePath);
    }

    /**
     * Get WriteInterface instance
     *
     * @return WriteInterface
     * @throws FileSystemException
     */
    public function getMediaDirectory()
    {
        if ($this->mediaDirectory === null) {
            $this->mediaDirectory = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        }
        return $this->mediaDirectory;
    }

    /**
     * Format file size
     *
     * @param string $bytes
     * @return string
     */
    public function formatFileSize($bytes)
    {
        $symbol = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
        $exp = (int)floor(log($bytes) / log(1024));
        return sprintf('%.2f ' . $symbol[$exp], $bytes / pow(1024, floor($exp)));
    }

    /**
     * Retrieve full file path
     *
     * @param string $relativeFilePath
     * @return string
     */
    public function getMediaFilePath($relativeFilePath)
    {
        return self::FILE_DIR . $relativeFilePath;
    }
}
