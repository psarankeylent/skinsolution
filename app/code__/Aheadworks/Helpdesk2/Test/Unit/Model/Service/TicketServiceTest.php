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
namespace Aheadworks\Helpdesk2\Test\Unit\Model\Service;

use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\Helpdesk2\Model\Service\TicketService;
use Aheadworks\Helpdesk2\Api\MessageRepositoryInterface;
use Aheadworks\Helpdesk2\Api\TicketRepositoryInterface;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket as TicketResource;
use Aheadworks\Helpdesk2\Model\Ticket\Detector;
use Aheadworks\Helpdesk2\Api\Data\MessageInterface;
use Aheadworks\Helpdesk2\Api\Data\TicketInterface;

/**
 * Test for TicketService class
 */
class TicketServiceTest extends TestCase
{
    /**
     * @var TicketService
     */
    private $model;

    /**
     * @var TicketRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $ticketRepositoryMock;

    /**
     * @var MessageRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $messageRepositoryMock;

    /**
     * @var TicketResource|\PHPUnit_Framework_MockObject_MockObject
     */
    private $ticketResourceMock;

    /**
     * @var Detector|\PHPUnit_Framework_MockObject_MockObject
     */
    private $detectorMock;

    /**
     * Init mocks for tests
     *
     * @return void
     * @throws \ReflectionException
     */
    public function setUp() : void
    {
        $objectManager = new ObjectManager($this);
        $this->ticketRepositoryMock = $this->getMockForAbstractClass(TicketRepositoryInterface::class);
        $this->messageRepositoryMock = $this->getMockForAbstractClass(MessageRepositoryInterface::class);
        $this->ticketResourceMock = $this->getMockBuilder(TicketResource::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->detectorMock = $this->getMockBuilder(Detector::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->model = $objectManager->getObject(
            TicketService::class,
            [
                'ticketRepository' => $this->ticketRepositoryMock,
                'messageRepository' => $this->messageRepositoryMock,
                'ticketResource' => $this->ticketResourceMock,
                'detector' => $this->detectorMock
            ]
        );
    }

    /**
     * Testing of createNewTicket method
     */
    public function testCreateNewTicket()
    {
        $this->ticketResourceMock->expects($this->once())
            ->method('beginTransaction')
            ->willReturnSelf();
        $ticketMock = $this->getMockForAbstractClass(TicketInterface::class);
        $this->ticketRepositoryMock->expects($this->once())
            ->method('save')
            ->with($ticketMock)
            ->willReturn($ticketMock);
        $messageMock = $this->getMockForAbstractClass(MessageInterface::class);
        $ticketId = 3;
        $ticketMock->expects($this->once())
            ->method('getEntityId')
            ->willReturn($ticketId);
        $messageMock->expects($this->once())
            ->method('setTicketId')
            ->with($ticketId)
            ->willReturnSelf();
        $this->messageRepositoryMock->expects($this->once())
            ->method('save')
            ->with($messageMock)
            ->willReturn($messageMock);
        $this->detectorMock->expects($this->once())
            ->method('detect')
            ->with(['ticket' => $ticketMock, 'message' => $messageMock], Detector::NEW_TICKET_TYPE)
            ->willReturn(true);
        $this->ticketResourceMock->expects($this->once())
            ->method('commit')
            ->willReturnSelf();

        $this->assertSame(true, $this->model->createNewTicket($ticketMock, $messageMock));
    }

    /**
     * Testing of create new ticket method on exception
     */
    public function testCreateNewTicketOnException()
    {
        $message = 'Exception message.';
        $this->expectException('Magento\Framework\Exception\LocalizedException');
        $this->expectExceptionMessage($message);
        $exception = new \Exception($message);

        $this->ticketResourceMock->expects($this->once())
            ->method('beginTransaction')
            ->willReturnSelf();
        $ticketMock = $this->getMockForAbstractClass(TicketInterface::class);
        $messageMock = $this->getMockForAbstractClass(MessageInterface::class);
        $this->ticketRepositoryMock->expects($this->once())
            ->method('save')
            ->with($ticketMock)
            ->willThrowException($exception);
        $this->ticketResourceMock->expects($this->once())
            ->method('rollBack')
            ->willReturnSelf();

        $this->model->createNewTicket($ticketMock, $messageMock);
    }

    /**
     * Testing of updateTicket method
     */
    public function testUpdateTicket()
    {
        $this->ticketResourceMock->expects($this->once())
            ->method('beginTransaction')
            ->willReturnSelf();
        $ticketMock = $this->getMockForAbstractClass(TicketInterface::class);
        $oldTicketMock = $this->getMockForAbstractClass(TicketInterface::class);
        $ticketId = 44;
        $ticketMock->expects($this->once())
            ->method('getEntityId')
            ->willReturn($ticketId);
        $this->ticketRepositoryMock->expects($this->once())
            ->method('getById')
            ->with($ticketId, true)
            ->willReturn($oldTicketMock);
        $this->ticketRepositoryMock->expects($this->once())
            ->method('save')
            ->with($ticketMock)
            ->willReturn($ticketMock);
        $this->detectorMock->expects($this->once())
            ->method('detect')
            ->with(['new_ticket' => $ticketMock, 'old_ticket' => $oldTicketMock], Detector::TICKET_UPDATED_TYPE)
            ->willReturn(true);
        $this->ticketResourceMock->expects($this->once())
            ->method('commit')
            ->willReturnSelf();

        $this->assertSame(true, $this->model->updateTicket($ticketMock));
    }

    /**
     * Testing of update method on exception
     */
    public function testUpdateTicketOnException()
    {
        $message = 'Exception message.';
        $this->expectException('Magento\Framework\Exception\LocalizedException');
        $this->expectExceptionMessage($message);
        $exception = new \Exception($message);

        $this->ticketResourceMock->expects($this->once())
            ->method('beginTransaction')
            ->willReturnSelf();
        $ticketMock = $this->getMockForAbstractClass(TicketInterface::class);
        $ticketId = 44;
        $ticketMock->expects($this->once())
            ->method('getEntityId')
            ->willReturn($ticketId);
        $this->ticketRepositoryMock->expects($this->once())
            ->method('getById')
            ->with($ticketId, true)
            ->willThrowException($exception);
        $this->ticketResourceMock->expects($this->once())
            ->method('rollBack')
            ->willReturnSelf();

        $this->model->updateTicket($ticketMock);
    }

    /**
     * Testing of createNewMessage method
     */
    public function testCreateNewMessage()
    {
        $this->ticketResourceMock->expects($this->once())
            ->method('beginTransaction')
            ->willReturnSelf();
        $messageMock = $this->getMockForAbstractClass(MessageInterface::class);
        $this->messageRepositoryMock->expects($this->once())
            ->method('save')
            ->with($messageMock)
            ->willReturn($messageMock);
        $ticketMock = $this->getMockForAbstractClass(TicketInterface::class);
        $ticketId = 3;
        $messageMock->expects($this->once())
            ->method('getTicketId')
            ->willReturn($ticketId);
        $this->ticketRepositoryMock->expects($this->once())
            ->method('getById')
            ->with($ticketId)
            ->willReturn($ticketMock);

        $this->detectorMock->expects($this->once())
            ->method('detect')
            ->with(['ticket' => $ticketMock, 'message' => $messageMock], Detector::NEW_MESSAGE_TYPE)
            ->willReturn(true);
        $this->ticketResourceMock->expects($this->once())
            ->method('commit')
            ->willReturnSelf();

        $this->assertSame(true, $this->model->createNewMessage($messageMock));
    }

    /**
     * Testing of create new message method on exception
     */
    public function testCreateNewMessageOnException()
    {
        $message = 'Exception message.';
        $this->expectException('Magento\Framework\Exception\LocalizedException');
        $this->expectExceptionMessage($message);
        $exception = new \Exception($message);

        $this->ticketResourceMock->expects($this->once())
            ->method('beginTransaction')
            ->willReturnSelf();
        $messageMock = $this->getMockForAbstractClass(MessageInterface::class);
        $this->messageRepositoryMock->expects($this->once())
            ->method('save')
            ->with($messageMock)
            ->willThrowException($exception);

        $this->ticketResourceMock->expects($this->once())
            ->method('rollBack')
            ->willReturnSelf();

        $this->model->createNewMessage($messageMock);
    }

    /**
     * Testing of escalateTicket method
     */
    public function testEscalateTicketMessage()
    {
        $ticketId = 3;
        $message = 'Ticket escalated';

        $this->ticketResourceMock->expects($this->once())
            ->method('beginTransaction')
            ->willReturnSelf();
        $ticketMock = $this->getMockForAbstractClass(TicketInterface::class);
        $this->ticketRepositoryMock->expects($this->once())
            ->method('getById')
            ->with($ticketId)
            ->willReturn($ticketMock);
        $this->detectorMock->expects($this->once())
            ->method('detect')
            ->with(['ticket' => $ticketMock, 'escalation-message' => $message], Detector::TICKET_ESCALATED_TYPE)
            ->willReturn(true);
        $this->ticketResourceMock->expects($this->once())
            ->method('commit')
            ->willReturnSelf();

        $this->assertSame(true, $this->model->escalateTicket($ticketId, $message));
    }

    /**
     * Testing of escalate message method on exception
     */
    public function testEscalateTicketOnException()
    {
        $ticketId = 3;
        $message = 'Ticket escalated';
        $exceptionMessage = 'Exception message.';
        $this->expectException('Magento\Framework\Exception\LocalizedException');
        $this->expectExceptionMessage($exceptionMessage);
        $exception = new \Exception($exceptionMessage);

        $this->ticketResourceMock->expects($this->once())
            ->method('beginTransaction')
            ->willReturnSelf();
        $this->ticketRepositoryMock->expects($this->once())
            ->method('getById')
            ->with($ticketId)
            ->willThrowException($exception);
        $this->ticketResourceMock->expects($this->once())
            ->method('rollBack')
            ->willReturnSelf();

        $this->model->escalateTicket($ticketId, $message);
    }
}
