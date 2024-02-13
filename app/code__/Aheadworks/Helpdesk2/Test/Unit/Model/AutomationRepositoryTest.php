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
namespace Aheadworks\Helpdesk2\Test\Unit\Model;

use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Aheadworks\Helpdesk2\Api\Data\AutomationSearchResultsInterface;
use Aheadworks\Helpdesk2\Model\Automation;
use Aheadworks\Helpdesk2\Model\AutomationRepository;
use Aheadworks\Helpdesk2\Api\Data\AutomationInterface;
use Aheadworks\Helpdesk2\Api\Data\AutomationInterfaceFactory;
use Aheadworks\Helpdesk2\Api\Data\AutomationSearchResultsInterfaceFactory;
use Aheadworks\Helpdesk2\Model\ResourceModel\Automation as AutomationResourceModel;
use Aheadworks\Helpdesk2\Model\ResourceModel\Automation\CollectionFactory as AutomationCollectionFactory;
use Aheadworks\Helpdesk2\Model\ResourceModel\Automation\Collection as AutomationCollection;

/**
 * Test for AutomationRepository class
 */
class AutomationRepositoryTest extends TestCase
{
    /**
     * @var AutomationRepository
     */
    private $model;

    /**
     * @var AutomationResourceModel|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resourceMock;

    /**
     * @var AutomationInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $automationFactoryMock;

    /**
     * @var AutomationCollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $automationCollectionFactoryMock;

    /**
     * @var AutomationSearchResultsInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $searchResultsFactoryMock;

    /**
     * @var JoinProcessorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $extensionAttributesJoinProcessorMock;

    /**
     * @var CollectionProcessorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $collectionProcessorMock;

    /**
     * @var DataObjectHelper|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dataObjectHelperMock;

    /**
     * @var array
     */
    private $testData = [
        AutomationInterface::ID => 1,
        AutomationInterface::NAME => 'automation name'
    ];

