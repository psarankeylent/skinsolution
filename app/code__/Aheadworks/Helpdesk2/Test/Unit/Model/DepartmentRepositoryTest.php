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
use Aheadworks\Helpdesk2\Api\Data\DepartmentSearchResultsInterface;
use Aheadworks\Helpdesk2\Model\Department;
use Aheadworks\Helpdesk2\Model\DepartmentRepository;
use Aheadworks\Helpdesk2\Api\Data\DepartmentInterface;
use Aheadworks\Helpdesk2\Api\Data\DepartmentInterfaceFactory;
use Aheadworks\Helpdesk2\Api\Data\DepartmentSearchResultsInterfaceFactory;
use Aheadworks\Helpdesk2\Model\ResourceModel\Department as DepartmentResourceModel;
use Aheadworks\Helpdesk2\Model\ResourceModel\Department\CollectionFactory as DepartmentCollectionFactory;
use Aheadworks\Helpdesk2\Model\ResourceModel\Department\Collection as DepartmentCollection;

/**
 * Test for DepartmentRepository class
 */
class DepartmentRepositoryTest extends TestCase
{
    /**
     * @var DepartmentRepository
     */
    private $model;

    /**
     * @var DepartmentResourceModel|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resourceMock;

    /**
     * @var DepartmentInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $departmentFactoryMock;

    /**
     * @var DepartmentCollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $departmentCollectionFactoryMock;

    /**
     * @var DepartmentSearchResultsInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
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
        DepartmentInterface::ID => 1,
        DepartmentInterface::NAME => 'department name'
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
        $this->resourceMock = $this->getMockBuilder(DepartmentResourceModel::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->departmentFactoryMock = $this->getMockBuilder(DepartmentInterfaceFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->departmentCollectionFactoryMock = $this->getMockBuilder(DepartmentCollectionFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->searchResultsFactoryMock = $this->getMockBuilder(DepartmentSearchResultsInterfaceFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->extensionAttributesJoinProcessorMock = $this->getMockForAbstractClass(JoinProcessorInterface::class);
        $this->collectionProcessorMock = $this->getMockForAbstractClass(CollectionProcessorInterface::class);
        $this->dataObjectHelperMock = $this->getMockBuilder(DataObjectHelper::class)
            ->disableOriginalConstructor()->getMock();
        $this->model = $objectManager->getObject(
            DepartmentRepository::class,
            [
                'resource' => $this->resourceMock,
                'dataObjectHelper' => $this->dataObjectHelperMock,
                'departmentFactory' => $this->departmentFactoryMock,
                'departmentCollectionFactory' => $this->departmentCollectionFactoryMock,
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
        /** @var DepartmentInterface|\PHPUnit_Framework_MockObject_MockObject $departmentMock */
        $departmentMock = $this->getMockBuilder(Department::class)
            ->disableOriginalConstructor()->getMock();
        $this->resourceMock->expects($this->once())
            ->method('save')
            ->willReturnSelf();
        $departmentMock->expects($this->once())
            ->method('getId')
            ->willReturn($this->testData[DepartmentInterface::ID]);

        $this->assertSame($departmentMock, $this->model->save($departmentMock));
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

        /** @var DepartmentInterface|\PHPUnit_Framework_MockObject_MockObject $departmentMock */
        $departmentMock = $this->getMockBuilder(Department::class)
            ->disableOriginalConstructor()->getMock();
        $this->resourceMock->expects($this->once())
            ->method('save')
            ->willThrowException($exception);

