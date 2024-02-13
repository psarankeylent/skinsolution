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
namespace Aheadworks\Helpdesk2\Test\Unit\Model\Ticket;

use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Aheadworks\Helpdesk2\Model\Ticket\Status;
use Aheadworks\Helpdesk2\Model\Ticket\StatusRepository;
use Aheadworks\Helpdesk2\Api\Data\TicketStatusInterface;
use Aheadworks\Helpdesk2\Api\Data\TicketStatusInterfaceFactory;
use Aheadworks\Helpdesk2\Api\Data\TicketStatusSearchResultsInterface;
use Aheadworks\Helpdesk2\Api\Data\TicketStatusSearchResultsInterfaceFactory;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Status as StatusResourceModel;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Status\CollectionFactory as StatusCollectionFactory;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Status\Collection as StatusCollection;

/**
 * Test for StatusRepository class
 */
class StatusRepositoryTest extends TestCase
{
    /**
     * @var StatusRepository
     */
    private $model;

    /**
     * @var StatusResourceModel|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resourceMock;

    /**
     * @var TicketStatusInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $statusFactoryMock;

    /**
     * @var StatusCollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $statusCollectionFactoryMock;

    /**
     * @var TicketStatusSearchResultsInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
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
     * @var DataObjectProcessor|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dataObjectProcessorMock;

    /**
     * @var array
     */
    private $testData = [
        TicketStatusInterface::ID => 1,
        TicketStatusInterface::LABEL => 'test'
    ];

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp() : void
    {
        $objectManager = new ObjectManager($this);
        $this->resourceMock = $this->getMockBuilder(StatusResourceModel::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->statusFactoryMock = $this->getMockBuilder(TicketStatusInterfaceFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->statusCollectionFactoryMock = $this->getMockBuilder(StatusCollectionFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->searchResultsFactoryMock = $this->getMockBuilder(TicketStatusSearchResultsInterfaceFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->extensionAttributesJoinProcessorMock = $this->getMockForAbstractClass(JoinProcessorInterface::class);
        $this->collectionProcessorMock = $this->getMockForAbstractClass(CollectionProcessorInterface::class);
        $this->dataObjectHelperMock = $this->getMockBuilder(DataObjectHelper::class)
            ->disableOriginalConstructor()->getMock();
        $this->dataObjectProcessorMock = $this->getMockBuilder(DataObjectProcessor::class)
            ->disableOriginalConstructor()->getMock();
        $this->model = $objectManager->getObject(
            StatusRepository::class,
            [
                'resource' => $this->resourceMock,
                'dataObjectHelper' => $this->dataObjectHelperMock,
                'dataObjectProcessor' => $this->dataObjectProcessorMock,
                'ticketStatusFactory' => $this->statusFactoryMock,
                'ticketStatusCollectionFactory' => $this->statusCollectionFactoryMock,
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
        /** @var TicketStatusInterface|\PHPUnit_Framework_MockObject_MockObject $statusMock */
        $statusMock = $this->getMockBuilder(Status::class)
            ->disableOriginalConstructor()->getMock();
        $this->resourceMock->expects($this->once())
            ->method('save')
            ->willReturnSelf();
        $statusMock->expects($this->once())
            ->method('getId')
            ->willReturn($this->testData[TicketStatusInterface::ID]);

        $this->assertSame($statusMock, $this->model->save($statusMock));
    }

    /**
     * Testing of save method on exception
     */
    public function testSaveOnException()
    {
        $exceptionStatus = 'Exception message.';
        $this->expectException('Magento\Framework\Exception\CouldNotSaveException');
        $this->expectExceptionMessage($exceptionStatus);
        $exception = new \Exception($exceptionStatus);

        /** @var TicketStatusInterface|\PHPUnit_Framework_MockObject_MockObject $statusMock */
        $statusMock = $this->getMockBuilder(Status::class)
            ->disableOriginalConstructor()->getMock();
        $this->resourceMock->expects($this->once())
            ->method('save')
            ->willThrowException($exception);

        $this->model->save($statusMock);
    }

    /**
     * Testing of get method
     */
    public function testGet()
    {
        $statusId = 1;

        /** @var TicketStatusInterface|\PHPUnit_Framework_MockObject_MockObject $statusMock */
        $statusMock = $this->createMock(Status::class);
        $this->statusFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($statusMock);

        $this->resourceMock->expects($this->once())
            ->method('load')
            ->with($statusMock, $statusId)
            ->willReturnSelf();
        $statusMock->expects($this->once())
            ->method('getId')
            ->willReturn($statusId);

        $this->assertSame($statusMock, $this->model->get($statusId));
    }

    /**
     * Testing of get method on exception
     */
    public function testGetOnException()
    {
        $this->expectException('Magento\Framework\Exception\NoSuchEntityException');
        $this->expectExceptionMessage('No such entity with id = 20');
        $statusId = 20;
        $statusMock = $this->createMock(Status::class);
        $this->statusFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($statusMock);

        $this->resourceMock->expects($this->once())
            ->method('load')
            ->with($statusMock, $statusId)
            ->willReturn(null);

        $this->model->get($statusId);
    }

    /**
     * Testing of getList method
     */
    public function testGetList()
    {
        $collectionSize = 1;
        /** @var StatusCollection|\PHPUnit_Framework_MockObject_MockObject $statusCollectionMock */
        $statusCollectionMock = $this->getMockBuilder(StatusCollection::class)
            ->disableOriginalConstructor()->getMock();
        /** @var SearchCriteriaInterface|\PHPUnit_Framework_MockObject_MockObject $searchCriteriaMock */
        $searchCriteriaMock = $this->getMockForAbstractClass(SearchCriteriaInterface::class);
        $searchResultsMock = $this->getMockForAbstractClass(TicketStatusSearchResultsInterface::class);
        /** @var Status|\PHPUnit_Framework_MockObject_MockObject $statusModelMock */
        $statusModelMock = $this->getMockBuilder(Status::class)
            ->disableOriginalConstructor()->getMock();
        /** @var TicketStatusInterface|\PHPUnit_Framework_MockObject_MockObject $statusMock */
        $statusMock = $this->getMockForAbstractClass(TicketStatusInterface::class);

        $this->statusCollectionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($statusCollectionMock);
        $this->extensionAttributesJoinProcessorMock->expects($this->once())
            ->method('process')
            ->with($statusCollectionMock, TicketStatusInterface::class);
        $this->collectionProcessorMock->expects($this->once())
            ->method('process')
            ->with($searchCriteriaMock, $statusCollectionMock);

        $statusCollectionMock->expects($this->once())
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

        $statusCollectionMock->expects($this->once())
            ->method('getItems')
            ->willReturn([$statusModelMock]);

        $this->statusFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($statusMock);
        $this->dataObjectProcessorMock->expects($this->once())
            ->method('buildOutputDataArray')
            ->with($statusModelMock, TicketStatusInterface::class)
            ->willReturn($this->testData);
        $this->dataObjectHelperMock->expects($this->once())
            ->method('populateWithArray')
            ->with($statusMock, $this->testData, TicketStatusInterface::class);

        $searchResultsMock->expects($this->once())
            ->method('setItems')
            ->with([$statusMock])
            ->willReturnSelf();

        $this->assertSame($searchResultsMock, $this->model->getList($searchCriteriaMock));
    }
}
