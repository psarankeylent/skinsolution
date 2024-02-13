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

use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\MediaStorage\Model\File\Uploader;

/**
 * Class FileUploader
 *
 * @package Aheadworks\Helpdesk2\Model\FileSystem
 */
class FileUploader
{
    /**
     * @var FileInfo
     */
    private $fileInfo;

    /**
     * @var UploaderFactory
     */
    private $uploaderFactory;

    /**
     * @param FileInfo $fileInfo
     * @param UploaderFactory $uploaderFactory
     */
    public function __construct(
        FileInfo $fileInfo,
        UploaderFactory $uploaderFactory
    ) {
        $this->fileInfo = $fileInfo;
        $this->uploaderFactory = $uploaderFactory;
    }

    /**
     * @inheritdoc
     */
    public function upload($file)
    {
        try {
            $result = ['file' => '', 'size' => '', 'name' => '', 'path' => '', 'type' => ''];
            $mediaDirectory = $this->fileInfo->getMediaDirectory()->getAbsolutePath(FileInfo::FILE_DIR);

            /** @var Uploader $uploader */
            $uploader = $this->uploaderFactory->create(['fileId' => $file]);
            $uploader
                ->setAllowRenameFiles(true)
                ->setFilesDispersion(true);

            $result = array_intersect_key($uploader->save($mediaDirectory), $result);
            $result['url'] = $this->fileInfo->getMediaUrl($result['file']);
        } catch (\Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }

        return $result;
    }
}