        $this->model->save($departmentMock);
    }

    /**
     * Testing of get method
     */
    public function testGet()
    {
        $departmentId = 1;

        /** @var DepartmentInterface|\PHPUnit_Framework_MockObject_MockObject $departmentMock */
        $departmentMock = $this->createMock(Department::class);
        $this->departmentFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($departmentMock);

        $this->resourceMock->expects($this->once())
            ->method('load')
            ->with($departmentMock, $departmentId)
            ->willReturnSelf();
        $departmentMock->expects($this->once())
            ->method('getId')
            ->willReturn($departmentId);

        $this->assertSame($departmentMock, $this->model->get($departmentId));
    }

    /**
     * Testing of get method on exception
     */
    public function testGetOnException()
    {
        $this->expectException('Magento\Framework\Exception\NoSuchEntityException');
        $this->expectExceptionMessage('No such entity with id = 20');
        $departmentId = 20;
        $departmentMock = $this->createMock(Department::class);
        $this->departmentFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($departmentMock);

        $this->resourceMock->expects($this->once())
            ->method('load')
            ->with($departmentMock, $departmentId)
            ->willReturn(null);

        $this->model->get($departmentId);
    }

    /**
     * Testing of getList method
     */
    public function testGetList()
    {
        $collectionSize = 1;
        /** @var DepartmentCollection|\PHPUnit_Framework_MockObject_MockObject $departmentCollectionMock */
        $departmentCollectionMock = $this->getMockBuilder(DepartmentCollection::class)
            ->disableOriginalConstructor()->getMock();
        /** @var SearchCriteriaInterface|\PHPUnit_Framework_MockObject_MockObject $searchCriteriaMock */
        $searchCriteriaMock = $this->getMockForAbstractClass(SearchCriteriaInterface::class);
        $searchResultsMock = $this->getMockForAbstractClass(DepartmentSearchResultsInterface::class);
        /** @var Department|\PHPUnit_Framework_MockObject_MockObject $departmentModelMock */
        $departmentModelMock = $this->getMockBuilder(Department::class)
            ->disableOriginalConstructor()->getMock();
        /** @var DepartmentInterface|\PHPUnit_Framework_MockObject_MockObject $departmentMock */
        $departmentMock = $this->getMockForAbstractClass(DepartmentInterface::class);

        $this->departmentCollectionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($departmentCollectionMock);
        $this->extensionAttributesJoinProcessorMock->expects($this->once())
            ->method('process')
            ->with($departmentCollectionMock, DepartmentInterface::class);
        $this->collectionProcessorMock->expects($this->once())
            ->method('process')
            ->with($searchCriteriaMock, $departmentCollectionMock);

        $departmentCollectionMock->expects($this->once())
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

        $departmentCollectionMock->expects($this->once())
            ->method('getItems')
            ->willReturn([$departmentModelMock]);

        $this->departmentFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($departmentMock);
        $departmentModelMock->expects($this->once())
            ->method('getData')
            ->willReturn($this->testData);
        $this->dataObjectHelperMock->expects($this->once())
            ->method('populateWithArray')
            ->with($departmentMock, $this->testData, DepartmentInterface::class);

        $searchResultsMock->expects($this->once())
            ->method('setItems')
            ->with([$departmentMock])
            ->willReturnSelf();

        $this->assertSame($searchResultsMock, $this->model->getList($searchCriteriaMock));
    }

    /**
     * Testing of getList method
     */
    public function testDeleteById()
    {
        $departmentId = '123';

        $departmentMock = $this->createMock(Department::class);
        $departmentMock->expects($this->any())
            ->method('getId')
            ->willReturn($departmentId);
        $this->departmentFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($departmentMock);
        $this->resourceMock->expects($this->once())
            ->method('load')
            ->with($departmentMock, $departmentId)
            ->willReturnSelf();
        $this->resourceMock->expects($this->once())
            ->method('delete')
            ->with($departmentMock)
            ->willReturn(true);

        $this->assertTrue($this->model->deleteById($departmentId));
    }

    /**
     * Testing of delete method on exception
     */
    public function testDeleteException()
    {
        $this->expectException('Magento\Framework\Exception\CouldNotDeleteException');
        $departmentMock = $this->createMock(Department::class);
        $this->resourceMock->expects($this->once())
            ->method('delete')
            ->with($departmentMock)
            ->willThrowException(new \Exception());
        $this->model->delete($departmentMock);
    }
}
