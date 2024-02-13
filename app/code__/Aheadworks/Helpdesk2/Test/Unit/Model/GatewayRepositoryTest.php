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
use Magento\Framework\Reflection\DataObjectProcessor;
use Aheadworks\Helpdesk2\Api\Data\GatewayDataSearchResultsInterface;
use Aheadworks\Helpdesk2\Model\Gateway;
use Aheadworks\Helpdesk2\Model\GatewayRepository;
use Aheadworks\Helpdesk2\Api\Data\GatewayDataInterface;
use Aheadworks\Helpdesk2\Api\Data\GatewayDataInterfaceFactory;
use Aheadworks\Helpdesk2\Api\Data\GatewayDataSearchResultsInterfaceFactory;
use Aheadworks\Helpdesk2\Model\ResourceModel\Gateway as GatewayResourceModel;
use Aheadworks\Helpdesk2\Model\ResourceModel\Gateway\CollectionFactory as GatewayCollectionFactory;
use Aheadworks\Helpdesk2\Model\ResourceModel\Gateway\Collection as GatewayCollection;

/**
 * Test for GatewayRepository class
 */
class GatewayRepositoryTest extends TestCase
{
    /**
     * @var GatewayRepository
     */
    private $model;

    /**
     * @var GatewayResourceModel|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resourceMock;

    /**
     * @var GatewayDataInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $gatewayFactoryMock;

    /**
     * @var GatewayCollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $gatewayCollectionFactoryMock;

    /**
     * @var GatewayDataSearchResultsInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
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
        GatewayDataInterface::ID => 1,
        GatewayDataInterface::NAME => 'gateway name'
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
        $this->resourceMock = $this->getMockBuilder(GatewayResourceModel::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->gatewayFactoryMock = $this->getMockBuilder(GatewayDataInterfaceFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->gatewayCollectionFactoryMock = $this->getMockBuilder(GatewayCollectionFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->searchResultsFactoryMock = $this->getMockBuilder(GatewayDataSearchResultsInterfaceFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->extensionAttributesJoinProcessorMock = $this->getMockForAbstractClass(JoinProcessorInterface::class);
        $this->collectionProcessorMock = $this->getMockForAbstractClass(CollectionProcessorInterface::class);
        $this->dataObjectHelperMock = $this->getMockBuilder(DataObjectHelper::class)
            ->disableOriginalConstructor()->getMock();
        $this->dataObjectProcessorMock = $this->getMockBuilder(DataObjectProcessor::class)
            ->disableOriginalConstructor()->getMock();
        $this->model = $objectManager->getObject(
            GatewayRepository::class,
            [
                'resource' => $this->resourceMock,
                'dataObjectHelper' => $this->dataObjectHelperMock,
                'dataObjectProcessor' => $this->dataObjectProcessorMock,
                'gatewayFactory' => $this->gatewayFactoryMock,
                'gatewayCollectionFactory' => $this->gatewayCollectionFactoryMock,
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
        /** @var GatewayDataInterface|\PHPUnit_Framework_MockObject_MockObject $gatewayMock */
        $gatewayMock = $this->getMockBuilder(Gateway::class)
            ->disableOriginalConstructor()->getMock();
        $this->resourceMock->expects($this->once())
            ->method('save')
            ->willReturnSelf();
        $gatewayMock->expects($this->once())
            ->method('getId')
            ->willReturn($this->testData[GatewayDataInterface::ID]);

        $this->assertSame($gatewayMock, $this->model->save($gatewayMock));
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

        /** @var GatewayDataInterface|\PHPUnit_Framework_MockObject_MockObject $gatewayMock */
        $gatewayMock = $this->getMockBuilder(Gateway::class)
            ->disableOriginalConstructor()->getMock();
        $this->resourceMock->expects($this->once())
            ->method('save')
            ->willThrowException($exception);

