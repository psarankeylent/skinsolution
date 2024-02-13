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
namespace Aheadworks\Helpdesk2\Model\Data\Processor\Form\Ticket\ThirdParty\Aheadworks\CustomerAttributes;

use Magento\Framework\Exception\LocalizedException;
use Magento\Customer\Model\FileUploaderDataResolver;
use Aheadworks\Helpdesk2\Api\TicketRepositoryInterface;
use Aheadworks\Helpdesk2\Model\ThirdPartyModule\Aheadworks\CustomerAttributes\CustomerLoader;
use Aheadworks\Helpdesk2\Model\ThirdPartyModule\Aheadworks\CustomerAttributes\AttributeMetaProvider;
use Aheadworks\Helpdesk2\Model\Data\Processor\Form\ProcessorInterface;

/**
 * Class AttributeList
 *
 * @package Aheadworks\Helpdesk2\Model\Data\Processor\Form\Ticket\ThirdParty\Aheadworks\CustomerAttributes
 */
class AttributeList implements ProcessorInterface
{
    /**
     * @var TicketRepositoryInterface
     */
    private $ticketRepository;

    /**
     * @var CustomerLoader
     */
    private $customerLoader;

    /**
     * @var AttributeMetaProvider
     */
    private $attributeMetaProvider;

    /**
     * @var FileUploaderDataResolver
     */
    private $fileUploaderDataResolver;

    /**
     * @param TicketRepositoryInterface $ticketRepository
     * @param CustomerLoader $customerLoader
     * @param AttributeMetaProvider $attributeMetaProvider
     * @param FileUploaderDataResolver $fileUploaderDataResolver
     */
    public function __construct(
        TicketRepositoryInterface $ticketRepository,
        CustomerLoader $customerLoader,
        AttributeMetaProvider $attributeMetaProvider,
        FileUploaderDataResolver $fileUploaderDataResolver
    ) {
        $this->ticketRepository = $ticketRepository;
        $this->customerLoader = $customerLoader;
        $this->attributeMetaProvider = $attributeMetaProvider;
        $this->fileUploaderDataResolver = $fileUploaderDataResolver;
    }

    /**
     * @inheritdoc
     */
    public function prepareEntityData($data)
    {
        try {
            $ticket = $this->ticketRepository->getById($data['ticket_id']);
            $customer = $this->customerLoader->loadDataByCustomerEmail($ticket->getCustomerEmail());
            $data['customer'] = $customer->getData();
            $this->fileUploaderDataResolver->overrideFileUploaderData($customer, $data['customer']);

        } catch (\Exception $exception) {
            $data['customer'] = [];
        }

        return $data;
    }

    /**
     * @inheritdoc
     *
     * @throws LocalizedException
     */
    public function prepareMetaData($meta)
    {
        $meta['general']['children'] = $this->attributeMetaProvider->getHelpDeskAttributesMeta();
        return $meta;
    }
}
