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
namespace Aheadworks\Helpdesk2\Model\Ticket;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Eav\Model\AttributeRepository as EavAttributeRepository;
use Aheadworks\Helpdesk2\Api\TicketAttributeRepositoryInterface;
use Aheadworks\Helpdesk2\Model\Ticket as TicketModel;

/**
 * Class AttributeRepository
 *
 * @package Aheadworks\Helpdesk2\Model\Ticket
 */
class AttributeRepository implements TicketAttributeRepositoryInterface
{
    /**
     * @var EavAttributeRepository
     */
    private $eavAttributeRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @param EavAttributeRepository $eavAttributeRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        EavAttributeRepository $eavAttributeRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->eavAttributeRepository = $eavAttributeRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * @inheritdoc
     */
    public function get($attributeCode)
    {
        return $this->eavAttributeRepository->get(TicketModel::ENTITY, $attributeCode);
    }

    /**
     * @inheritdoc
     *
     * @throws InputException
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        return $this->eavAttributeRepository->getList(TicketModel::ENTITY, $searchCriteria);
    }

    /**
     * @inheritdoc
     *
     * @throws InputException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getCustomAttributesMetadata($dataObjectClassName = null)
    {
        return $this->getList($this->searchCriteriaBuilder->create())->getItems();
    }
}
