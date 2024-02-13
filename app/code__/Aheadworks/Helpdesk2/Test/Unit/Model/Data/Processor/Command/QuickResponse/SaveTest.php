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
use Magento\Framework\Api\DataObjectHelper;
use Aheadworks\Helpdesk2\Api\Data\QuickResponseInterface;
use Aheadworks\Helpdesk2\Api\Data\QuickResponseInterfaceFactory;
use Aheadworks\Helpdesk2\Api\QuickResponseRepositoryInterface;
use Aheadworks\Helpdesk2\Model\Data\Command\QuickResponse\Save;

/**
 * Class SaveTest
 *
 * @package Aheadworks\Helpdesk2\Test\Unit\Model\Data\Processor\Command\QuickResponse
 */
class SaveTest extends TestCase
{
    /**
     * @var Save
     */
    private $model;

    /**
     * @var QuickResponseRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $quickResponseRepositoryMock;

    /**
     * @var DataObjectHelper|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dataObjectHelperMock;

    /**
     * @var QuickResponseInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $quickResponseFactoryMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp() : void
    {
        $objectManager = new ObjectManager($this);
        $this->quickResponseFactoryMock = $this->getMockBuilder(QuickResponseInterfaceFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->dataObjectHelperMock = $this->getMockBuilder(DataObjectHelper::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->quickResponseRepositoryMock = $this->getMockForAbstractClass(QuickResponseRepositoryInterface::class);
        $this->model = $objectManager->getObject(
            Save::class,
            [
                'dataObjectHelper' => $this->dataObjectHelperMock,
                'quickResponseRepository' => $this->quickResponseRepositoryMock,
                'quickResponseFactory' => $this->quickResponseFactoryMock
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
        $quickResponseData = [
            QuickResponseInterface::TITLE => 'test'
        ];
        $quickResponseMock = $this->getMockForAbstractClass(QuickResponseInterface::class);
        $this->quickResponseFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($quickResponseMock);
        $this->dataObjectHelperMock->expects($this->once())
            ->method('populateWithArray')
            ->with($quickResponseMock, $quickResponseData, QuickResponseInterface::class)
            ->willReturn($quickResponseMock);
        $this->quickResponseRepositoryMock->expects($this->once())
            ->method('save')
            ->with($quickResponseMock)
            ->willReturn($quickResponseMock);

        $this->model->execute($quickResponseData);
    }
}
