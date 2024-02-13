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
namespace Aheadworks\Helpdesk2\Test\Unit\Model\Data\Processor\Command\Automation;

use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Api\DataObjectHelper;
use Aheadworks\Helpdesk2\Api\Data\AutomationInterface;
use Aheadworks\Helpdesk2\Api\Data\AutomationInterfaceFactory;
use Aheadworks\Helpdesk2\Api\AutomationRepositoryInterface;
use Aheadworks\Helpdesk2\Model\Data\Command\Automation\Save;

/**
 * Class SaveTest
 *
 * @package Aheadworks\Helpdesk2\Test\Unit\Model\Data\Processor\Command\Automation
 */
class SaveTest extends TestCase
{
    /**
     * @var Save
     */
    private $model;

    /**
     * @var AutomationRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $automationRepositoryMock;

    /**
     * @var DataObjectHelper|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dataObjectHelperMock;

    /**
     * @var AutomationInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $automationFactoryMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp() : void
    {
        $objectManager = new ObjectManager($this);
        $this->automationFactoryMock = $this->getMockBuilder(AutomationInterfaceFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->dataObjectHelperMock = $this->getMockBuilder(DataObjectHelper::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->automationRepositoryMock = $this->getMockForAbstractClass(AutomationRepositoryInterface::class);
        $this->model = $objectManager->getObject(
            Save::class,
            [
                'dataObjectHelper' => $this->dataObjectHelperMock,
                'automationRepository' => $this->automationRepositoryMock,
                'automationFactory' => $this->automationFactoryMock
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
        $automationData = [
            AutomationInterface::NAME => 'test'
        ];
        $automationMock = $this->getMockForAbstractClass(AutomationInterface::class);
        $this->automationFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($automationMock);
        $this->dataObjectHelperMock->expects($this->once())
            ->method('populateWithArray')
            ->with($automationMock, $automationData, AutomationInterface::class)
            ->willReturn($automationMock);
        $this->automationRepositoryMock->expects($this->once())
            ->method('save')
            ->with($automationMock)
            ->willReturn($automationMock);

        $this->model->execute($automationData);
    }
}
