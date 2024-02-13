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
use Aheadworks\Helpdesk2\Api\Data\QuickResponseSearchResultsInterface;
use Aheadworks\Helpdesk2\Model\QuickResponse;
use Aheadworks\Helpdesk2\Model\QuickResponseRepository;
use Aheadworks\Helpdesk2\Api\Data\QuickResponseInterface;
use Aheadworks\Helpdesk2\Api\Data\QuickResponseInterfaceFactory;
use Aheadworks\Helpdesk2\Api\Data\QuickResponseSearchResultsInterfaceFactory;
use Aheadworks\Helpdesk2\Model\ResourceModel\QuickResponse as QuickResponseResourceModel;
use Aheadworks\Helpdesk2\Model\ResourceModel\QuickResponse\CollectionFactory as QuickResponseCollectionFactory;
use Aheadworks\Helpdesk2\Model\ResourceModel\QuickResponse\Collection as QuickResponseCollection;

/**
 * Test for QuickResponseRepository class
 */
class QuickResponseRepositoryTest extends TestCase
{
    /**
     * @var QuickResponseRepository
     */
    private $model;

    /**
     * @var QuickResponseResourceModel|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resourceMock;

    /**
     * @var QuickResponseInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $quickResponseFactoryMock;

    /**
     * @var QuickResponseCollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $quickResponseCollectionFactoryMock;

    /**
     * @var QuickResponseSearchResultsInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
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
        QuickResponseInterface::ID => 1,
        QuickResponseInterface::TITLE => 'quick response title'
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
        $this->resourceMock = $this->getMockBuilder(QuickResponseResourceModel::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->quickResponseFactoryMock = $this->getMockBuilder(QuickResponseInterfaceFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->quickResponseCollectionFactoryMock = $this->getMockBuilder(QuickResponseCollectionFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->searchResultsFactoryMock = $this->getMockBuilder(QuickResponseSearchResultsInterfaceFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->extensionAttributesJoinProcessorMock = $this->getMockForAbstractClass(JoinProcessorInterface::class);
        $this->collectionProcessorMock = $this->getMockForAbstractClass(CollectionProcessorInterface::class);
        $this->dataObjectHelperMock = $this->getMockBuilder(DataObjectHelper::class)
            ->disableOriginalConstructor()->getMock();
        $this->model = $objectManager->getObject(
            QuickResponseRepository::class,
            [
                'resource' => $this->resourceMock,
                'dataObjectHelper' => $this->dataObjectHelperMock,
                'quickResponseFactory' => $this->quickResponseFactoryMock,
                'quickResponseCollectionFactory' => $this->quickResponseCollectionFactoryMock,
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
        /** @var QuickResponseInterface|\PHPUnit_Framework_MockObject_MockObject $quickResponseMock */
        $quickResponseMock = $this->getMockBuilder(QuickResponse::class)
            ->disableOriginalConstructor()->getMock();
        $this->resourceMock->expects($this->once())
            ->method('save')
            ->willReturnSelf();
        $quickResponseMock->expects($this->once())
            ->method('getId')
            ->willReturn($this->testData[QuickResponseInterface::ID]);

        $this->assertSame($quickResponseMock, $this->model->save($quickResponseMock));
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

        /** @var QuickResponseInterface|\PHPUnit_Framework_MockObject_MockObject $quickResponseMock */
        $quickResponseMock = $this->getMockBuilder(QuickResponse::class)
            ->disableOriginalConstructor()->getMock();
        $this->resourceMock->expects($this->once())
            ->method('save')
            ->willThrowException($exception);

