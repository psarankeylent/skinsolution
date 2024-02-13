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
use Aheadworks\Helpdesk2\Model\Service\GatewayService;
use Aheadworks\Helpdesk2\Model\Gateway\Email\Loader as EmailLoader;
use Aheadworks\Helpdesk2\Model\Gateway\Email\Processor as EmailProcessor;
use Aheadworks\Helpdesk2\Model\ResourceModel\Gateway\Email as EmailResourceModel;
use Aheadworks\Helpdesk2\Model\Gateway\Email;

/**
 * Test for GatewayService class
 */
class GatewayServiceTest extends TestCase
{
    /**
     * @var GatewayService
     */
    private $model;

    /**
     * @var EmailLoader|\PHPUnit_Framework_MockObject_MockObject
     */
    private $emailLoaderMock;

    /**
     * @var EmailResourceModel|\PHPUnit_Framework_MockObject_MockObject
     */
    private $emailResourceMock;

    /**
     * @var EmailProcessor|\PHPUnit_Framework_MockObject_MockObject
     */
    private $emailProcessorMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp() : void
    {
        $objectManager = new ObjectManager($this);
        $this->emailLoaderMock = $this->getMockBuilder(EmailLoader::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->emailResourceMock = $this->getMockBuilder(EmailResourceModel::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->emailProcessorMock = $this->getMockBuilder(EmailProcessor::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->model = $objectManager->getObject(
            GatewayService::class,
            [
                'emailLoader' => $this->emailLoaderMock,
                'emailResource' => $this->emailResourceMock,
                'emailProcessor' => $this->emailProcessorMock
            ]
        );
    }

    /**
     * Testing of processEmails method
     */
    public function testProcessEmails()
    {
        $emailMock1 = $this->getMockBuilder(Email::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->emailLoaderMock->expects($this->once())
            ->method('loadUnprocessedEmails')
            ->willReturn([$emailMock1]);

        $this->emailProcessorMock->expects($this->once())
            ->method('process')
            ->with($emailMock1)
            ->willReturn($emailMock1);
        $this->emailResourceMock->expects($this->once())
            ->method('save')
            ->with($emailMock1)
            ->willReturnSelf();

        $this->assertSame(true, $this->model->processEmails());
    }
}
