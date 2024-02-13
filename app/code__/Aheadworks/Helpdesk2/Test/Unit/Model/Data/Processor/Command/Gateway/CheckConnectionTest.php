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
namespace Aheadworks\Helpdesk2\Test\Unit\Model\Data\Processor\Command\Gateway;

use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Api\DataObjectHelper;
use Aheadworks\Helpdesk2\Api\Data\GatewayDataInterface;
use Aheadworks\Helpdesk2\Api\Data\GatewayDataInterfaceFactory;
use Aheadworks\Helpdesk2\Model\Gateway\Email\Connection\AuthType\DefaultModel;
use Aheadworks\Helpdesk2\Model\Data\Command\Gateway\CheckConnection;

/**
 * Class SaveTest
 *
 * @package Aheadworks\Helpdesk2\Test\Unit\Model\Data\Processor\Command\Gateway
 */
class CheckConnectionTest extends TestCase
{
    /**
     * @var CheckConnection
     */
    private $model;

    /**
     * @var GatewayDataInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $gatewayDataFactoryMock;

    /**
     * @var DataObjectHelper|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dataObjectHelperMock;

    /**
     * DefaultModel|\PHPUnit_Framework_MockObject_MockObject
     */
    private $defaultAuthModelMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp() : void
    {
        $objectManager = new ObjectManager($this);
        $this->defaultAuthModelMock = $this->getMockBuilder(DefaultModel::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->dataObjectHelperMock = $this->getMockBuilder(DataObjectHelper::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->gatewayDataFactoryMock = $this->getMockBuilder(GatewayDataInterfaceFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->model = $objectManager->getObject(
            CheckConnection::class,
            [
                'dataObjectHelper' => $this->dataObjectHelperMock,
                'gatewayDataFactory' => $this->gatewayDataFactoryMock,
                'defaultAuthModel' => $this->defaultAuthModelMock
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
        $gatewayData = [
            GatewayDataInterface::NAME => 'test'
        ];
        $gatewayMock = $this->getMockForAbstractClass(GatewayDataInterface::class);
        $this->gatewayDataFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($gatewayMock);
        $this->dataObjectHelperMock->expects($this->once())
            ->method('populateWithArray')
            ->with($gatewayMock, $gatewayData, GatewayDataInterface::class)
            ->willReturn($gatewayMock);
        $this->defaultAuthModelMock->expects($this->once())
            ->method('getConnection')
            ->with($gatewayMock);

        $this->model->execute($gatewayData);
    }
}
