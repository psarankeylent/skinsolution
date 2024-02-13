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
namespace Aheadworks\Bup\Model\DataProcessor\UserProfilePostDataProcessor;

use Aheadworks\Bup\Model\DataProcessor\PostDataProcessorInterface;
use Aheadworks\Bup\Model\UserProfile\ImageUploader;
use Aheadworks\Bup\Api\Data\UserProfileInterface;
use Aheadworks\Bup\Block\Adminhtml\User\Edit\Tab\FormElementApplier;

/**
 * Class Image
 *
 * @package Aheadworks\Bup\Model\DataProcessor\UserProfilePostDataProcessor
 */
class Image implements PostDataProcessorInterface
{
    /**
     * @var ImageUploader
     */
    private $imageUploader;

    /**
     * @param ImageUploader $imageUploader
     */
    public function __construct(
        ImageUploader $imageUploader
    ) {
        $this->imageUploader = $imageUploader;
    }

    /**
     * @inheritdoc
     */
    public function prepareEntityData($data)
    {
        $result = $this->imageUploader->saveImageToMediaFolder(FormElementApplier::IMAGE_INPUT_NAME);
        if (isset($result['file'])) {
            $data[UserProfileInterface::IMAGE] = $result['file'];
        }
        if (isset($data[UserProfileInterface::IMAGE . '_delete'])) {
            $data[UserProfileInterface::IMAGE] = '';
        }

        return $data;
    }
}
