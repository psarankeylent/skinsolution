<?php
/**
 * Paradox Labs, Inc.
 * http://www.paradoxlabs.com
 * 717-431-3330
 *
 * Need help? Open a ticket in our support system:
 *  http://support.paradoxlabs.com
 *
 * @author      Ryan Hoerr <info@paradoxlabs.com>
 * @license     http://store.paradoxlabs.com/license.html
 */

namespace ParadoxLabs\Subscriptions\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use ParadoxLabs\Subscriptions\Api\Data\SubscriptionInterface;
use ParadoxLabs\Subscriptions\Api\SubscriptionRepositoryInterface;
use ParadoxLabs\Subscriptions\Model\Config;
use ParadoxLabs\Subscriptions\Model\ResourceModel\Subscription\CollectionFactory;
use ParadoxLabs\Subscriptions\Model\Source\Status;

/**
 * Class MassStatus
 */
class MassStatus extends Action
{
    /**
     * @var string
     */
    protected $redirectUrl = '*/*/';

    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var SubscriptionRepositoryInterface
     */
    protected $subscriptionRepository;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var Status
     */
    protected $statusSource;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param SubscriptionRepositoryInterface $subscriptionRepository
     * @param Config $config
     * @param Status|null $statusSource
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        SubscriptionRepositoryInterface $subscriptionRepository,
        Config $config,
        Status $statusSource
    ) {
        parent::__construct($context);

        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->subscriptionRepository = $subscriptionRepository;
        $this->config = $config;
        $this->statusSource = $statusSource;
    }

    /**
     * Execute action
     *
     * @return Redirect
     */
    public function execute()
    {
        try {
            $collection = $this->filter->getCollection($this->collectionFactory->create());
            return $this->massAction($collection);
        } catch (\Throwable $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            return $resultRedirect->setPath($this->redirectUrl);
        }
    }

    /**
     * Return component referer url, or something
     *
     * @return null|string
     */
    protected function getComponentRefererUrl()
    {
        return $this->filter->getComponentRefererUrl() ?: 'subscriptions/index/';
    }

    /**
     * Change Status for selected subscriptions
     *
     * @param AbstractDb $collection
     * @return Redirect
     */
    protected function massAction(AbstractDb $collection)
    {
        $newStatus      = $this->getRequest()->getParam('status');
        $newStatusLabel = $this->statusSource->getOptionText($newStatus);

        $countChanged = 0;
        $countFailed  = 0;
        $countSkipped = 0;

        $this->changeStatus($newStatus, $collection, $countSkipped, $countChanged, $countFailed);

        if ($countChanged) {
            $this->messageManager->addSuccessMessage(
                __('Changed %1 subscription(s) to %2 successfully.', $countChanged, $newStatusLabel)
            );
        }

        if ($countSkipped) {
            $this->messageManager->addNoticeMessage(
                __('%1 subscription(s) already had status %2.', $countSkipped, $newStatusLabel)
            );
        }

        if ($countFailed) {
            $this->messageManager->addErrorMessage(
                __(
                    'Could not change %1 subscription(s) to %2. That change is not allowed.',
                    $countFailed,
                    $newStatusLabel
                )
            );
        }

        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath($this->getComponentRefererUrl());
        return $resultRedirect;
    }

    /**
     * Change the selected subscriptions status if the status can change to it.
     *
     * @param string $newStatus
     * @param AbstractDb $collection
     * @param int $countSkipped
     * @param int $countChanged
     * @param int $countFailed
     * @return void
     */
    protected function changeStatus(
        $newStatus,
        AbstractDb $collection,
        &$countSkipped,
        &$countChanged,
        &$countFailed
    ) {
        /** @var SubscriptionInterface $subscription */
        foreach ($collection->getItems() as $subscription) {
            if ($subscription->getStatus() === $newStatus) {
                $countSkipped++;
                continue;
            }

            if ($this->statusSource->canSetStatus($subscription, $newStatus) !== true) {
                $countFailed++;
                continue;
            }

            try {
                $subscription->setStatus($newStatus);
                $this->subscriptionRepository->save($subscription);

                $countChanged++;
            } catch (\Throwable $exception) {
                $countFailed++;
            }
        }
    }
}
