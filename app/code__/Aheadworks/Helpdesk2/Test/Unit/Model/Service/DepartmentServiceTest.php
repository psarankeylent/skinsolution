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
use Aheadworks\Helpdesk2\Model\Department\Search\Builder as SearchBuilder;
use Aheadworks\Helpdesk2\Api\GatewayRepositoryInterface;
use Aheadworks\Helpdesk2\Model\Gateway\ProcessorPool;
use Aheadworks\Helpdesk2\Model\Service\DepartmentService;
use Aheadworks\Helpdesk2\Model\Source\Gateway\Type as GatewayType;
use Aheadworks\Helpdesk2\Model\Gateway\Processor\ProcessorInterface;
use Aheadworks\Helpdesk2\Api\Data\DepartmentInterface;
use Aheadworks\Helpdesk2\Api\Data\GatewayDataInterface;

/**
 * Test for DepartmentService class
 */
class DepartmentServiceTest extends TestCase
{
    /**
     * @var DepartmentService
     */
    private $model;

    /**
     * @var SearchBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    private $searchBuilderMock;

    /**
     * @var GatewayRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $gatewayRepositoryMock;

    /**
     * @var ProcessorPool|\PHPUnit_Framework_MockObject_MockObject
     */
    private $processorPoolMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp() : void
    {
        $objectManager = new ObjectManager($this);
        $this->searchBuilderMock = $this->getMockBuilder(SearchBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->gatewayRepositoryMock = $this->getMockForAbstractClass(GatewayRepositoryInterface::class);
        $this->processorPoolMock = $this->getMockBuilder(ProcessorPool::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->model = $objectManager->getObject(
            DepartmentService::class,
            [
                'searchBuilder' => $this->searchBuilderMock,
                'gatewayRepository' => $this->gatewayRepositoryMock,
                'processorPool' => $this->processorPoolMock
            ]
        );
    }

    /**
     * Testing of processGateways method
     */
    public function testProcessGateways()
    {
        $gatewayId = 1;
        $departmentMock1 = $this->getMockForAbstractClass(DepartmentInterface::class);
        $departmentMock2 = $this->getMockForAbstractClass(DepartmentInterface::class);
        $this->searchBuilderMock->expects($this->once())
            ->method('addIsActiveFilter')
            ->willReturnSelf();
        $this->searchBuilderMock->expects($this->once())
            ->method('searchDepartments')
            ->willReturn([$departmentMock1, $departmentMock2]);
        $departmentMock1->expects($this->once())
            ->method('getGatewayIds')
            ->willReturn([]);
        $departmentMock2->expects($this->once())
            ->method('getGatewayIds')
            ->willReturn([$gatewayId]);

        $gatewayMock = $this->getMockForAbstractClass(GatewayDataInterface::class);
        $this->gatewayRepositoryMock->expects($this->once())
            ->method('get')
            ->with($gatewayId)
            ->willReturn($gatewayMock);
        $gatewayMock->expects($this->once())
            ->method('getIsActive')
            ->willReturn(true);
        $gatewayMock->expects($this->once())
            ->method('getType')
            ->willReturn(GatewayType::EMAIL);
        $processorMock = $this->getMockForAbstractClass(ProcessorInterface::class);
        $this->processorPoolMock->expects($this->once())
            ->method('getProcessor')
            ->with(GatewayType::EMAIL)
            ->willReturn($processorMock);
        $processorMock->expects($this->once())
            ->method('process')
            ->with($gatewayMock);

        $this->assertSame(true, $this->model->processGateways());
    }
}
