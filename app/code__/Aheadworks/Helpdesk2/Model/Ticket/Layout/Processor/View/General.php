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
namespace Aheadworks\Helpdesk2\Model\Ticket\Layout\Processor\View;

use Aheadworks\Helpdesk2\Model\Source\Department\AgentList as AgentListSource;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Stdlib\ArrayManager;
use Aheadworks\Helpdesk2\Model\Ticket\Layout\ProcessorInterface;
use Aheadworks\Helpdesk2\Api\DepartmentRepositoryInterface;
use Aheadworks\Helpdesk2\Model\Ticket\Layout\Renderer\ViewRendererInterface;
use Aheadworks\Helpdesk2\Api\TicketStatusRepositoryInterface;
use Aheadworks\Helpdesk2\Model\User\Repository as UserRepository;

/**
 * Class General
 *
 * @package Aheadworks\Helpdesk2\Model\Ticket\Layout\Processor\View
 */
class General implements ProcessorInterface
{
    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @var DepartmentRepositoryInterface
     */
    private $departmentRepository;

    /**
     * @var TicketStatusRepositoryInterface
     */
    private $statusRepository;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @param ArrayManager $arrayManager
     * @param DepartmentRepositoryInterface $departmentRepository
     * @param TicketStatusRepositoryInterface $statusRepository
     * @param UserRepository $userRepository
     */
    public function __construct(
        ArrayManager $arrayManager,
        DepartmentRepositoryInterface $departmentRepository,
        TicketStatusRepositoryInterface $statusRepository,
        UserRepository $userRepository
    ) {
        $this->arrayManager = $arrayManager;
        $this->departmentRepository = $departmentRepository;
        $this->statusRepository = $statusRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * Prepare department selector
     *
     * @param array $jsLayout
     * @param ViewRendererInterface $renderer
     * @return array
     * @throws NoSuchEntityException
     */
    public function process($jsLayout, $renderer)
    {
        $formDataProvider = 'components/aw_helpdesk2_config_provider';
        $jsLayout = $this->arrayManager->merge(
            $formDataProvider,
            $jsLayout,
            [
                'data' => [
                    'ticket_id' => $renderer->getTicket()->getEntityId(),
                    'department_name' => $this->getDepartmentName($renderer),
                    'status_label' => $this->getStatusLabel($renderer),
                    'agent_name' => $this->getAgentName($renderer)
                ]
            ]
        );

        $formDataProvider = 'components/aw_helpdesk2_form_data_provider';
        $jsLayout = $this->arrayManager->merge(
            $formDataProvider,
            $jsLayout,
            [
                'data' => [
                    'key' => $renderer->getTicket()->getExternalLink()
                ]
            ]
        );

        return $jsLayout;
    }

    /**
     * Get department name
     *
     * @param ViewRendererInterface $renderer
     * @return string
     * @throws NoSuchEntityException
     */
    private function getDepartmentName($renderer)
    {
        $ticket = $renderer->getTicket();
        $department = $this->departmentRepository->get($ticket->getDepartmentId(), $ticket->getStoreId());
        return $department->getCurrentStorefrontLabel()->getContent();
    }

    /**
     * Get status label
     *
     * @param ViewRendererInterface $renderer
     * @return string
     * @throws NoSuchEntityException
     */
    private function getStatusLabel($renderer)
    {
        $status = $this->statusRepository->get($renderer->getTicket()->getStatusId());
        return __($status->getLabel());
    }

    /**
     * Get agent name
     *
     * @param ViewRendererInterface $renderer
     * @return string
     * @throws NoSuchEntityException
     */
    private function getAgentName($renderer)
    {
        $agentId = $renderer->getTicket()->getAgentId();
        $agentName = AgentListSource::getNotAssignedLabel();
        if ($agentId) {
            $agent = $this->userRepository->getById($agentId);
            $agentName = $agent->getFirstName() . ' ' . $agent->getLastName();
        }

        return $agentName;
    }
}