    /**
     * Init mocks for tests
     *
     * @return void
     * @throws \ReflectionException
     */
    public function setUp() : void
    {
        $objectManager = new ObjectManager($this);
        $this->resourceMock = $this->getMockBuilder(AutomationResourceModel::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->automationFactoryMock = $this->getMockBuilder(AutomationInterfaceFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->automationCollectionFactoryMock = $this->getMockBuilder(AutomationCollectionFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->searchResultsFactoryMock = $this->getMockBuilder(AutomationSearchResultsInterfaceFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->extensionAttributesJoinProcessorMock = $this->getMockForAbstractClass(JoinProcessorInterface::class);
        $this->collectionProcessorMock = $this->getMockForAbstractClass(CollectionProcessorInterface::class);
        $this->dataObjectHelperMock = $this->getMockBuilder(DataObjectHelper::class)
            ->disableOriginalConstructor()->getMock();
        $this->model = $objectManager->getObject(
            AutomationRepository::class,
            [
                'resource' => $this->resourceMock,
                'dataObjectHelper' => $this->dataObjectHelperMock,
                'automationFactory' => $this->automationFactoryMock,
                'automationCollectionFactory' => $this->automationCollectionFactoryMock,
                'searchResultsFactory' => $this->searchResultsFactoryMock,
                'extensionAttributesJoinProcessor' => $this->extensionAttributesJoinProcessorMock,
                'collectionProcessor' => $this->collectionProcessorMock
            ]
        );
    }

    /**
     * Testing of save method
     */
    public function testSave()
    {
        /** @var AutomationInterface|\PHPUnit_Framework_MockObject_MockObject $automationMock */
        $automationMock = $this->getMockBuilder(Automation::class)
            ->disableOriginalConstructor()->getMock();
        $this->resourceMock->expects($this->once())
            ->method('save')
            ->willReturnSelf();
        $automationMock->expects($this->once())
            ->method('getId')
            ->willReturn($this->testData[AutomationInterface::ID]);

        $this->assertSame($automationMock, $this->model->save($automationMock));
    }

    /**
     * Testing of save method on exception
     */
    public function testSaveOnException()
    {
        $exceptionMessage = 'Exception message.';
        $this->expectException('Magento\Framework\Exception\CouldNotSaveException');
        $this->expectExceptionMessage($exceptionMessage);
        $exception = new \Exception($exceptionMessage);

        /** @var AutomationInterface|\PHPUnit_Framework_MockObject_MockObject $automationMock */
        $automationMock = $this->getMockBuilder(Automation::class)
            ->disableOriginalConstructor()->getMock();
        $this->resourceMock->expects($this->once())
            ->method('save')
            ->willThrowException($exception);

        $this->model->save($automationMock);
    }

    /**
     * Testing of get method
     */
    public function testGet()
    {
        $automationId = 1;

        /** @var AutomationInterface|\PHPUnit_Framework_MockObject_MockObject $automationMock */
        $automationMock = $this->createMock(Automation::class);
        $this->automationFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($automationMock);

        $this->resourceMock->expects($this->once())
            ->method('load')
            ->with($automationMock, $automationId)
            ->willReturnSelf();
        $automationMock->expects($this->once())
            ->method('getId')
            ->willReturn($automationId);

        $this->assertSame($automationMock, $this->model->get($automationId));
    }

    /**
     * Testing of get method on exception
     */
    public function testGetOnException()
    {
        $this->expectException('Magento\Framework\Exception\NoSuchEntityException');
        $this->expectExceptionMessage('No such entity with id = 20');
        $automationId = 20;
        $automationMock = $this->createMock(Automation::class);
        $this->automationFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($automationMock);

        $this->resourceMock->expects($this->once())
            ->method('load')
            ->with($automationMock, $automationId)
            ->willReturn(null);

        $this->model->get($automationId);
    }

    /**
     * Testing of getList method
     */
    public function testGetList()
    {
        $collectionSize = 1;
        /** @var AutomationCollection|\PHPUnit_Framework_MockObject_MockObject $automationCollectionMock */
        $automationCollectionMock = $this->getMockBuilder(AutomationCollection::class)
            ->disableOriginalConstructor()->getMock();
        /** @var SearchCriteriaInterface|\PHPUnit_Framework_MockObject_MockObject $searchCriteriaMock */
        $searchCriteriaMock = $this->getMockForAbstractClass(SearchCriteriaInterface::class);
        $searchResultsMock = $this->getMockForAbstractClass(AutomationSearchResultsInterface::class);
        /** @var Automation|\PHPUnit_Framework_MockObject_MockObject $automationModelMock */
        $automationModelMock = $this->getMockBuilder(Automation::class)
            ->disableOriginalConstructor()->getMock();
        /** @var AutomationInterface|\PHPUnit_Framework_MockObject_MockObject $automationMock */
        $automationMock = $this->getMockForAbstractClass(AutomationInterface::class);

        $this->automationCollectionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($automationCollectionMock);
        $this->extensionAttributesJoinProcessorMock->expects($this->once())
            ->method('process')
            ->with($automationCollectionMock, AutomationInterface::class);
        $this->collectionProcessorMock->expects($this->once())
            ->method('process')
            ->with($searchCriteriaMock, $automationCollectionMock);

        $automationCollectionMock->expects($this->once())
            ->method('getSize')
            ->willReturn($collectionSize);

        $this->searchResultsFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($searchResultsMock);
        $searchResultsMock->expects($this->once())
            ->method('setSearchCriteria')
            ->with($searchCriteriaMock);
        $searchResultsMock->expects($this->once())
            ->method('setTotalCount')
            ->with($collectionSize);

        $automationCollectionMock->expects($this->once())
            ->method('getItems')
            ->willReturn([$automationModelMock]);

        $this->automationFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($automationMock);
        $automationModelMock->expects($this->once())
            ->method('getData')
            ->willReturn($this->testData);
        $this->dataObjectHelperMock->expects($this->once())
            ->method('populateWithArray')
            ->with($automationMock, $this->testData, AutomationInterface::class);

        $searchResultsMock->expects($this->once())
            ->method('setItems')
            ->with([$automationMock])
            ->willReturnSelf();

        $this->assertSame($searchResultsMock, $this->model->getList($searchCriteriaMock));
    }

    /**
     * Testing of getList method
     */
    public function testDeleteById()
    {
        $automationId = '123';

        $automationMock = $this->createMock(Automation::class);
        $automationMock->expects($this->any())
            ->method('getId')
            ->willReturn($automationId);
        $this->automationFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($automationMock);
        $this->resourceMock->expects($this->once())
            ->method('load')
            ->with($automationMock, $automationId)
            ->willReturnSelf();
        $this->resourceMock->expects($this->once())
            ->method('delete')
            ->with($automationMock)
            ->willReturn(true);

        $this->assertTrue($this->model->deleteById($automationId));
    }

    /**
     * Testing of delete method on exception
     */
    public function testDeleteException()
    {
        $this->expectException('Magento\Framework\Exception\CouldNotDeleteException');
        $automationMock = $this->createMock(Automation::class);
        $this->resourceMock->expects($this->once())
            ->method('delete')
            ->with($automationMock)
            ->willThrowException(new \Exception());
        $this->model->delete($automationMock);
    }
}
