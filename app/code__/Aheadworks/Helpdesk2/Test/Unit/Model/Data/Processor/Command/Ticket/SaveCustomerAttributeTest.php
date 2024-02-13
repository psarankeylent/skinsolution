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
namespace Aheadworks\Helpdesk2\Test\Unit\Model\Data\Processor\Command\Ticket;

use Magento\Customer\Api\Data\CustomerInterface;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Api\DataObjectHelper;
use Aheadworks\Helpdesk2\Model\Data\Command\Ticket\SaveCustomerAttribute;
use Magento\Customer\Api\CustomerRepositoryInterface;

/**
 * Class ReplyTest
 *
 * @package Aheadworks\Helpdesk2\Test\Unit\Model\Data\Processor\Command\Ticket
 */
class SaveCustomerAttributeTest extends TestCase
{
    /**
     * @var SaveCustomerAttribute
     */
    private $model;

    /**
     * @var DataObjectHelper|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dataObjectHelperMock;

    /**
     * @var CustomerRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $customerRepositoryMock;

    /**
     * Init mocks for tests
     *
     * @return void
     * @throws \ReflectionException
     */
    public function setUp() : void
    {
        $objectManager = new ObjectManager($this);
        $this->dataObjectHelperMock = $this->getMockBuilder(DataObjectHelper::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->customerRepositoryMock = $this->getMockForAbstractClass(CustomerRepositoryInterface::class);
        $this->model = $objectManager->getObject(
            SaveCustomerAttribute::class,
            [
                'dataObjectHelper' => $this->dataObjectHelperMock,
                'customerRepository' => $this->customerRepositoryMock
            ]
        );
    }

    /**
     * Testing of execute method on exception
     *
     * @throws LocalizedException
     */
    public function testExecuteOnException()
    {
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('Customer email is required to save attribute');

        $this->model->execute(
            [
                'email' => null,
                'test_attr' => 'attr_value'
            ]
        );
    }

    /**
     * Testing of execute method
     *
     * @throws LocalizedException
     * @throws \ReflectionException
     */
    public function testExecute()
    {
        $email = 'test_email@test.com';
        $customerData = [
            'email' => $email,
            'test_attr' => 'attr_value'
        ];
        $customerMock = $this->getMockForAbstractClass(CustomerInterface::class);
        $this->customerRepositoryMock->expects($this->once())
            ->method('get')
            ->with($email)
            ->willReturn($customerMock);
        $this->dataObjectHelperMock->expects($this->once())
            ->method('populateWithArray')
            ->with($customerMock, $customerData, CustomerInterface::class)
            ->willReturn($customerMock);
        $this->customerRepositoryMock->expects($this->once())
            ->method('save')
            ->with($customerMock)
            ->willReturn(true);

        $this->assertSame(true, $this->model->execute($customerData));
    }
}
