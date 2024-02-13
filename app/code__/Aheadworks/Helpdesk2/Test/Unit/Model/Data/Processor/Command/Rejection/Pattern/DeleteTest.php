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
use Aheadworks\Helpdesk2\Model\Data\Command\Rejection\Pattern\Delete;

/**
 * Class DeleteTest
 *
 * @package Aheadworks\Helpdesk2\Test\Unit\Model\Data\Processor\Command\Pattern
 */
class DeleteTest extends TestCase
{
    /**
     * @var Delete
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
            Delete::class,
            [
                'patternRepository' => $this->patternRepositoryMock,
            ]
        );
    }

    /**
     * Testing of execute method on exception
     *
     * @throws LocalizedException
     */
    public function testExecuteOnException()
    {
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('Pattern ID param is required to remove pattern');

        $this->model->execute(
            [
                RejectingPatternInterface::ID => null
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
        $patternId = 2;
        $result = true;
        $this->patternRepositoryMock->expects($this->once())
            ->method('deleteById')
            ->with($patternId)
            ->willReturn($result);

        $this->assertSame(
            $result,
            $this->model->execute(
                [
                    RejectingPatternInterface::ID => $patternId
                ]
            )
        );
    }
}
