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
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\Helpdesk2\Model\Config;

/**
 * Test for Config class
 */
class ConfigTest extends TestCase
{
    /**
     * @var Config
     */
    private $model;

    /**
     * @var ScopeConfigInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $scopeConfigMock;

    /**
     * Init mocks for tests
     */
    protected function setUp() : void
    {
        $objectManager = new ObjectManager($this);
        $this->scopeConfigMock = $this->getMockForAbstractClass(ScopeConfigInterface::class);
        $this->model = $objectManager->getObject(
            Config::class,
            [
                'scopeConfig' => $this->scopeConfigMock
            ]
        );
    }

    /**
     * Test getPrimaryDepartment method
     */
    public function testGetPrimaryDepartment()
    {
        $expected = '1';

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_GENERAL_PRIMARY_DEPARTMENT)
            ->willReturn($expected);

        $this->assertEquals($expected, $this->model->getPrimaryDepartment());
    }

    /**
     * Test getBackendTicketPageDisplayedTicketsCount method
     */
    public function testGetBackendTicketPageDisplayedTicketsCount()
    {
        $expected = 5;

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_BACKEND_TICKET_PAGE_COUNT_OF_DISPLAYED_TICKETS)
            ->willReturn($expected);

        $this->assertEquals($expected, $this->model->getBackendTicketPageDisplayedTicketsCount());
    }

    /**
     * Test getBackendTicketPageDisplayedOrdersCount method
     */
    public function testGetBackendTicketPageDisplayedOrdersCount()
    {
        $expected = '3';

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_BACKEND_TICKET_PAGE_COUNT_OF_DISPLAYED_ORDERS)
            ->willReturn($expected);

        $this->assertEquals($expected, $this->model->getBackendTicketPageDisplayedOrdersCount());
    }

    /**
     * Test getBackendTicketPageDisplayedOrderStatuses method
     * @dataProvider orderStatusesDataProvider
     * @param string $value
     * @param array $result
     */
    public function testGetBackendTicketPageDisplayedOrderStatuses($value, $result)
    {
        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_BACKEND_TICKET_PAGE_DISPLAYED_ORDER_STATUSES)
            ->willReturn($value);

        $this->assertEquals($result, $this->model->getBackendTicketPageDisplayedOrderStatuses());
    }

    /**
     * Data provider for testGetBackendTicketPageDisplayedOrderStatuses method
     */
    public function orderStatusesDataProvider()
    {
        return [
            ['1,2,3', [1,2,3]],
            ['', ['']],
            [null, []]
        ];
    }

    /**
     * Test isEnabledContactFormIntegration method
     */
    public function testIsEnabledContactFormIntegration()
    {
        $expected = false;

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_FRONTEND_IS_ENABLED_CONTACT_FORM_INTEGRATION)
            ->willReturn($expected);

        $this->assertEquals($expected, $this->model->isEnabledContactFormIntegration());
    }

    /**
     * Test isEnabledCustomerRating method
     */
    public function testIsEnabledCustomerRating()
    {
        $expected = true;

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_IS_ENABLED_CUSTOMER_RATING)
            ->willReturn($expected);

        $this->assertEquals($expected, $this->model->isEnabledCustomerRating());
    }

    /**
     * Test isAllowedToAttachFiles method
     */
    public function testIsAllowedToAttachFiles()
    {
        $expected = false;

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_FRONTEND_IS_ALLOWED_TO_ATTACH_FILES)
            ->willReturn($expected);

        $this->assertEquals($expected, $this->model->isAllowedToAttachFiles());
    }

    /**
     * Test getMaxUploadFileSize method
     */
    public function testGetMaxUploadFileSize()
    {
        $value = 4;
        $result = $value * 1024 * 1024;

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_FRONTEND_MAX_UPLOAD_FILE_SIZE)
            ->willReturn($value);

        $this->assertEquals($result, $this->model->getMaxUploadFileSize());
    }

    /**
     * Test testGetAllowedFileExtensions method
     *
     * @dataProvider fileExtensionsDataProvider
     * @param string $value
     * @param array $result
     */
    public function testGetAllowedFileExtensions($value, $result)
    {
        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_FRONTEND_ALLOWED_FILE_EXTENSIONS)
            ->willReturn($value);

        $this->assertEquals($result, $this->model->getAllowedFileExtensions());
    }

    /**
     * Data provider for testGetAllowedFileExtensions method
     */
    public function fileExtensionsDataProvider()
    {
        return [
            ['1,2,3', [1,2,3]],
            ['', []],
            [null, []]
        ];
    }

    /**
     * Test isTicketEscalationEnabled method
     */
    public function testIsTicketEscalationEnabled()
    {
        $expected = false;

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_TICKET_ESCALATION_IS_ENABLED)
            ->willReturn($expected);

        $this->assertEquals($expected, $this->model->isTicketEscalationEnabled());
    }

    /**
     * Test getEscalationSupervisorEmails method
     *
     * @dataProvider escalationSupervisorEmailsDataProvider
     * @param string $value
     * @param array $result
     */
    public function testGetEscalationSupervisorEmails($value, $result)
    {
        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_TICKET_ESCALATION_SUPERVISOR_EMAILS)
            ->willReturn($value);

        $this->assertEquals($result, $this->model->getEscalationSupervisorEmails());
    }

    /**
     * Data provider for testGetEscalationSupervisorEmails method
     */
    public function escalationSupervisorEmailsDataProvider()
    {
        return [
            ['1,2,3', [1,2,3]],
            ['', []],
            [null, []]
        ];
    }

    /**
     * Test getEscalationEmailTemplate method
     */
    public function testGetEscalationEmailTemplate()
    {
        $expected = 'template1';

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_TICKET_ESCALATION_EMAIL_TEMPLATE)
            ->willReturn($expected);

        $this->assertEquals($expected, $this->model->getEscalationEmailTemplate());
    }
}
