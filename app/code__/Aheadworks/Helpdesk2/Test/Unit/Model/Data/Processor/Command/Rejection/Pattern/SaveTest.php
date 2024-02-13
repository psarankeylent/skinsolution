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
use Magento\Framework\Api\DataObjectHelper;
use Aheadworks\Helpdesk2\Api\Data\RejectingPatternInterface;
use Aheadworks\Helpdesk2\Api\Data\RejectingPatternInterfaceFactory;
use Aheadworks\Helpdesk2\Api\RejectingPatternRepositoryInterface;
use Aheadworks\Helpdesk2\Model\Data\Command\Rejection\Pattern\Save;

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
     * @var RejectingPatternRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $patternRepositoryMock;

    /**
     * @var DataObjectHelper|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dataObjectHelperMock;

    /**
     * @var RejectingPatternInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $patternFactoryMock;

    /**
     * Init mocks for tests
     *
     * @return void
     * @throws \ReflectionException
     */
    public function setUp() : void
    {
        $objectManager = new ObjectManager($this);
        $this->patternFactoryMock = $this->getMockBuilder(RejectingPatternInterfaceFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->dataObjectHelperMock = $this->getMockBuilder(DataObjectHelper::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->patternRepositoryMock = $this->getMockForAbstractClass(RejectingPatternRepositoryInterface::class);
        $this->model = $objectManager->getObject(
            Save::class,
            [
                'dataObjectHelper' => $this->dataObjectHelperMock,
                'patternRepository' => $this->patternRepositoryMock,
                'patternFactory' => $this->patternFactoryMock
            ]
        );
    }

    /**s
     * Testing of execute method
     *
     * @throws LocalizedException
     * @throws \ReflectionException
     */
    public function testExecute()
    {
        $patternData = [
            RejectingPatternInterface::TITLE => 'test'
        ];
        $patternMock = $this->getMockForAbstractClass(RejectingPatternInterface::class);
        $this->patternFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($patternMock);
        $this->dataObjectHelperMock->expects($this->once())
            ->method('populateWithArray')
            ->with($patternMock, $patternData, RejectingPatternInterface::class)
            ->willReturn($patternMock);
        $this->patternRepositoryMock->expects($this->once())
            ->method('save')
            ->with($patternMock)
            ->willReturn($patternMock);

        $this->model->execute($patternData);
    }
}
