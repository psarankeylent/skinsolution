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
namespace Aheadworks\Helpdesk2\Model\Data\Processor\Model\RejectedMessage;

use Aheadworks\Helpdesk2\Api\Data\RejectedMessageInterface;
use Aheadworks\Helpdesk2\Model\Data\Processor\Model\ProcessorInterface;
use Magento\Framework\Serialize\Serializer\Json as Serializer;

/**
 * Class JsonData
 *
 * @package Aheadworks\Helpdesk2\Model\Data\Processor\Model\RejectedMessage
 */
class JsonData implements ProcessorInterface
{
    /**
     * @var Serializer
     */
    private $jsonSerializer;

    /**
     * @param Serializer $jsonSerializer
     */
    public function __construct(
        Serializer $jsonSerializer
    ) {
        $this->jsonSerializer = $jsonSerializer;
    }

    /**
     * Prepare model before save
     *
     * @param RejectedMessageInterface $rejectedMessage
     * @return RejectedMessageInterface
     */
    public function prepareModelBeforeSave($rejectedMessage)
    {
        $rejectedMessage->setMessageData(
            $this->jsonSerializer->serialize($rejectedMessage->getMessageData())
        );

        return $rejectedMessage;
    }

    /**
     * Prepare model after load
     *
     * @param RejectedMessageInterface $rejectedMessage
     * @return RejectedMessageInterface
     */
    public function prepareModelAfterLoad($rejectedMessage)
    {
        try {
            $dataArray = $this->jsonSerializer->unserialize($rejectedMessage->getMessageData());
        } catch (\Exception $exception) {
            $dataArray = [];
        }

        $rejectedMessage->setMessageData($dataArray);

        return $rejectedMessage;
    }
}
