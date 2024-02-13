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
 * @package    Bup
 * @version    1.0.2
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Bup\Test\Unit\Model;

use Magento\Framework\Exception\NoSuchEntityException;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Aheadworks\Bup\Api\Data\UserProfileSearchResultsInterface;
use Aheadworks\Bup\Model\UserProfile;
use Aheadworks\Bup\Model\UserProfileRepository;
use Aheadworks\Bup\Api\Data\UserProfileInterface;
use Aheadworks\Bup\Api\Data\UserProfileInterfaceFactory;
use Aheadworks\Bup\Api\Data\UserProfileSearchResultsInterfaceFactory;
use Aheadworks\Bup\Model\ResourceModel\UserProfile as UserProfileResource;
use Aheadworks\Bup\Model\ResourceModel\UserProfile\CollectionFactory as UserProfileCollectionFactory;
use Aheadworks\Bup\Model\ResourceModel\UserProfile\Collection as UserProfileCollection;
use Magento\Framework\Exception\CouldNotSaveException;

/**
 * Test for UserProfileRepository class
 */
class UserProfileRepositoryTest extends TestCase
{
    /**
     * @var UserProfileRepository
     */
    private $model;

    /**
     * @var UserProfileResource|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resourceMock;

    /**
     * @var UserProfileInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $userProfileFactoryMock;

    /**
     * @var UserProfileCollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $userProfileCollectionFactoryMock;

    /**
     * @var UserProfileSearchResultsInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
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
        UserProfileInterface::USER_ID => 2,
        UserProfileInterface::DISPLAY_NAME => 'test_name'
    ];

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);
        $this->resourceMock = $this->getMockBuilder(UserProfileResource::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->userProfileFactoryMock = $this->getMockBuilder(UserProfileInterfaceFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->userProfileCollectionFactoryMock = $this->getMockBuilder(UserProfileCollectionFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->searchResultsFactoryMock = $this->getMockBuilder(UserProfileSearchResultsInterfaceFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->extensionAttributesJoinProcessorMock = $this->getMockForAbstractClass(JoinProcessorInterface::class);
        $this->collectionProcessorMock = $this->getMockForAbstractClass(CollectionProcessorInterface::class);
        $this->dataObjectHelperMock = $this->getMockBuilder(DataObjectHelper::class)
            ->disableOriginalConstructor()->getMock();
        $this->dataObjectProcessorMock = $this->getMockBuilder(DataObjectProcessor::class)
            ->disableOriginalConstructor()->getMock();
        $this->model = $objectManager->getObject(
            UserProfileRepository::class,
            [
                'resource' => $this->resourceMock,
                'dataObjectHelper' => $this->dataObjectHelperMock,
                'dataObjectProcessor' => $this->dataObjectProcessorMock,
                'userProfileFactory' => $this->userProfileFactoryMock,
                'userProfileCollectionFactory' => $this->userProfileCollectionFactoryMock,
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
        /** @var UserProfileInterface|\PHPUnit_Framework_MockObject_MockObject $userProfileMock */
        $userProfileMock = $this->getMockBuilder(UserProfile::class)
            ->disableOriginalConstructor()->getMock();
        $this->resourceMock->expects($this->once())
            ->method('save')
            ->willReturnSelf();
        $userProfileMock->expects($this->once())
            ->method('getUserId')
            ->willReturn($this->testData[UserProfileInterface::USER_ID]);

        $this->assertSame($userProfileMock, $this->model->save($userProfileMock));
    }

    /**
     * Testing of save method on exception
     */
    public function testSaveOnException()
    {
        $exception = new \Exception('Exception message.');

        /** @var UserProfileInterface|\PHPUnit_Framework_MockObject_MockObject $userProfileMock */
        $userProfileMock = $this->getMockBuilder(UserProfile::class)
            ->disableOriginalConstructor()->getMock();
        $this->resourceMock->expects($this->once())
            ->method('save')
            ->willThrowException($exception);

        $this->expectException(CouldNotSaveException::class);

        $this->model->save($userProfileMock);
    }

    /**
     * Testing of get method
     */
    public function testGet()
    {
        $userId = 1;

        /** @var UserProfileInterface|\PHPUnit_Framework_MockObject_MockObject $userProfileMock */
        $userProfileMock = $this->createMock(UserProfile::class);
        $this->userProfileFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($userProfileMock);

        $this->resourceMock->expects($this->once())
            ->method('load')
            ->with($userProfileMock, $userId)
            ->willReturnSelf();
        $userProfileMock->expects($this->once())
            ->method('getUserId')
            ->willReturn($userId);

        $this->assertSame($userProfileMock, $this->model->get($userId));
    }

    /**
     * Testing of get method on exception
     */
    public function testGetOnException()
    {
        $userId = 20;

        $userProfileMock = $this->createMock(UserProfile::class);
        $this->userProfileFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($userProfileMock);

        $this->resourceMock->expects($this->once())
            ->method('load')
            ->with($userProfileMock, $userId)
            ->willReturn(null);

        $this->expectException(NoSuchEntityException::class);

        $this->model->get($userId);
    }

    /**
     * Testing of getList method
     */
    public function testGetList()
    {
        $collectionSize = 1;
        /** @var UserProfileCollection|\PHPUnit_Framework_MockObject_MockObject $userProfileCollectionMock */
        $userProfileCollectionMock = $this->getMockBuilder(UserProfileCollection::class)
            ->disableOriginalConstructor()->getMock();
        /** @var SearchCriteriaInterface|\PHPUnit_Framework_MockObject_MockObject $searchCriteriaMock */
        $searchCriteriaMock = $this->getMockForAbstractClass(SearchCriteriaInterface::class);
        $searchResultsMock = $this->getMockForAbstractClass(UserProfileSearchResultsInterface::class);
        /** @var UserProfile|\PHPUnit_Framework_MockObject_MockObject $userProfileModelMock */
        $userProfileModelMock = $this->getMockBuilder(UserProfile::class)
            ->disableOriginalConstructor()->getMock();
        /** @var UserProfileInterface|\PHPUnit_Framework_MockObject_MockObject $userProfileMock */
        $userProfileMock = $this->getMockForAbstractClass(UserProfileInterface::class);

        $this->userProfileCollectionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($userProfileCollectionMock);
        $this->extensionAttributesJoinProcessorMock->expects($this->once())
            ->method('process')
            ->with($userProfileCollectionMock, UserProfileInterface::class);
        $this->collectionProcessorMock->expects($this->once())
            ->method('process')
            ->with($searchCriteriaMock, $userProfileCollectionMock);

        $userProfileCollectionMock->expects($this->once())
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

        $userProfileCollectionMock->expects($this->once())
            ->method('getItems')
            ->willReturn([$userProfileModelMock]);

        $this->userProfileFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($userProfileMock);
        $this->dataObjectProcessorMock->expects($this->once())
            ->method('buildOutputDataArray')
            ->with($userProfileModelMock, UserProfileInterface::class)
            ->willReturn($this->testData);
        $this->dataObjectHelperMock->expects($this->once())
            ->method('populateWithArray')
            ->with($userProfileMock, $this->testData, UserProfileInterface::class);

        $searchResultsMock->expects($this->once())
            ->method('setItems')
            ->with([$userProfileMock])
            ->willReturnSelf();

        $this->assertSame($searchResultsMock, $this->model->getList($searchCriteriaMock));
    }
}