        $this->model->save($quickResponseMock);
    }

    /**
     * Testing of get method
     */
    public function testGet()
    {
        $quickResponseId = 1;

        /** @var QuickResponseInterface|\PHPUnit_Framework_MockObject_MockObject $quickResponseMock */
        $quickResponseMock = $this->createMock(QuickResponse::class);
        $this->quickResponseFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($quickResponseMock);

        $this->resourceMock->expects($this->once())
            ->method('load')
            ->with($quickResponseMock, $quickResponseId)
            ->willReturnSelf();
        $quickResponseMock->expects($this->once())
            ->method('getId')
            ->willReturn($quickResponseId);

        $this->assertSame($quickResponseMock, $this->model->get($quickResponseId));
    }

    /**
     * Testing of get method on exception
     */
    public function testGetOnException()
    {
        $this->expectException('Magento\Framework\Exception\NoSuchEntityException');
        $this->expectExceptionMessage('No such entity with id = 20');
        $quickResponseId = 20;
        $quickResponseMock = $this->createMock(QuickResponse::class);
        $this->quickResponseFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($quickResponseMock);

        $this->resourceMock->expects($this->once())
            ->method('load')
            ->with($quickResponseMock, $quickResponseId)
            ->willReturn(null);

        $this->model->get($quickResponseId);
    }

    /**
     * Testing of getList method
     */
    public function testGetList()
    {
        $collectionSize = 1;
        /** @var QuickResponseCollection|\PHPUnit_Framework_MockObject_MockObject $quickResponseCollectionMock */
        $quickResponseCollectionMock = $this->getMockBuilder(QuickResponseCollection::class)
            ->disableOriginalConstructor()->getMock();
        /** @var SearchCriteriaInterface|\PHPUnit_Framework_MockObject_MockObject $searchCriteriaMock */
        $searchCriteriaMock = $this->getMockForAbstractClass(SearchCriteriaInterface::class);
        $searchResultsMock = $this->getMockForAbstractClass(QuickResponseSearchResultsInterface::class);
        /** @var QuickResponse|\PHPUnit_Framework_MockObject_MockObject $quickResponseModelMock */
        $quickResponseModelMock = $this->getMockBuilder(QuickResponse::class)
            ->disableOriginalConstructor()->getMock();
        /** @var QuickResponseInterface|\PHPUnit_Framework_MockObject_MockObject $quickResponseMock */
        $quickResponseMock = $this->getMockForAbstractClass(QuickResponseInterface::class);

        $this->quickResponseCollectionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($quickResponseCollectionMock);
        $this->extensionAttributesJoinProcessorMock->expects($this->once())
            ->method('process')
            ->with($quickResponseCollectionMock, QuickResponseInterface::class);
        $this->collectionProcessorMock->expects($this->once())
            ->method('process')
            ->with($searchCriteriaMock, $quickResponseCollectionMock);

        $quickResponseCollectionMock->expects($this->once())
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

        $quickResponseCollectionMock->expects($this->once())
            ->method('getItems')
            ->willReturn([$quickResponseModelMock]);

        $this->quickResponseFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($quickResponseMock);
        $quickResponseModelMock->expects($this->once())
            ->method('getData')
            ->willReturn($this->testData);
        $this->dataObjectHelperMock->expects($this->once())
            ->method('populateWithArray')
            ->with($quickResponseMock, $this->testData, QuickResponseInterface::class);

        $searchResultsMock->expects($this->once())
            ->method('setItems')
            ->with([$quickResponseMock])
            ->willReturnSelf();

        $this->assertSame($searchResultsMock, $this->model->getList($searchCriteriaMock));
    }

    /**
     * Testing of getList method
     */
    public function testDeleteById()
    {
        $quickResponseId = '123';

        $quickResponseMock = $this->createMock(QuickResponse::class);
        $quickResponseMock->expects($this->any())
            ->method('getId')
            ->willReturn($quickResponseId);
        $this->quickResponseFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($quickResponseMock);
        $this->resourceMock->expects($this->once())
            ->method('load')
            ->with($quickResponseMock, $quickResponseId)
            ->willReturnSelf();
        $this->resourceMock->expects($this->once())
            ->method('delete')
            ->with($quickResponseMock)
            ->willReturn(true);

        $this->assertTrue($this->model->deleteById($quickResponseId));
    }

    /**
     * Testing of delete method on exception
     */
    public function testDeleteException()
    {
        $this->expectException('Magento\Framework\Exception\CouldNotDeleteException');
        $quickResponseMock = $this->createMock(QuickResponse::class);
        $this->resourceMock->expects($this->once())
            ->method('delete')
            ->with($quickResponseMock)
            ->willThrowException(new \Exception());
        $this->model->delete($quickResponseMock);
    }
}
