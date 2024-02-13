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
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Aheadworks\Helpdesk2\Api\Data\TicketSearchResultsInterface;
use Aheadworks\Helpdesk2\Model\Ticket;
use Aheadworks\Helpdesk2\Model\TicketRepository;
use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Api\Data\TicketInterfaceFactory;
use Aheadworks\Helpdesk2\Api\Data\TicketSearchResultsInterfaceFactory;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket as TicketResourceModel;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\CollectionFactory as TicketCollectionFactory;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Collection as TicketCollection;

/**
 * Test for TicketRepository class
 */
class TicketRepositoryTest extends TestCase
{
    /**
     * @var TicketRepository
     */
    private $model;

    /**
     * @var TicketResourceModel|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resourceMock;

    /**
     * @var TicketInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $ticketFactoryMock;

    /**
     * @var TicketCollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $ticketCollectionFactoryMock;

    /**
     * @var TicketSearchResultsInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
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
        TicketInterface::ENTITY_ID => 1,
        TicketInterface::SUBJECT => 'ticket subject'
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
        $this->resourceMock = $this->getMockBuilder(TicketResourceModel::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->ticketFactoryMock = $this->getMockBuilder(TicketInterfaceFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->ticketCollectionFactoryMock = $this->getMockBuilder(TicketCollectionFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->searchResultsFactoryMock = $this->getMockBuilder(TicketSearchResultsInterfaceFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->extensionAttributesJoinProcessorMock = $this->getMockForAbstractClass(JoinProcessorInterface::class);
        $this->collectionProcessorMock = $this->getMockForAbstractClass(CollectionProcessorInterface::class);
        $this->dataObjectHelperMock = $this->getMockBuilder(DataObjectHelper::class)
            ->disableOriginalConstructor()->getMock();
        $this->dataObjectProcessorMock = $this->getMockBuilder(DataObjectProcessor::class)
            ->disableOriginalConstructor()->getMock();
        $this->model = $objectManager->getObject(
            TicketRepository::class,
            [
                'resource' => $this->resourceMock,
                'dataObjectHelper' => $this->dataObjectHelperMock,
                'dataObjectProcessor' => $this->dataObjectProcessorMock,
                'ticketFactory' => $this->ticketFactoryMock,
                'ticketCollectionFactory' => $this->ticketCollectionFactoryMock,
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
        /** @var TicketInterface|\PHPUnit_Framework_MockObject_MockObject $ticketMock */
        $ticketMock = $this->getMockBuilder(Ticket::class)
            ->disableOriginalConstructor()->getMock();
        $this->resourceMock->expects($this->once())
            ->method('save')
            ->willReturnSelf();
        $ticketMock->expects($this->once())
            ->method('getEntityId')
            ->willReturn($this->testData[TicketInterface::ENTITY_ID]);

        $this->assertSame($ticketMock, $this->model->save($ticketMock));
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

        /** @var TicketInterface|\PHPUnit_Framework_MockObject_MockObject $ticketMock */
        $ticketMock = $this->getMockBuilder(Ticket::class)
            ->disableOriginalConstructor()->getMock();
        $this->resourceMock->expects($this->once())
            ->method('save')
            ->willThrowException($exception);

