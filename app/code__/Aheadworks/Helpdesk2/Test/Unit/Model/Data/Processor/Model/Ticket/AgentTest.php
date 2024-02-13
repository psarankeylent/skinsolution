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
namespace Aheadworks\Helpdesk2\Test\Unit\Model\Data\Processor\Model\Ticket;

use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\Exception\NoSuchEntityException;
use Aheadworks\Helpdesk2\Api\Data\DepartmentInterface;
use Aheadworks\Helpdesk2\Model\Source\Department\AgentList as AgentListSource;
use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Api\DepartmentRepositoryInterface;
use Aheadworks\Helpdesk2\Model\Data\Processor\Model\Ticket\Agent;

/**
 * Class AgentTest
 *
 * @package Aheadworks\Helpdesk2\Test\Unit\Model\Data\Processor\Model\Ticket
 */
class AgentTest extends TestCase
{
    /**
     * @var Agent
     */
    private $model;

    /**
     * @var DepartmentRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $departmentRepositoryMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp() : void
    {
        $objectManager = new ObjectManager($this);
        $this->departmentRepositoryMock = $this->getMockForAbstractClass(DepartmentRepositoryInterface::class);
        $this->model = $objectManager->getObject(
            Agent::class,
            [
                'departmentRepository' => $this->departmentRepositoryMock,
            ]
        );
    }

    /**
     * Testing of prepareModelBeforeSave method
     *
     * @param int $ticketIdAgent
     * @param int $ticketId
     * @param int $primaryAgentId
     * @param int $expectedAgentId
     * @dataProvider dataProvider
     * @throws NoSuchEntityException
     */
    public function testPrepareModelBeforeSave($ticketIdAgent, $ticketId, $primaryAgentId, $expectedAgentId)
    {
        $ticketMock = $this->getMockForAbstractClass(TicketInterface::class);
        $ticketMock->expects($this->any())
            ->method('getAgentId')
            ->willReturn($ticketIdAgent);
        $ticketMock->expects($this->any())
            ->method('getEntityId')
            ->willReturn($ticketId);
        $ticketMock->expects($this->any())
            ->method('getStoreId')
            ->willReturn(1);
        $ticketMock->expects($this->any())
            ->method('getDepartmentId')
            ->willReturn(2);

        $departmentMock = $this->getMockForAbstractClass(DepartmentInterface::class);
        $this->departmentRepositoryMock->expects($this->any())
            ->method('get')
            ->willReturn($departmentMock);
        $departmentMock->expects($this->any())
            ->method('getPrimaryAgentId')
            ->willReturn($primaryAgentId);
        if ($expectedAgentId) {
            $ticketMock->expects($this->once())
                ->method('setAgentId')
                ->with($expectedAgentId)
                ->willReturnSelf();
        }

        $this->assertSame($ticketMock, $this->model->prepareModelBeforeSave($ticketMock));
    }

    /**
     * Data provider
     */
    public function dataProvider()
    {
        return [
            [4, 3, 1, null],
            [null, null, 1, 1],
            [null, null, null, AgentListSource::NOT_ASSIGNED_VALUE]
        ];
    }
}
