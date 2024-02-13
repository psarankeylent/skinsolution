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
 * @package    Bup
 * @version    1.0.2
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Bup\Model\UserProfile;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\ReadInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class ImageInfo
 *
 * @package Aheadworks\Bup\Model\UserProfile
 */
class ImageInfo
{
    /**
     * Relative media directory for image storage
     */
    const FILE_DIR = 'aw_bup/images';

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var ReadInterface
     */
    private $mediaDirectory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param Filesystem $filesystem
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Filesystem $filesystem,
        StoreManagerInterface $storeManager
    ) {
        $this->filesystem = $filesystem;
        $this->storeManager = $storeManager;
    }

    /**
     * Get file url in media folder
     *
     * @param string $imagePath
     * @return string
     * @throws NoSuchEntityException
     */
    public function getMediaUrl($imagePath)
    {
        $file = ltrim(str_replace('\\', '/', $imagePath), '/');
        $storeBaseUrl = $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);

        return $storeBaseUrl . self::FILE_DIR . '/' . $file;
    }

    /**
     * Get ReadInterface instance
     *
     * @return ReadInterface
     */
    public function getMediaDirectory()
    {
        if ($this->mediaDirectory === null) {
            $this->mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
        }

        return $this->mediaDirectory;
    }
}
