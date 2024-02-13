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
use Magento\Framework\Api\DataObjectHelper;
use Aheadworks\Helpdesk2\Api\Data\MessageInterface;
use Aheadworks\Helpdesk2\Api\Data\MessageInterfaceFactory;
use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Api\Data\TicketInterfaceFactory;
use Aheadworks\Helpdesk2\Model\Data\Command\Ticket\Reply;
use Aheadworks\Helpdesk2\Api\TicketRepositoryInterface;
use Aheadworks\Helpdesk2\Api\TicketManagementInterface;

/**
 * Class ReplyTest
 *
 * @package Aheadworks\Helpdesk2\Test\Unit\Model\Data\Processor\Command\Ticket
 */
class ReplyTest extends TestCase
{
    /**
     * @var Reply
     */
    private $model;

    /**
     * @var DataObjectHelper|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dataObjectHelperMock;

    /**
     * @var TicketInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $ticketFactoryMock;

    /**
     * @var MessageInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $messageFactoryMock;

    /**
     * @var TicketRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $ticketRepositoryMock;

    /**
     * @var TicketManagementInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $ticketManagementMock;

    /**
     * Init mocks for tests
     *
     * @return void
     * @throws \ReflectionException
     */
    public function setUp() : void
    {
        $objectManager = new ObjectManager($this);
        $this->dataObjectHelperMock = $this->getMockBuilder(DataObjectHelper::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->ticketFactoryMock = $this->getMockBuilder(TicketInterfaceFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->messageFactoryMock = $this->getMockBuilder(MessageInterfaceFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->ticketRepositoryMock = $this->getMockForAbstractClass(TicketRepositoryInterface::class);
        $this->ticketManagementMock = $this->getMockForAbstractClass(TicketManagementInterface::class);
        $this->model = $objectManager->getObject(
            Reply::class,
            [
                'dataObjectHelper' => $this->dataObjectHelperMock,
                'ticketRepository' => $this->ticketRepositoryMock,
                'ticketManagement' => $this->ticketManagementMock,
                'ticketFactory' => $this->ticketFactoryMock,
                'messageFactory' => $this->messageFactoryMock,
            ]
        );
    }

    /**
     * Testing of execute method
     *
     * @throws LocalizedException
     * @throws \ReflectionException
     */
    public function testExecute()
    {
        $ticketEntityId = 22;
        $ticketData = [
            TicketInterface::ENTITY_ID => $ticketEntityId,
            TicketInterface::SUBJECT => 'test',
            MessageInterface::CONTENT => 'message_content'
        ];
        $ticketMock = $this->getMockForAbstractClass(TicketInterface::class);
        $this->ticketRepositoryMock->expects($this->once())
            ->method('getById')
            ->with($ticketEntityId)
            ->willReturn($ticketMock);

        $messageMock = $this->getMockForAbstractClass(MessageInterface::class);
        $this->messageFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($messageMock);
        $this->dataObjectHelperMock->expects($this->exactly(2))
            ->method('populateWithArray')
            ->withConsecutive(
                [$ticketMock, $ticketData, TicketInterface::class],
                [$messageMock, $ticketData, MessageInterface::class]
            )->willReturnOnConsecutiveCalls($ticketMock, $messageMock);

        $ticketMock->expects($this->exactly(2))
            ->method('getEntityId')
            ->willReturn($ticketEntityId);
        $messageMock->expects($this->exactly(2))
            ->method('setTicketId')
            ->with($ticketEntityId)
            ->willReturnSelf();

        $this->ticketManagementMock->expects($this->once())
            ->method('updateTicket')
            ->with($ticketMock)
            ->willReturn(true);
        $this->ticketManagementMock->expects($this->once())
            ->method('createNewMessage')
            ->with($messageMock)
            ->willReturn(true);

        $this->assertSame($ticketMock, $this->model->execute($ticketData));
    }
}
