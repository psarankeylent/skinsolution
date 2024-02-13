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
namespace Aheadworks\Helpdesk2\Block\Adminhtml\Notification;

use Aheadworks\Helpdesk2\Controller\Adminhtml\Migration\DoNotShowAgain;
use Aheadworks\Helpdesk2\Model\Migration\Checker\Required as MigrationRequiredChecker;
use Aheadworks\Helpdesk2\Model\Migration\Processor;
use Aheadworks\Helpdesk2\Model\UrlBuilder;
use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\FlagManager;

/**
 * Class MigrationPopup
 *
 * @package Aheadworks\Helpdesk2\Block\Adminhtml\Notification
 */
class MigrationPopup extends Template
{
    /**
     * @var string
     */
    protected $_template = 'Aheadworks_Helpdesk2::notification/popup.phtml';

    /**
     * @var MigrationRequiredChecker
     */
    private $migrationChecker;

    /**
     * @var FlagManager
     */
    private $flagManager;

    /**
     * @var UrlBuilder
     */
    private $urlBuilder;

    /**
     * @param Context $context
     * @param MigrationRequiredChecker $migrationRequired
     * @param FlagManager $flagManager
     * @param UrlBuilder $urlBuilder
     * @param array $data
     */
    public function __construct(
        Context $context,
        MigrationRequiredChecker $migrationRequired,
        FlagManager $flagManager,
        UrlBuilder $urlBuilder,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->migrationChecker = $migrationRequired;
        $this->flagManager = $flagManager;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Render block
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (!$this->canShow()) {
            return '';
        }

        return parent::_toHtml();
    }

    /**
     * Check whether block should be displayed
     *
     * @return bool
     */
    public function canShow()
    {
        $helpdesk1WasInstalled = $this->migrationChecker->isMigrationRequired();
        $migrationCompleted = (int)$this->flagManager->getFlagData(Processor::MIGRATION_COMPLETED_FLAG);
        $doNotShowAgainNotificationPopup = (int)$this->flagManager->getFlagData(DoNotShowAgain::DO_NOT_SHOW_AGAIN_FLAG);

        return $helpdesk1WasInstalled && !$migrationCompleted && !$doNotShowAgainNotificationPopup;
    }

    /**
     * Retrieve url for DoNotShowAgain Controller
     *
     * @return string
     */
    public function getDoNotShowAgainUrl()
    {
        return $this->urlBuilder->getBackendMigrationNotificationDoNotShowAgain();
    }
}
