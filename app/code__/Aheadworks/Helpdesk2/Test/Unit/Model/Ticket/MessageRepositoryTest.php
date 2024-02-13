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
use Aheadworks\Helpdesk2\Api\Data\MessageSearchResultsInterface;
use Aheadworks\Helpdesk2\Model\Ticket\Message;
use Aheadworks\Helpdesk2\Model\Ticket\MessageRepository;
use Aheadworks\Helpdesk2\Api\Data\MessageInterface;
use Aheadworks\Helpdesk2\Api\Data\MessageInterfaceFactory;
use Aheadworks\Helpdesk2\Api\Data\MessageSearchResultsInterfaceFactory;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Message as MessageResourceModel;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Message\CollectionFactory as MessageCollectionFactory;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Message\Collection as MessageCollection;

/**
 * Test for MessageRepository class
 */
class MessageRepositoryTest extends TestCase
{
    /**
     * @var MessageRepository
     */
    private $model;

    /**
     * @var MessageResourceModel|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resourceMock;

    /**
     * @var MessageInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $messageFactoryMock;

    /**
     * @var MessageCollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $messageCollectionFactoryMock;

    /**
     * @var MessageSearchResultsInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
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
        MessageInterface::ID => 1,
        MessageInterface::CONTENT => 'message content'
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
        $this->resourceMock = $this->getMockBuilder(MessageResourceModel::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->messageFactoryMock = $this->getMockBuilder(MessageInterfaceFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->messageCollectionFactoryMock = $this->getMockBuilder(MessageCollectionFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->searchResultsFactoryMock = $this->getMockBuilder(MessageSearchResultsInterfaceFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->extensionAttributesJoinProcessorMock = $this->getMockForAbstractClass(JoinProcessorInterface::class);
        $this->collectionProcessorMock = $this->getMockForAbstractClass(CollectionProcessorInterface::class);
        $this->dataObjectHelperMock = $this->getMockBuilder(DataObjectHelper::class)
            ->disableOriginalConstructor()->getMock();
        $this->dataObjectProcessorMock = $this->getMockBuilder(DataObjectProcessor::class)
            ->disableOriginalConstructor()->getMock();
        $this->model = $objectManager->getObject(
            MessageRepository::class,
            [
                'resource' => $this->resourceMock,
                'dataObjectHelper' => $this->dataObjectHelperMock,
                'dataObjectProcessor' => $this->dataObjectProcessorMock,
                'messageFactory' => $this->messageFactoryMock,
                'messageCollectionFactory' => $this->messageCollectionFactoryMock,
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
        /** @var MessageInterface|\PHPUnit_Framework_MockObject_MockObject $messageMock */
        $messageMock = $this->getMockBuilder(Message::class)
            ->disableOriginalConstructor()->getMock();
        $this->resourceMock->expects($this->once())
            ->method('save')
            ->willReturnSelf();
        $messageMock->expects($this->once())
            ->method('getId')
            ->willReturn($this->testData[MessageInterface::ID]);

        $this->assertSame($messageMock, $this->model->save($messageMock));
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

        /** @var MessageInterface|\PHPUnit_Framework_MockObject_MockObject $messageMock */
        $messageMock = $this->getMockBuilder(Message::class)
            ->disableOriginalConstructor()->getMock();
        $this->resourceMock->expects($this->once())
            ->method('save')
            ->willThrowException($exception);