        $this->model->save($ticketMock);
    }

    /**
     * Testing of getById method
     */
    public function testGetById()
    {
        $ticketId = 1;

        /** @var TicketInterface|\PHPUnit_Framework_MockObject_MockObject $ticketMock */
        $ticketMock = $this->createMock(Ticket::class);
        $this->ticketFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($ticketMock);

        $this->resourceMock->expects($this->once())
            ->method('load')
            ->with($ticketMock, $ticketId)
            ->willReturnSelf();
        $ticketMock->expects($this->exactly(2))
            ->method('getEntityId')
            ->willReturn($ticketId);

        $this->assertSame($ticketMock, $this->model->getById($ticketId));
    }

    /**
     * Testing of getByUid method
     */
    public function testGetByUid()
    {
        $ticketUid = 'TSS-12345';
        $ticketId = 566;

        $this->resourceMock->expects($this->once())
            ->method('getTicketIdByUid')
            ->with($ticketUid)
            ->willReturn($ticketId);

        /** @var TicketInterface|\PHPUnit_Framework_MockObject_MockObject $ticketMock */
        $ticketMock = $this->createMock(Ticket::class);
        $this->ticketFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($ticketMock);

        $this->resourceMock->expects($this->once())
            ->method('load')
            ->with($ticketMock, $ticketId)
            ->willReturnSelf();
        $ticketMock->expects($this->exactly(2))
            ->method('getEntityId')
            ->willReturn($ticketId);
        $ticketMock->expects($this->exactly(2))
            ->method('getUid')
            ->willReturn($ticketUid);

        $this->assertSame($ticketMock, $this->model->getByUid($ticketUid));
    }

    /**
     * Testing of getByExternalLink method
     */
    public function testGetByExternalLink()
    {
        $externalLink = 'test-external-link';
        $ticketId = 33;

        $this->resourceMock->expects($this->once())
            ->method('getTicketIdByExternalLink')
            ->with($externalLink)
            ->willReturn($ticketId);

        /** @var TicketInterface|\PHPUnit_Framework_MockObject_MockObject $ticketMock */
        $ticketMock = $this->createMock(Ticket::class);
        $this->ticketFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($ticketMock);

        $this->resourceMock->expects($this->once())
            ->method('load')
            ->with($ticketMock, $ticketId)
            ->willReturnSelf();
        $ticketMock->expects($this->exactly(2))
            ->method('getEntityId')
            ->willReturn($ticketId);
        $ticketMock->expects($this->exactly(2))
            ->method('getExternalLink')
            ->willReturn($externalLink);

        $this->assertSame($ticketMock, $this->model->getByExternalLink($externalLink));
    }

    /**
     * Testing of get method on exception
     */
    public function testGetByIdOnException()
    {
        $this->expectException('Magento\Framework\Exception\NoSuchEntityException');
        $this->expectExceptionMessage('No such entity with entity_id = 20');
        $ticketId = 20;
        $ticketMock = $this->createMock(Ticket::class);
        $this->ticketFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($ticketMock);

        $this->resourceMock->expects($this->once())
            ->method('load')
            ->with($ticketMock, $ticketId)
            ->willReturn(null);

        $this->model->getById($ticketId);
    }

    /**
     * Testing of getList method
     */
    public function testGetList()
    {
        $collectionSize = 1;
        /** @var TicketCollection|\PHPUnit_Framework_MockObject_MockObject $ticketCollectionMock */
        $ticketCollectionMock = $this->getMockBuilder(TicketCollection::class)
            ->disableOriginalConstructor()->getMock();
        /** @var SearchCriteriaInterface|\PHPUnit_Framework_MockObject_MockObject $searchCriteriaMock */
        $searchCriteriaMock = $this->getMockForAbstractClass(SearchCriteriaInterface::class);
        $searchResultsMock = $this->getMockForAbstractClass(TicketSearchResultsInterface::class);
        /** @var Ticket|\PHPUnit_Framework_MockObject_MockObject $ticketModelMock */
        $ticketModelMock = $this->getMockBuilder(Ticket::class)
            ->disableOriginalConstructor()->getMock();
        /** @var TicketInterface|\PHPUnit_Framework_MockObject_MockObject $ticketMock */
        $ticketMock = $this->getMockForAbstractClass(TicketInterface::class);

        $this->ticketCollectionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($ticketCollectionMock);
        $this->extensionAttributesJoinProcessorMock->expects($this->once())
            ->method('process')
            ->with($ticketCollectionMock, TicketInterface::class);
        $this->collectionProcessorMock->expects($this->once())
            ->method('process')
            ->with($searchCriteriaMock, $ticketCollectionMock);

        $ticketCollectionMock->expects($this->once())
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

        $ticketCollectionMock->expects($this->once())
            ->method('getItems')
            ->willReturn([$ticketModelMock]);

        $this->ticketFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($ticketMock);
        $this->dataObjectProcessorMock->expects($this->once())
            ->method('buildOutputDataArray')
            ->with($ticketModelMock, TicketInterface::class)
            ->willReturn($this->testData);
        $this->dataObjectHelperMock->expects($this->once())
            ->method('populateWithArray')
            ->with($ticketMock, $this->testData, TicketInterface::class);

        $searchResultsMock->expects($this->once())
            ->method('setItems')
            ->with([$ticketMock])
            ->willReturnSelf();

        $this->assertSame($searchResultsMock, $this->model->getList($searchCriteriaMock));
    }

    /**
     * Testing of getList method
     */
    public function testDeleteById()
    {
        $ticketId = '123';

        $ticketMock = $this->createMock(Ticket::class);
        $ticketMock->expects($this->any())
            ->method('getEntityId')
            ->willReturn($ticketId);
        $this->ticketFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($ticketMock);
        $this->resourceMock->expects($this->once())
            ->method('load')
            ->with($ticketMock, $ticketId)
            ->willReturnSelf();
        $this->resourceMock->expects($this->once())
            ->method('delete')
            ->with($ticketMock)
            ->willReturn(true);

        $this->assertTrue($this->model->deleteById($ticketId));
    }

    /**
     * Testing of delete method on exception
     */
    public function testDeleteException()
    {
        $this->expectException('Magento\Framework\Exception\CouldNotDeleteException');
        $ticketMock = $this->createMock(Ticket::class);
        $this->resourceMock->expects($this->once())
            ->method('delete')
            ->with($ticketMock)
            ->willThrowException(new \Exception());
        $this->model->delete($ticketMock);
    }
}
