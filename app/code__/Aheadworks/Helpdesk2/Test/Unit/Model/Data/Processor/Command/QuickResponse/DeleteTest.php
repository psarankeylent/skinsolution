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
use Aheadworks\Helpdesk2\Model\Data\Command\QuickResponse\Delete;

/**
 * Class DeleteTest
 *
 * @package Aheadworks\Helpdesk2\Test\Unit\Model\Data\Processor\Command\QuickResponse
 */
class DeleteTest extends TestCase
{
    /**
     * @var Delete
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
            Delete::class,
            [
                'quickResponseRepository' => $this->quickResponseRepositoryMock,
            ]
        );
    }

    /**
     * Testing of execute method on exception
     * @throws LocalizedException
     */
    public function testExecuteOnException()
    {
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('ID param is required to delete response');

        $this->model->execute(
            [
                QuickResponseInterface::ID => null
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
        $quickResponseId = 2;
        $result = true;
        $this->quickResponseRepositoryMock->expects($this->once())
            ->method('deleteById')
            ->with($quickResponseId)
            ->willReturn($result);

        $this->assertSame(
            $result,
            $this->model->execute(
                [
                    QuickResponseInterface::ID => $quickResponseId
                ]
            )
        );
    }
}
