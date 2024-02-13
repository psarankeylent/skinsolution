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
namespace Aheadworks\Helpdesk2\Test\Unit\Model\Data\Processor\Command\Ticket;

use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Model\Data\Command\Ticket\RemoveAttachments;
use Aheadworks\Helpdesk2\Model\Source\Ticket\Status as TicketStatus;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket as TicketResource;
use Aheadworks\Helpdesk2\Model\FileSystem\Writer as FileSystemWriter;
use Aheadworks\Helpdesk2\Model\FileSystem\FileInfo;
use Aheadworks\Helpdesk2\Api\Data\MessageAttachmentInterface;

/**
 * Class RemoveAttachmentsTest
 *
 * @package Aheadworks\Helpdesk2\Test\Unit\Model\Data\Processor\Command\Ticket
 */
class RemoveAttachmentsTest extends TestCase
{
    /**
     * @var RemoveAttachments
     */
    private $model;

    /**
     * @var TicketResource|\PHPUnit_Framework_MockObject_MockObject
     */
    private $ticketResourceMock;

    /**
     * @var FileSystemWriter|\PHPUnit_Framework_MockObject_MockObject
     */
    private $fileSystemWriterMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp() : void
    {
        $objectManager = new ObjectManager($this);
        $this->ticketResourceMock = $this->getMockBuilder(TicketResource::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->fileSystemWriterMock = $this->getMockBuilder(FileSystemWriter::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->model = $objectManager->getObject(
            RemoveAttachments::class,
            [
                'ticketResource' => $this->ticketResourceMock,
                'fileSystemWriter' => $this->fileSystemWriterMock,
            ]
        );
    }

    /**
     * Testing of execute method on exception
     *
     * @throws LocalizedException
     */
    public function testExecuteOnException()
    {
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('Ticket entity ID param is required to remove attachments');

        $this->model->execute(
            [
                TicketInterface::ENTITY_ID => null
            ]
        );
    }

    /**
     * Testing of execute method
     *
     * @throws LocalizedException
     */
    public function testExecute()
    {
        $ticketEntityId = 1;
        $attachData = [
            [
                MessageAttachmentInterface::FILE_PATH => 'test'
            ]
        ];

        $this->ticketResourceMock->expects($this->once())
            ->method('getTicketAttachments')
            ->with($ticketEntityId)
            ->willReturn($attachData);
        $this->fileSystemWriterMock->expects($this->once())
            ->method('removeFileFromMedia')
            ->with(FileInfo::FILE_DIR, 'test')
            ->willReturn(true);

        $this->assertSame(
            true,
            $this->model->execute(
                [
                    TicketInterface::ENTITY_ID => $ticketEntityId
                ]
            )
        );
    }
}
