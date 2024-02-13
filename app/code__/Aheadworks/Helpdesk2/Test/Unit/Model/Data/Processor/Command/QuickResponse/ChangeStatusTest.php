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
namespace Aheadworks\Helpdesk2\Test\Unit\Model\Data\Processor\Command\QuickResponse;

use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\Helpdesk2\Api\Data\QuickResponseInterface;
use Aheadworks\Helpdesk2\Api\QuickResponseRepositoryInterface;
use Aheadworks\Helpdesk2\Model\Data\Command\QuickResponse\ChangeStatus;

/**
 * Class ChangeStatusTest
 *
 * @package Aheadworks\Helpdesk2\Test\Unit\Model\Data\Processor\Command\QuickResponse
 */
class ChangeStatusTest extends TestCase
{
    /**
     * @var ChangeStatus
     */
    private $model;

    /**
     * @var QuickResponseRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $quickResponseRepositoryMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp() : void
    {
        $objectManager = new ObjectManager($this);
        $this->quickResponseRepositoryMock = $this->getMockForAbstractClass(QuickResponseRepositoryInterface::class);
        $this->model = $objectManager->getObject(
            ChangeStatus::class,
            [
                'quickResponseRepository' => $this->quickResponseRepositoryMock,
            ]
        );
    }

    /**
     * Testing of execute method on exception
     *
     * @param bool $isQuickResponseActive
     * @param int $quickResponseId
     * @dataProvider dataProviderOnException
     * @throws LocalizedException
     */
    public function testExecuteOnException($isQuickResponseActive, $quickResponseId)
    {
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('Status and ID params are required to change status');

        $this->model->execute(
            [
                QuickResponseInterface::IS_ACTIVE => $isQuickResponseActive,
                QuickResponseInterface::ID => $quickResponseId
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
     * @param bool $isQuickResponseActive
     * @param int $quickResponseId
     * @param int $oldValue
     * @param bool $result
     * @dataProvider dataProvider
     * @throws LocalizedException
     */
    public function testExecute($isQuickResponseActive, $oldValue, $quickResponseId, $result)
    {
        $quickResponseMock = $this->getMockForAbstractClass(QuickResponseInterface::class);
        $this->quickResponseRepositoryMock->expects($this->once())
            ->method('get')
            ->with($quickResponseId)
            ->willReturn($quickResponseMock);

        $quickResponseMock->expects($this->once())
            ->method('getIsActive')
            ->willReturn($oldValue);

        $quickResponseMock->expects($this->any())
            ->method('setIsActive')
            ->with($isQuickResponseActive)
            ->willReturnSelf();

        $this->quickResponseRepositoryMock->expects($this->any())
            ->method('save')
            ->with($quickResponseMock)
            ->willReturn($result);

        $this->assertSame(
            $result,
            $this->model->execute(
                [
                    QuickResponseInterface::IS_ACTIVE => $isQuickResponseActive,
                    QuickResponseInterface::ID => $quickResponseId
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
