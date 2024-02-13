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

use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Escaper;
use Aheadworks\Helpdesk2\Model\Data\Command\Ticket\Escalate;
use Aheadworks\Helpdesk2\Api\TicketManagementInterface;

/**
 * Class EscalateTest
 *
 * @package Aheadworks\Helpdesk2\Test\Unit\Model\Data\Processor\Command\Ticket.
 */
class EscalateTest extends TestCase
{
    /**
     * @var Escalate
     */
    private $model;

    /**
     * @var TicketManagementInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $ticketManagementMock;

    /**
     * @var Escaper|\PHPUnit_Framework_MockObject_MockObject
     */
    private $escaperMock;

    /**
     * Init mocks for tests
     *
     * @return void
     * @throws \ReflectionException
     */
    public function setUp() : void
    {
        $objectManager = new ObjectManager($this);
        $this->ticketManagementMock = $this->getMockForAbstractClass(TicketManagementInterface::class);
        $this->escaperMock = $this->getMockBuilder(Escaper::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->model = $objectManager->getObject(
            Escalate::class,
            [
                'ticketManagement' => $this->ticketManagementMock,
                'escaper' => $this->escaperMock
            ]
        );
    }

    /**
     * Testing of execute method on exception
     *
     * @param \PHPUnit_Framework_MockObject_MockObject|null $ticket
     * @param string $escalationMessage
     * @dataProvider dataProviderOnException
     * @throws LocalizedException
     */
    public function testExecuteOnException($ticket, $escalationMessage)
    {
        if (!$ticket) {
            $this->expectException('InvalidArgumentException');
            $this->expectExceptionMessage('Ticket is required to be escalated');
        } elseif (!$escalationMessage) {
            $this->expectException('InvalidArgumentException');
            $this->expectExceptionMessage('Escalation message is required');
        }

        $this->model->execute(
            [
                'ticket' => $ticket,
                'escalation-message' => $escalationMessage
            ]
        );
    }

    /**
     * Data provider on exception
     */
    public function dataProviderOnException()
    {
        $ticketMock = [
            TicketInterface::ENTITY_ID => 1
        ];
        return [
            [$ticketMock, null],
            [null, 'test message'],
            [null, null]
        ];
    }

    /**
     * Testing of execute method
     *
     * @throws LocalizedException
     */
    public function testExecute()
    {
        $ticketMock = [
            TicketInterface::ENTITY_ID => 1
        ];
        $escalationMessage = 'test';
        $result = true;
        $this->ticketManagementMock->expects($this->once())
            ->method('escalateTicket')
            ->with(1)
            ->willReturn(true);
        $this->escaperMock->expects($this->once())
            ->method('escapeHtml')
            ->with($escalationMessage)
            ->willReturn($escalationMessage);

        $this->assertSame(
            $result,
            $this->model->execute(
                [
                    'ticket' => $ticketMock,
                    'escalation-message' => $escalationMessage
                ]
            )
        );
    }
}
