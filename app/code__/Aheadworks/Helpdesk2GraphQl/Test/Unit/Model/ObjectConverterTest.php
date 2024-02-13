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
 * @package    Helpdesk2GraphQl
 * @version    1.0.0
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Helpdesk2GraphQl\Test\Unit\Model;

use Aheadworks\Helpdesk2\Api\Data\MessageInterface;
use Aheadworks\Helpdesk2GraphQl\Model\DataProcessor\Pool as ProcessorsPool;
use Aheadworks\Helpdesk2GraphQl\Model\ObjectConverter;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

/**
 * Class ObjectConverterTest
 *
 * @package Aheadworks\Helpdesk2GraphQl\Test\Unit\Model
 */
class ObjectConverterTest extends TestCase
{
    /**
     * @var DataObjectProcessor
     */
    private $dataObjectProcessorMock;

    /**
     * @var ProcessorsPool
     */
    private $processorsPoolMock;

    /**
     * @var ObjectConverter
     */
    private $converter;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);
        $this->dataObjectProcessorMock = $this->createMock(DataObjectProcessor::class);
        $this->processorsPoolMock = $this->createMock(ProcessorsPool::class);

        $this->converter = $objectManager->getObject(
            ObjectConverter::class,
            [
                'processorsPool' => $this->processorsPoolMock,
                'dataObjectProcessor' => $this->dataObjectProcessorMock
            ]
        );
    }

    /**
     * Test convertToArray method
     */
    public function testConvertToArray()
    {
        $messageData = [
            'id' => 1,
            'ticket_id' => 1,
            'content' => 'content',
            'type' => 'type',
            'author_name' => 'name',
            'author_email' => 'email',
            'created_at' => 'created_at',
            'attachments' => 'attachments',
        ];
        $instanceName = MessageInterface::class;
        $objectMock = $this->createConfiguredMock(MessageInterface::class, [
            'getId' => 1,
            'getTicketId' => 1,
            'getContent' => 'content',
            'getType' => 'type',
            'getAuthorName' => 'name',
            'getAuthorEmail' => 'email',
            'getCreatedAt' => 'created_at',
            'getAttachments' => 'attachments',
        ]);
        $assertData = $messageData;
        $assertData['model'] = $objectMock;

        $this->dataObjectProcessorMock->expects($this->once())
            ->method('buildOutputDataArray')
            ->with($objectMock, $instanceName)
            ->willReturn($messageData);
        $this->processorsPoolMock->expects($this->once())
            ->method('getForInstance')
            ->with($instanceName)
            ->willReturn(false);

        $this->assertEquals($assertData, $this->converter->convertToArray(
            $objectMock,
            $instanceName
        ));
    }
}
