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
use Aheadworks\Helpdesk2\Api\GatewayRepositoryInterface;
use Aheadworks\Helpdesk2\Model\Data\Command\Gateway\Save;

/**
 * Class SaveTest
 *
 * @package Aheadworks\Helpdesk2\Test\Unit\Model\Data\Processor\Command\Gateway
 */
class SaveTest extends TestCase
{
    /**
     * @var Save
     */
    private $model;

    /**
     * @var GatewayRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $gatewayRepositoryMock;

    /**
     * @var DataObjectHelper|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dataObjectHelperMock;

    /**
     * @var GatewayDataInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $gatewayFactoryMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp() : void
    {
        $objectManager = new ObjectManager($this);
        $this->gatewayFactoryMock = $this->getMockBuilder(GatewayDataInterfaceFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->dataObjectHelperMock = $this->getMockBuilder(DataObjectHelper::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->gatewayRepositoryMock = $this->getMockForAbstractClass(GatewayRepositoryInterface::class);
        $this->model = $objectManager->getObject(
            Save::class,
            [
                'dataObjectHelper' => $this->dataObjectHelperMock,
                'gatewayRepository' => $this->gatewayRepositoryMock,
                'gatewayFactory' => $this->gatewayFactoryMock
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
        $this->gatewayFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($gatewayMock);
        $this->dataObjectHelperMock->expects($this->once())
            ->method('populateWithArray')
            ->with($gatewayMock, $gatewayData, GatewayDataInterface::class)
            ->willReturn($gatewayMock);
        $this->gatewayRepositoryMock->expects($this->once())
            ->method('save')
            ->with($gatewayMock)
            ->willReturn($gatewayMock);

        $this->model->execute($gatewayData);
    }
}