        $this->model->save($messageMock);
    }

    /**
     * Testing of getById method
     */
    public function testGetById()
    {
        $messageId = 1;

        /** @var MessageInterface|\PHPUnit_Framework_MockObject_MockObject $messageMock */
        $messageMock = $this->createMock(Message::class);
        $this->messageFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($messageMock);

        $this->resourceMock->expects($this->once())
            ->method('load')
            ->with($messageMock, $messageId)
            ->willReturnSelf();
        $messageMock->expects($this->exactly(2))
            ->method('getId')
            ->willReturn($messageId);

        $this->assertSame($messageMock, $this->model->getById($messageId));
    }

    /**
     * Testing of get method on exception
     */
    public function testGetByIdOnException()
    {
        $this->expectException('Magento\Framework\Exception\NoSuchEntityException');
        $this->expectExceptionMessage('No such entity with id = 20');
        $messageId = 20;
        $messageMock = $this->createMock(Message::class);
        $this->messageFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($messageMock);

        $this->resourceMock->expects($this->once())
            ->method('load')
            ->with($messageMock, $messageId)
            ->willReturn(null);

        $this->model->getById($messageId);
    }

    /**
     * Testing of getList method
     */
    public function testGetList()
    {
        $collectionSize = 1;
        /** @var MessageCollection|\PHPUnit_Framework_MockObject_MockObject $messageCollectionMock */
        $messageCollectionMock = $this->getMockBuilder(MessageCollection::class)
            ->disableOriginalConstructor()->getMock();
        /** @var SearchCriteriaInterface|\PHPUnit_Framework_MockObject_MockObject $searchCriteriaMock */
        $searchCriteriaMock = $this->getMockForAbstractClass(SearchCriteriaInterface::class);
        $searchResultsMock = $this->getMockForAbstractClass(MessageSearchResultsInterface::class);
        /** @var Message|\PHPUnit_Framework_MockObject_MockObject $messageModelMock */
        $messageModelMock = $this->getMockBuilder(Message::class)
            ->disableOriginalConstructor()->getMock();
        /** @var MessageInterface|\PHPUnit_Framework_MockObject_MockObject $messageMock */
        $messageMock = $this->getMockForAbstractClass(MessageInterface::class);

        $this->messageCollectionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($messageCollectionMock);
        $this->extensionAttributesJoinProcessorMock->expects($this->once())
            ->method('process')
            ->with($messageCollectionMock, MessageInterface::class);
        $this->collectionProcessorMock->expects($this->once())
            ->method('process')
            ->with($searchCriteriaMock, $messageCollectionMock);

        $messageCollectionMock->expects($this->once())
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

        $messageCollectionMock->expects($this->once())
            ->method('getItems')
            ->willReturn([$messageModelMock]);

        $this->messageFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($messageMock);
        $this->dataObjectProcessorMock->expects($this->once())
            ->method('buildOutputDataArray')
            ->with($messageModelMock, MessageInterface::class)
            ->willReturn($this->testData);
        $this->dataObjectHelperMock->expects($this->once())
            ->method('populateWithArray')
            ->with($messageMock, $this->testData, MessageInterface::class);

        $searchResultsMock->expects($this->once())
            ->method('setItems')
            ->with([$messageMock])
            ->willReturnSelf();

        $this->assertSame($searchResultsMock, $this->model->getList($searchCriteriaMock));
    }

    /**
     * Testing of getList method
     */
    public function testDeleteById()
    {
        $messageId = '123';

        $messageMock = $this->createMock(Message::class);
        $messageMock->expects($this->any())
            ->method('getId')
            ->willReturn($messageId);
        $this->messageFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($messageMock);
        $this->resourceMock->expects($this->once())
            ->method('load')
            ->with($messageMock, $messageId)
            ->willReturnSelf();
        $this->resourceMock->expects($this->once())
            ->method('delete')
            ->with($messageMock)
            ->willReturn(true);

        $this->assertTrue($this->model->deleteById($messageId));
    }

    /**
     * Testing of delete method on exception
     */
    public function testDeleteException()
    {
        $this->expectException('Magento\Framework\Exception\CouldNotDeleteException');
        $messageMock = $this->createMock(Message::class);
        $this->resourceMock->expects($this->once())
            ->method('delete')
            ->with($messageMock)
            ->willThrowException(new \Exception());
        $this->model->delete($messageMock);
    }
}
