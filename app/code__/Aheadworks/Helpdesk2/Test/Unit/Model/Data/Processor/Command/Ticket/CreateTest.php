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
use Aheadworks\Helpdesk2\Api\TicketManagementInterface;
use Aheadworks\Helpdesk2\Model\Data\Command\Ticket\Create;

/**
 * Class CreateTest
 *
 * @package Aheadworks\Helpdesk2\Test\Unit\Model\Data\Processor\Command\Ticket
 */
class CreateTest extends TestCase
{
    /**
     * @var Create
     */
    private $model;

    /**
     * @var DataObjectHelper|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dataObjectHelperMock;

    /**
     * @var TicketManagementInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $ticketManagementMock;

    /**
     * @var TicketInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $ticketFactoryMock;

    /**
     * @var MessageInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $messageFactoryMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp() : void
    {
        $objectManager = new ObjectManager($this);
        $this->ticketManagementMock = $this->getMockForAbstractClass(TicketManagementInterface::class);
        $this->dataObjectHelperMock = $this->getMockBuilder(DataObjectHelper::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->ticketFactoryMock = $this->getMockBuilder(TicketInterfaceFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->messageFactoryMock = $this->getMockBuilder(MessageInterfaceFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->model = $objectManager->getObject(
            Create::class,
            [
                'dataObjectHelper' => $this->dataObjectHelperMock,
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
     */
    public function testExecute()
    {
        $ticketData = [
            TicketInterface::SUBJECT => 'test',
            MessageInterface::CONTENT => 'message_content'
        ];
        $ticketMock = $this->getMockForAbstractClass(TicketInterface::class);
        $this->ticketFactoryMock->expects($this->once())
            ->method('create')
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

        $this->ticketManagementMock->expects($this->once())
            ->method('createNewTicket')
            ->with($ticketMock, $messageMock)
            ->willReturn(true);

        $this->assertSame($ticketMock, $this->model->execute($ticketData));
    }
}
