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
use Aheadworks\Helpdesk2\Model\Data\Command\Rejection\Message\Process;
use Aheadworks\Helpdesk2\Model\Rejection\Processor\Provider;
use Aheadworks\Helpdesk2\Model\Rejection\MessageRepository;
use Aheadworks\Helpdesk2\Api\Data\RejectedMessageInterface;
use Aheadworks\Helpdesk2\Model\Rejection\Processor\ProcessorInterface;

/**
 * Class ProcessTest
 *
 * @package Aheadworks\Helpdesk2\Test\Unit\Model\Data\Processor\Rejection\Message
 */
class ProcessTest extends TestCase
{
    /**
     * @var Process
     */
    private $model;

    /**
     * @var Provider|\PHPUnit_Framework_MockObject_MockObject
     */
    private $processorProviderMock;

    /**
     * @var MessageRepository|\PHPUnit_Framework_MockObject_MockObject
     */
    private $rejectedMessageRepositoryMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp() : void
    {
        $objectManager = new ObjectManager($this);
        $this->processorProviderMock = $this->getMockBuilder(Provider::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->rejectedMessageRepositoryMock = $this->getMockBuilder(MessageRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->model = $objectManager->getObject(
            Process::class,
            [
                'processorProvider' => $this->processorProviderMock,
                'rejectedMessageRepository' => $this->rejectedMessageRepositoryMock
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
        $type = 'email';
        $result = true;
        $rejectedMessageMock = $this->getMockForAbstractClass(RejectedMessageInterface::class);
        $rejectedMessageMock->expects($this->once())
            ->method('getType')
            ->willReturn($type);
        $processorMock = $this->getMockForAbstractClass(ProcessorInterface::class);
        $this->processorProviderMock->expects($this->once())
            ->method('getProcessor')
            ->with($type)
            ->willReturn($processorMock);
        $processorMock->expects($this->once())
            ->method('process')
            ->with($rejectedMessageMock)
            ->willReturn(true);
        $this->rejectedMessageRepositoryMock->expects($this->once())
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
