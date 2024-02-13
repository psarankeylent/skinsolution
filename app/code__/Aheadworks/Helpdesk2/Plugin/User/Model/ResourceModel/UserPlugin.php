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
namespace Aheadworks\Helpdesk2\Plugin\User\Model\ResourceModel;

use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket as TicketResourceModel;
use Magento\User\Model\ResourceModel\User as MagentoUserResourceModel;

/**
 * Class UserPlugin
 *
 * @package Aheadworks\Helpdesk2\Plugin\User\Model\ResourceModel
 */
class UserPlugin
{
    /**
     * @var TicketResourceModel
     */
    private $ticketResourceModel;

    /**
     * @param TicketResourceModel $ticketResourceModel
     */
    public function __construct(TicketResourceModel $ticketResourceModel)
    {
        $this->ticketResourceModel = $ticketResourceModel;
    }

    /**
     * Update ticket tables after delete magento user
     *
     * @param MagentoUserResourceModel $subject
     * @param bool $result
     * @param \Magento\Framework\Model\AbstractModel $user
     * @return bool
     */
    public function afterDelete(MagentoUserResourceModel $subject, $result, $user)
    {
        if ($result) {
            $this->ticketResourceModel->resetAgentId($user->getId());
        }

        return $result;
    }
}