        $this->model->save($gatewayMock);
    }

    /**
     * Testing of get method
     */
    public function testGet()
    {
        $gatewayId = 1;

        /** @var GatewayDataInterface|\PHPUnit_Framework_MockObject_MockObject $gatewayMock */
        $gatewayMock = $this->createMock(Gateway::class);
        $this->gatewayFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($gatewayMock);

        $this->resourceMock->expects($this->once())
            ->method('load')
            ->with($gatewayMock, $gatewayId)
            ->willReturnSelf();
        $gatewayMock->expects($this->once())
            ->method('getId')
            ->willReturn($gatewayId);

        $this->assertSame($gatewayMock, $this->model->get($gatewayId));
    }

    /**
     * Testing of get method on exception
     *
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage No such entity with id = 20
     */
    public function testGetOnException()
    {
        $this->expectException('Magento\Framework\Exception\NoSuchEntityException');
        $this->expectExceptionMessage('No such entity with id = 20');
        $gatewayId = 20;
        $gatewayMock = $this->createMock(Gateway::class);
        $this->gatewayFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($gatewayMock);

        $this->resourceMock->expects($this->once())
            ->method('load')
            ->with($gatewayMock, $gatewayId)
            ->willReturn(null);

        $this->model->get($gatewayId);
    }

    /**
     * Testing of getList method
     */
    public function testGetList()
    {
        $collectionSize = 1;
        /** @var GatewayCollection|\PHPUnit_Framework_MockObject_MockObject $gatewayCollectionMock */
        $gatewayCollectionMock = $this->getMockBuilder(GatewayCollection::class)
            ->disableOriginalConstructor()->getMock();
        /** @var SearchCriteriaInterface|\PHPUnit_Framework_MockObject_MockObject $searchCriteriaMock */
        $searchCriteriaMock = $this->getMockForAbstractClass(SearchCriteriaInterface::class);
        $searchResultsMock = $this->getMockForAbstractClass(GatewayDataSearchResultsInterface::class);
        /** @var Gateway|\PHPUnit_Framework_MockObject_MockObject $gatewayModelMock */
        $gatewayModelMock = $this->getMockBuilder(Gateway::class)
            ->disableOriginalConstructor()->getMock();
        /** @var GatewayDataInterface|\PHPUnit_Framework_MockObject_MockObject $gatewayMock */
        $gatewayMock = $this->getMockForAbstractClass(GatewayDataInterface::class);

        $this->gatewayCollectionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($gatewayCollectionMock);
        $this->extensionAttributesJoinProcessorMock->expects($this->once())
            ->method('process')
            ->with($gatewayCollectionMock, GatewayDataInterface::class);
        $this->collectionProcessorMock->expects($this->once())
            ->method('process')
            ->with($searchCriteriaMock, $gatewayCollectionMock);

        $gatewayCollectionMock->expects($this->once())
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

        $gatewayCollectionMock->expects($this->once())
            ->method('getItems')
            ->willReturn([$gatewayModelMock]);

        $this->gatewayFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($gatewayMock);
        $this->dataObjectProcessorMock->expects($this->once())
            ->method('buildOutputDataArray')
            ->with($gatewayModelMock, GatewayDataInterface::class)
            ->willReturn($this->testData);
        $this->dataObjectHelperMock->expects($this->once())
            ->method('populateWithArray')
            ->with($gatewayMock, $this->testData, GatewayDataInterface::class);

        $searchResultsMock->expects($this->once())
            ->method('setItems')
            ->with([$gatewayMock])
            ->willReturnSelf();

        $this->assertSame($searchResultsMock, $this->model->getList($searchCriteriaMock));
    }

    /**
     * Testing of getList method
     */
    public function testDeleteById()
    {
        $gatewayId = '123';

        $gatewayMock = $this->createMock(Gateway::class);
        $gatewayMock->expects($this->any())
            ->method('getId')
            ->willReturn($gatewayId);
        $this->gatewayFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($gatewayMock);
        $this->resourceMock->expects($this->once())
            ->method('load')
            ->with($gatewayMock, $gatewayId)
            ->willReturnSelf();
        $this->resourceMock->expects($this->once())
            ->method('delete')
            ->with($gatewayMock)
            ->willReturn(true);

        $this->assertTrue($this->model->deleteById($gatewayId));
    }

    /**
     * Testing of delete method on exception
     *
     * @expectedException \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function testDeleteException()
    {
        $this->expectException('Magento\Framework\Exception\CouldNotDeleteException');
        $gatewayMock = $this->createMock(Gateway::class);
        $this->resourceMock->expects($this->once())
            ->method('delete')
            ->with($gatewayMock)
            ->willThrowException(new \Exception());
        $this->model->delete($gatewayMock);
    }
}
