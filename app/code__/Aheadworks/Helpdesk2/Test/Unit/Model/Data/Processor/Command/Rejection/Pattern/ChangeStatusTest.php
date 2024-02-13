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
namespace Aheadworks\Helpdesk2\Test\Unit\Model\Data\Processor\Command\Pattern;

use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\Helpdesk2\Api\Data\RejectingPatternInterface;
use Aheadworks\Helpdesk2\Api\RejectingPatternRepositoryInterface;
use Aheadworks\Helpdesk2\Model\Data\Command\Rejection\Pattern\ChangeStatus;

/**
 * Class ChangeStatusTest
 *
 * @package Aheadworks\Helpdesk2\Test\Unit\Model\Data\Processor\Command\Pattern
 */
class ChangeStatusTest extends TestCase
{
    /**
     * @var ChangeStatus
     */
    private $model;

    /**
     * @var RejectingPatternRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $patternRepositoryMock;

    /**
     * Init mocks for tests
     *
     * @return void
     * @throws \ReflectionException
     */
    public function setUp() : void
    {
        $objectManager = new ObjectManager($this);
        $this->patternRepositoryMock = $this->getMockForAbstractClass(RejectingPatternRepositoryInterface::class);
        $this->model = $objectManager->getObject(
            ChangeStatus::class,
            [
                'patternRepository' => $this->patternRepositoryMock,
            ]
        );
    }

    /**
     * Testing of execute method on exception
     *
     * @param bool $isPatternActive
     * @param int $patternId
     * @dataProvider dataProviderOnException
     * @throws LocalizedException
     */
    public function testExecuteOnException($isPatternActive, $patternId)
    {
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('Status and ID params are required to change status');

        $this->model->execute(
            [
                RejectingPatternInterface::IS_ACTIVE => $isPatternActive,
                RejectingPatternInterface::ID => $patternId
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
     * @param bool $isPatternActive
     * @param int $patternId
     * @param int $oldValue
     * @param bool $result
     * @dataProvider dataProvider
     * @throws LocalizedException
     * @throws \ReflectionException
     */
    public function testExecute($isPatternActive, $oldValue, $patternId, $result)
    {
        $patternMock = $this->getMockForAbstractClass(RejectingPatternInterface::class);
        $this->patternRepositoryMock->expects($this->once())
            ->method('get')
            ->with($patternId)
            ->willReturn($patternMock);

        $patternMock->expects($this->once())
            ->method('getIsActive')
            ->willReturn($oldValue);

        $patternMock->expects($this->any())
            ->method('setIsActive')
            ->with($isPatternActive)
            ->willReturnSelf();

        $this->patternRepositoryMock->expects($this->any())
            ->method('save')
            ->with($patternMock)
            ->willReturn($result);

        $this->assertSame(
            $result,
            $this->model->execute(
                [
                    RejectingPatternInterface::IS_ACTIVE => $isPatternActive,
                    RejectingPatternInterface::ID => $patternId
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
