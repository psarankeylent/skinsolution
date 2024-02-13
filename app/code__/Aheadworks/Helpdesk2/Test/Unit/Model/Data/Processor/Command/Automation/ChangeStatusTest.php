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
namespace Aheadworks\Helpdesk2\Test\Unit\Model\Data\Processor\Command\Automation;

use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\Helpdesk2\Api\Data\AutomationInterface;
use Aheadworks\Helpdesk2\Api\AutomationRepositoryInterface;
use Aheadworks\Helpdesk2\Model\Data\Command\Automation\ChangeStatus;

/**
 * Class ChangeStatusTest
 *
 * @package Aheadworks\Helpdesk2\Test\Unit\Model\Data\Processor\Command\Automation
 */
class ChangeStatusTest extends TestCase
{
    /**
     * @var ChangeStatus
     */
    private $model;

    /**
     * @var AutomationRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $automationRepositoryMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp() : void
    {
        $objectManager = new ObjectManager($this);
        $this->automationRepositoryMock = $this->getMockForAbstractClass(AutomationRepositoryInterface::class);
        $this->model = $objectManager->getObject(
            ChangeStatus::class,
            [
                'automationRepository' => $this->automationRepositoryMock,
            ]
        );
    }

    /**
     * Testing of execute method on exception
     *
     * @param bool $isAutomationActive
     * @param int $automationId
     * @dataProvider dataProviderOnException
     * @throws LocalizedException
     */
    public function testExecuteOnException($isAutomationActive, $automationId)
    {
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('Status and ID params are required to change status');

        $this->model->execute(
            [
                AutomationInterface::IS_ACTIVE => $isAutomationActive,
                AutomationInterface::ID => $automationId
            ]
        );
    }

    /**
     * Data provider on exception
     */
    public function dataProviderOnException()
    {
        return [
            [null, 1],
            [false, null],
            [null, null]
        ];
    }

    /**
     * Testing of execute method
     *
     * @param bool $isAutomationActive
     * @param int $automationId
     * @param int $oldValue
     * @param bool $result
     * @dataProvider dataProvider
     * @throws LocalizedException
     */
    public function testExecute($isAutomationActive, $oldValue, $automationId, $result)
    {
        $automationMock = $this->getMockForAbstractClass(AutomationInterface::class);
        $this->automationRepositoryMock->expects($this->once())
            ->method('get')
            ->with($automationId)
            ->willReturn($automationMock);

        $automationMock->expects($this->once())
            ->method('getIsActive')
            ->willReturn($oldValue);

        $automationMock->expects($this->any())
            ->method('setIsActive')
            ->with($isAutomationActive)
            ->willReturnSelf();

        $this->automationRepositoryMock->expects($this->any())
            ->method('save')
            ->with($automationMock)
            ->willReturn($result);

        $this->assertSame(
            $result,
            $this->model->execute(
                [
                    AutomationInterface::IS_ACTIVE => $isAutomationActive,
                    AutomationInterface::ID => $automationId
                ]
            )
        );
    }

    /**
     * Data provider
     */
    public function dataProvider()
    {
        return [
            [true, false, 1, true],
            [true, true, 1, false],
            [false, false, 1, false],
            [false, true, 1, false]
        ];
    }
}
