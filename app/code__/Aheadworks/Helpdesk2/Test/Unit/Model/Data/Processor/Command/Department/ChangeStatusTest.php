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
namespace Aheadworks\Helpdesk2\Test\Unit\Model\Data\Processor\Command\Department;

use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\Helpdesk2\Api\Data\DepartmentInterface;
use Aheadworks\Helpdesk2\Api\DepartmentRepositoryInterface;
use Aheadworks\Helpdesk2\Model\Data\Command\Department\ChangeStatus;

/**
 * Class ChangeStatusTest
 *
 * @package Aheadworks\Helpdesk2\Test\Unit\Model\Data\Processor\Command\Department
 */
class ChangeStatusTest extends TestCase
{
    /**
     * @var ChangeStatus
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
            ChangeStatus::class,
            [
                'departmentRepository' => $this->departmentRepositoryMock,
            ]
        );
    }

    /**
     * Testing of execute method on exception
     *
     * @param bool $isDepartmentActive
     * @param int $departmentId
     * @dataProvider dataProviderOnException
     * @throws LocalizedException
     */
    public function testExecuteOnException($isDepartmentActive, $departmentId)
    {
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('Status and ID params are required to change status');

        $this->model->execute(
            [
                DepartmentInterface::IS_ACTIVE => $isDepartmentActive,
                DepartmentInterface::ID => $departmentId
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
     * @param bool $isDepartmentActive
     * @param int $departmentId
     * @param int $oldValue
     * @param bool $result
     * @dataProvider dataProvider
     * @throws LocalizedException
     */
    public function testExecute($isDepartmentActive, $oldValue, $departmentId, $result)
    {
        $departmentMock = $this->getMockForAbstractClass(DepartmentInterface::class);
        $this->departmentRepositoryMock->expects($this->once())
            ->method('get')
            ->with($departmentId)
            ->willReturn($departmentMock);

        $departmentMock->expects($this->once())
            ->method('getIsActive')
            ->willReturn($oldValue);

        $departmentMock->expects($this->any())
            ->method('setIsActive')
            ->with($isDepartmentActive)
            ->willReturnSelf();

        $this->departmentRepositoryMock->expects($this->any())
            ->method('save')
            ->with($departmentMock)
            ->willReturn($result);

        $this->assertSame(
            $result,
            $this->model->execute(
                [
                    DepartmentInterface::IS_ACTIVE => $isDepartmentActive,
                    DepartmentInterface::ID => $departmentId
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
