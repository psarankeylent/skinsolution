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
namespace Aheadworks\Helpdesk2\Controller\Adminhtml\Ticket\Attachment;

use Aheadworks\Helpdesk2\Model\FileSystem\FileUploader;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;

/**
 * Class Upload
 *
 * @package Aheadworks\Helpdesk2\Controller\Adminhtml\Ticket\Attachment
 */
class Upload extends Action
{
    /**
     * @var FileUploader
     */
    private $fileUploader;

    /**
     * @param Context $context
     * @param FileUploader $fileUploader
     */
    public function __construct(
        Context $context,
        FileUploader $fileUploader
    ) {
        parent::__construct($context);
        $this->fileUploader = $fileUploader;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        try {
            $imageId = $this->getRequest()->getParam('param_name', 'attachments');
            $result = $this->fileUploader->upload($imageId);
        } catch (\Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }

        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);
    }
}
