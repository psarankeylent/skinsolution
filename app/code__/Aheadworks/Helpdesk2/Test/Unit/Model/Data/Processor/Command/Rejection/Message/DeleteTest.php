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
namespace Aheadworks\Helpdesk2\Test\Unit\Model\Data\Processor\Rejection\Message;

use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\Helpdesk2\Model\Rejection\MessageRepository;
use Aheadworks\Helpdesk2\Model\Data\Command\Rejection\Message\Delete;
use Aheadworks\Helpdesk2\Api\Data\RejectedMessageInterface;

/**
 * Class DeleteTest
 *
 * @package Aheadworks\Helpdesk2\Test\Unit\Model\Data\Processor\Rejection\Message
 */
class DeleteTest extends TestCase
{
    /**
     * @var Delete
     */
    private $model;

    /**
     * @var MessageRepository|\PHPUnit_Framework_MockObject_MockObject
     */
    private $messageRepositoryMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp() : void
    {
        $objectManager = new ObjectManager($this);
        $this->messageRepositoryMock = $this->getMockBuilder(MessageRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->model = $objectManager->getObject(
            Delete::class,
            [
                'rejectedMessageRepository' => $this->messageRepositoryMock,
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
        $this->expectExceptionMessage('Rejected email item param is required');

        $this->model->execute(
            [
                'item' => null
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
        $result = true;
        $rejectedMessageMock = $this->getMockForAbstractClass(RejectedMessageInterface::class);

        $this->messageRepositoryMock->expects($this->once())
            ->method('delete')
            ->with($rejectedMessageMock)
            ->willReturn($result);

        $this->assertSame(
            $result,
            $this->model->execute(
                [
                   'item' => $rejectedMessageMock
                ]
            )
        );
    }
}
