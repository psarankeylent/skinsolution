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
 * @package    Helpdesk2GraphQl
 * @version    1.0.0
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Helpdesk2GraphQl\Model\Resolver\Ticket;

use Aheadworks\Helpdesk2\Api\Data\DepartmentInterface;
use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Api\DepartmentRepositoryInterface;
use Aheadworks\Helpdesk2GraphQl\Model\ObjectConverter;
use Aheadworks\Helpdesk2GraphQl\Model\Resolver\AbstractResolver;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

/**
 * Class DepartmentResolver
 *
 * @package Aheadworks\Helpdesk2GraphQl\Model\Resolver\Ticket
 */
class DepartmentResolver extends AbstractResolver
{
    /**
     * @var DepartmentRepositoryInterface
     */
    private $repository;

    /**
     * @var ObjectConverter
     */
    private $objectConverter;

    /**
     * @param DepartmentRepositoryInterface $repository
     * @param ObjectConverter $objectConverter
     */
    public function __construct(
        DepartmentRepositoryInterface $repository,
        ObjectConverter $objectConverter
    ) {
        $this->repository = $repository;
        $this->objectConverter = $objectConverter;
    }

    /**
     * @inheritDoc
     */
    protected function performResolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        /** @var TicketInterface $ticket */
        $ticket = $value['model'];
        $departmentId = $ticket->getDepartmentId();
        $storeId = $ticket->getStoreId();

        $department = $this->repository->get($departmentId, $storeId);

        return $this->objectConverter->convertToArray($department, DepartmentInterface::class);
    }
}
