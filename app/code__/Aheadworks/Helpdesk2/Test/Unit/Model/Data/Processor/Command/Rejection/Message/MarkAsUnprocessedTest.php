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
use Aheadworks\Helpdesk2\Model\ResourceModel\Gateway\Email as EmailResourceModel;
use Aheadworks\Helpdesk2\Model\Data\Command\Rejection\Message\MarkAsUnprocessed;
use Aheadworks\Helpdesk2\Model\Source\Gateway\Email\Status as EmailStatus;
use Aheadworks\Helpdesk2\Model\Gateway\Email;
use Aheadworks\Helpdesk2\Model\Gateway\Email\Loader as EmailLoader;
use Aheadworks\Helpdesk2\Model\Rejection\MessageRepository;
use Aheadworks\Helpdesk2\Api\Data\RejectedMessageInterface;
use Aheadworks\Helpdesk2\Model\Rejection\Processor\Type\Email as EmailProcessor;

/**
 * Class MarkAsUnprocessedTest
 *
 * @package Aheadworks\Helpdesk2\Test\Unit\Model\Data\Processor\Rejection\Email
 */
class MarkAsUnprocessedTest extends TestCase
{
    /**
     * @var MarkAsUnprocessed
     */
    private $model;

    /**
     * @var EmailResourceModel|\PHPUnit_Framework_MockObject_MockObject
     */
    private $emailResourceMock;

    /**
     * @var EmailLoader|\PHPUnit_Framework_MockObject_MockObject
     */
    private $emailLoaderMock;

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
        $this->emailResourceMock = $this->getMockBuilder(EmailResourceModel::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->emailLoaderMock = $this->getMockBuilder(EmailLoader::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->rejectedMessageRepositoryMock = $this->getMockBuilder(MessageRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->model = $objectManager->getObject(
            MarkAsUnprocessed::class,
            [
                'emailResource' => $this->emailResourceMock,
                'emailLoader' => $this->emailLoaderMock,
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
        $emailId = 1;
        $rejectedMessageMock = $this->getMockForAbstractClass(RejectedMessageInterface::class);
        $rejectedMessageMock->expects($this->once())
            ->method('getMessageData')
            ->with(EmailProcessor::GATEWAY_EMAIL_ID)
            ->willReturn($emailId);
        $emailMock = $this->getMockBuilder(Email::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->emailLoaderMock->expects($this->once())
            ->method('loadById')
            ->with($emailId)
            ->willReturn($emailMock);
        $emailMock->expects($this->once())
            ->method('getId')
            ->willReturn($emailId);
        $emailMock->expects($this->once())
            ->method('setStatus')
            ->with(EmailStatus::UNPROCESSED)
            ->willReturnSelf();
        $emailMock->expects($this->once())
            ->method('setRejectPatternId')
            ->with(null)
            ->willReturnSelf();
        $this->emailResourceMock->expects($this->once())
            ->method('save')
            ->with($emailMock)
            ->willReturnSelf();
        $this->rejectedMessageRepositoryMock->expects($this->once())
            ->method('delete')
            ->with($rejectedMessageMock)
            ->willReturn(true);

        $this->model->execute(
            [
                'item' => $rejectedMessageMock
            ]
        );
    }
}
