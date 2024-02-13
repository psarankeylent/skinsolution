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
namespace Aheadworks\Helpdesk2\Model\Ticket\Message\Author;

use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Model\Ticket\Message\Author\Resolver\ResultFactory as ResolverResultFactory;
use Aheadworks\Helpdesk2\Model\User\Repository as UserRepository;
use Magento\Authorization\Model\UserContextInterface;
use Magento\Backend\Model\Auth\Session;
use Magento\Framework\App\State as AppState;
use Magento\Framework\Exception\LocalizedException;
use Magento\User\Model\User as MagentoUser;

/**
 * Class Resolver
 *
 * @package Aheadworks\Helpdesk2\Model\Ticket\Message\Author
 */
class Resolver
{
    /**
     * @var Session
     */
    private $authSession;

    /**
     * @var UserContextInterface
     */
    private $userContext;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var AppState
     */
    private $appState;

    /**
     * @var ResolverResultFactory
     */
    private $resultFactory;

    /**
     * @param ResolverResultFactory $resultFactory
     * @param AppState $appState
     * @param Session $authSession
     * @param UserContextInterface $userContext
     * @param UserRepository $userRepository
     */
    public function __construct(
        ResolverResultFactory $resultFactory,
        AppState $appState,
        Session $authSession,
        UserContextInterface $userContext,
        UserRepository $userRepository
    ) {
        $this->resultFactory = $resultFactory;
        $this->appState = $appState;
        $this->authSession = $authSession;
        $this->userContext = $userContext;
        $this->userRepository = $userRepository;
    }

    /**
     * Resolve agent
     *
     * @return Resolver\Result
     * @throws LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function resolveAgent()
    {
        $result = $this->resultFactory->create();

        if ($this->userContext->getUserType() == UserContextInterface::USER_TYPE_ADMIN) {
            /** @var MagentoUser $user */
            $user = $this->userRepository->getById($this->userContext->getUserId());
            return $result
                ->setName($user->getName())
                ->setEmail($user->getEmail());

        }

        throw new LocalizedException(__('Admin user cannot be identified'));
    }

    /**
     * Resolve automation
     *
     * @return Resolver\Result
     */
    public function resolveAutomation()
    {
        $result = $this->resultFactory->create();

        return $result->setName(__('Automation'))->setEmail('automation@email.com');
    }

    /**
     * Resolve author for detector
     *
     * @param TicketInterface $ticket
     * @return Resolver\Result
     * @throws LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function resolveForDetector($ticket)
    {
        $userType = $this->userContext->getUserType();
        if ($userType == UserContextInterface::USER_TYPE_ADMIN) {
            return $this->resolveAgent();
        } else {
            $result = $this->resultFactory->create();
            return $result
                ->setName($ticket->getCustomerName())
                ->setEmail($ticket->getCustomerEmail());
        }
    }
}
