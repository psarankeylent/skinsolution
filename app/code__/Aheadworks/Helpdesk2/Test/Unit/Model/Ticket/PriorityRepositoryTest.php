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
use Aheadworks\Helpdesk2\Model\Ticket\Priority;
use Aheadworks\Helpdesk2\Model\Ticket\PriorityRepository;
use Aheadworks\Helpdesk2\Api\Data\TicketPriorityInterface;
use Aheadworks\Helpdesk2\Api\Data\TicketPriorityInterfaceFactory;
use Aheadworks\Helpdesk2\Api\Data\TicketPrioritySearchResultsInterface;
use Aheadworks\Helpdesk2\Api\Data\TicketPrioritySearchResultsInterfaceFactory;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Priority as PriorityResourceModel;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Priority\CollectionFactory as PriorityCollectionFactory;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Priority\Collection as PriorityCollection;

/**
 * Test for PriorityRepository class
 */
class PriorityRepositoryTest extends TestCase
{
    /**
     * @var PriorityRepository
     */
    private $model;

    /**
     * @var PriorityResourceModel|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resourceMock;

    /**
     * @var TicketPriorityInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $priorityFactoryMock;

    /**
     * @var PriorityCollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $priorityCollectionFactoryMock;

    /**
     * @var TicketPrioritySearchResultsInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
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
        TicketPriorityInterface::ID => 1,
        TicketPriorityInterface::LABEL => 'test'
    ];

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp() : void
    {
        $objectManager = new ObjectManager($this);
        $this->resourceMock = $this->getMockBuilder(PriorityResourceModel::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->priorityFactoryMock = $this->getMockBuilder(TicketPriorityInterfaceFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->priorityCollectionFactoryMock = $this->getMockBuilder(PriorityCollectionFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->searchResultsFactoryMock = $this->getMockBuilder(TicketPrioritySearchResultsInterfaceFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->extensionAttributesJoinProcessorMock = $this->getMockForAbstractClass(JoinProcessorInterface::class);
        $this->collectionProcessorMock = $this->getMockForAbstractClass(CollectionProcessorInterface::class);
        $this->dataObjectHelperMock = $this->getMockBuilder(DataObjectHelper::class)
            ->disableOriginalConstructor()->getMock();
        $this->dataObjectProcessorMock = $this->getMockBuilder(DataObjectProcessor::class)
            ->disableOriginalConstructor()->getMock();
        $this->model = $objectManager->getObject(
            PriorityRepository::class,
            [
                'resource' => $this->resourceMock,
                'dataObjectHelper' => $this->dataObjectHelperMock,
                'dataObjectProcessor' => $this->dataObjectProcessorMock,
                'ticketPriorityFactory' => $this->priorityFactoryMock,
                'ticketPriorityCollectionFactory' => $this->priorityCollectionFactoryMock,
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
        /** @var TicketPriorityInterface|\PHPUnit_Framework_MockObject_MockObject $priorityMock */
        $priorityMock = $this->getMockBuilder(Priority::class)
            ->disableOriginalConstructor()->getMock();
        $this->resourceMock->expects($this->once())
            ->method('save')
            ->willReturnSelf();
        $priorityMock->expects($this->once())
            ->method('getId')
            ->willReturn($this->testData[TicketPriorityInterface::ID]);

        $this->assertSame($priorityMock, $this->model->save($priorityMock));
    }

    /**
     * Testing of save method on exception
     */
    public function testSaveOnException()
    {
        $exceptionPriority = 'Exception message.';
        $this->expectException('Magento\Framework\Exception\CouldNotSaveException');
        $this->expectExceptionMessage($exceptionPriority);
        $exception = new \Exception($exceptionPriority);

        /** @var TicketPriorityInterface|\PHPUnit_Framework_MockObject_MockObject $priorityMock */
        $priorityMock = $this->getMockBuilder(Priority::class)
            ->disableOriginalConstructor()->getMock();
        $this->resourceMock->expects($this->once())
            ->method('save')
            ->willThrowException($exception);

        $this->model->save($priorityMock);
    }

    /**
     * Testing of get method
     */
    public function testGet()
    {
        $priorityId = 1;

        /** @var TicketPriorityInterface|\PHPUnit_Framework_MockObject_MockObject $priorityMock */
        $priorityMock = $this->createMock(Priority::class);
        $this->priorityFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($priorityMock);

        $this->resourceMock->expects($this->once())
            ->method('load')
            ->with($priorityMock, $priorityId)
            ->willReturnSelf();
        $priorityMock->expects($this->once())
            ->method('getId')
            ->willReturn($priorityId);

        $this->assertSame($priorityMock, $this->model->get($priorityId));
    }

    /**
     * Testing of get method on exception
     */
    public function testGetOnException()
    {
        $this->expectException('Magento\Framework\Exception\NoSuchEntityException');
        $this->expectExceptionMessage('No such entity with id = 20');
        $priorityId = 20;
        $priorityMock = $this->createMock(Priority::class);
        $this->priorityFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($priorityMock);

        $this->resourceMock->expects($this->once())
            ->method('load')
            ->with($priorityMock, $priorityId)
            ->willReturn(null);

        $this->model->get($priorityId);
    }

    /**
     * Testing of getList method
     */
    public function testGetList()
    {
        $collectionSize = 1;
        /** @var PriorityCollection|\PHPUnit_Framework_MockObject_MockObject $priorityCollectionMock */
        $priorityCollectionMock = $this->getMockBuilder(PriorityCollection::class)
            ->disableOriginalConstructor()->getMock();
        /** @var SearchCriteriaInterface|\PHPUnit_Framework_MockObject_MockObject $searchCriteriaMock */
        $searchCriteriaMock = $this->getMockForAbstractClass(SearchCriteriaInterface::class);
        $searchResultsMock = $this->getMockForAbstractClass(TicketPrioritySearchResultsInterface::class);
        /** @var Priority|\PHPUnit_Framework_MockObject_MockObject $priorityModelMock */
        $priorityModelMock = $this->getMockBuilder(Priority::class)
            ->disableOriginalConstructor()->getMock();
        /** @var TicketPriorityInterface|\PHPUnit_Framework_MockObject_MockObject $priorityMock */
        $priorityMock = $this->getMockForAbstractClass(TicketPriorityInterface::class);

        $this->priorityCollectionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($priorityCollectionMock);
        $this->extensionAttributesJoinProcessorMock->expects($this->once())
            ->method('process')
            ->with($priorityCollectionMock, TicketPriorityInterface::class);
        $this->collectionProcessorMock->expects($this->once())
            ->method('process')
            ->with($searchCriteriaMock, $priorityCollectionMock);

        $priorityCollectionMock->expects($this->once())
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

        $priorityCollectionMock->expects($this->once())
            ->method('getItems')
            ->willReturn([$priorityModelMock]);

        $this->priorityFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($priorityMock);
        $this->dataObjectProcessorMock->expects($this->once())
            ->method('buildOutputDataArray')
            ->with($priorityModelMock, TicketPriorityInterface::class)
            ->willReturn($this->testData);
        $this->dataObjectHelperMock->expects($this->once())
            ->method('populateWithArray')
            ->with($priorityMock, $this->testData, TicketPriorityInterface::class);

        $searchResultsMock->expects($this->once())
            ->method('setItems')
            ->with([$priorityMock])
            ->willReturnSelf();

        $this->assertSame($searchResultsMock, $this->model->getList($searchCriteriaMock));
    }
}
