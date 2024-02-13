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
use Aheadworks\Helpdesk2\Api\Data\TagSearchResultsInterface;
use Aheadworks\Helpdesk2\Model\Ticket\Tag;
use Aheadworks\Helpdesk2\Model\Ticket\TagRepository;
use Aheadworks\Helpdesk2\Api\Data\TagInterface;
use Aheadworks\Helpdesk2\Api\Data\TagInterfaceFactory;
use Aheadworks\Helpdesk2\Api\Data\TagSearchResultsInterfaceFactory;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Tag as TagResourceModel;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Tag\CollectionFactory as TagCollectionFactory;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Tag\Collection as TagCollection;

/**
 * Test for TagRepository class
 */
class TagRepositoryTest extends TestCase
{
    /**
     * @var TagRepository
     */
    private $model;

    /**
     * @var TagResourceModel|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resourceMock;

    /**
     * @var TagInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $tagFactoryMock;

    /**
     * @var TagCollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $tagCollectionFactoryMock;

    /**
     * @var TagSearchResultsInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
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
        TagInterface::ID => 1,
        TagInterface::NAME => 'tag name'
    ];

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp() : void
    {
        $objectManager = new ObjectManager($this);
        $this->resourceMock = $this->getMockBuilder(TagResourceModel::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->tagFactoryMock = $this->getMockBuilder(TagInterfaceFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->tagCollectionFactoryMock = $this->getMockBuilder(TagCollectionFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->searchResultsFactoryMock = $this->getMockBuilder(TagSearchResultsInterfaceFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->extensionAttributesJoinProcessorMock = $this->getMockForAbstractClass(JoinProcessorInterface::class);
        $this->collectionProcessorMock = $this->getMockForAbstractClass(CollectionProcessorInterface::class);
        $this->dataObjectHelperMock = $this->getMockBuilder(DataObjectHelper::class)
            ->disableOriginalConstructor()->getMock();
        $this->model = $objectManager->getObject(
            TagRepository::class,
            [
                'resource' => $this->resourceMock,
                'dataObjectHelper' => $this->dataObjectHelperMock,
                'tagFactory' => $this->tagFactoryMock,
                'tagCollectionFactory' => $this->tagCollectionFactoryMock,
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
        /** @var TagInterface|\PHPUnit_Framework_MockObject_MockObject $tagMock */
        $tagMock = $this->getMockBuilder(Tag::class)
            ->disableOriginalConstructor()->getMock();
        $this->resourceMock->expects($this->once())
            ->method('save')
            ->willReturnSelf();
        $tagMock->expects($this->once())
            ->method('getId')
            ->willReturn($this->testData[TagInterface::ID]);

        $this->assertSame($tagMock, $this->model->save($tagMock));
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

        /** @var TagInterface|\PHPUnit_Framework_MockObject_MockObject $tagMock */
        $tagMock = $this->getMockBuilder(Tag::class)
            ->disableOriginalConstructor()->getMock();
        $this->resourceMock->expects($this->once())
            ->method('save')
            ->willThrowException($exception);

        $this->model->save($tagMock);
    }

    /**
     * Testing of get method
     */
    public function testGet()
    {
        $tagId = 1;

        /** @var TagInterface|\PHPUnit_Framework_MockObject_MockObject $tagMock */
        $tagMock = $this->createMock(Tag::class);
        $this->tagFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($tagMock);

        $this->resourceMock->expects($this->once())
            ->method('load')
            ->with($tagMock, $tagId)
            ->willReturnSelf();
        $tagMock->expects($this->exactly(2))
            ->method('getId')
            ->willReturn($tagId);

        $this->assertSame($tagMock, $this->model->get($tagId));
    }

    /**
     * Testing of get method on exception
     */
    public function testGetOnException()
    {
        $this->expectException('Magento\Framework\Exception\NoSuchEntityException');
        $this->expectExceptionMessage('No such entity with id = 20');
        $tagId = 20;
        $tagMock = $this->createMock(Tag::class);
        $this->tagFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($tagMock);

        $this->resourceMock->expects($this->once())
            ->method('load')
            ->with($tagMock, $tagId)
            ->willReturn(null);

        $this->model->get($tagId);
    }

    /**
     * Testing of getList method
     */
    public function testGetList()
    {
        $collectionSize = 1;
        /** @var TagCollection|\PHPUnit_Framework_MockObject_MockObject $tagCollectionMock */
        $tagCollectionMock = $this->getMockBuilder(TagCollection::class)
            ->disableOriginalConstructor()->getMock();
        /** @var SearchCriteriaInterface|\PHPUnit_Framework_MockObject_MockObject $searchCriteriaMock */
        $searchCriteriaMock = $this->getMockForAbstractClass(SearchCriteriaInterface::class);
        $searchResultsMock = $this->getMockForAbstractClass(TagSearchResultsInterface::class);
        /** @var Tag|\PHPUnit_Framework_MockObject_MockObject $tagModelMock */
        $tagModelMock = $this->getMockBuilder(Tag::class)
            ->disableOriginalConstructor()->getMock();
        /** @var TagInterface|\PHPUnit_Framework_MockObject_MockObject $tagMock */
        $tagMock = $this->getMockForAbstractClass(TagInterface::class);

        $this->tagCollectionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($tagCollectionMock);
        $tagCollectionMock->expects($this->once())
            ->method('getIterator')
            ->willReturn(new \ArrayIterator([$tagModelMock]));
        $this->extensionAttributesJoinProcessorMock->expects($this->once())
            ->method('process')
            ->with($tagCollectionMock, TagInterface::class);
        $this->collectionProcessorMock->expects($this->once())
            ->method('process')
            ->with($searchCriteriaMock, $tagCollectionMock);

        $tagCollectionMock->expects($this->once())
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

        $this->tagFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($tagMock);
        $tagModelMock->expects($this->once())
            ->method('getData')
            ->willReturn($this->testData);
        $this->dataObjectHelperMock->expects($this->once())
            ->method('populateWithArray')
            ->with($tagMock, $this->testData, TagInterface::class);

        $searchResultsMock->expects($this->once())
            ->method('setItems')
            ->with([$tagMock])
            ->willReturnSelf();

        $this->assertSame($searchResultsMock, $this->model->getList($searchCriteriaMock));
    }

    /**
     * Testing of getList method
     */
    public function testDeleteById()
    {
        $tagId = '123';

        $tagMock = $this->createMock(Tag::class);
        $tagMock->expects($this->any())
            ->method('getId')
            ->willReturn($tagId);
        $this->tagFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($tagMock);
        $this->resourceMock->expects($this->once())
            ->method('load')
            ->with($tagMock, $tagId)
            ->willReturnSelf();
        $this->resourceMock->expects($this->once())
            ->method('delete')
            ->with($tagMock)
            ->willReturn(true);

        $this->assertTrue($this->model->deleteById($tagId));
    }

    /**
     * Testing of delete method on exception
     */
    public function testDeleteException()
    {
        $this->expectException('Magento\Framework\Exception\CouldNotDeleteException');
        $tagMock = $this->createMock(Tag::class);
        $this->resourceMock->expects($this->once())
            ->method('delete')
            ->with($tagMock)
            ->willThrowException(new \Exception());
        $this->model->delete($tagMock);
    }
}
