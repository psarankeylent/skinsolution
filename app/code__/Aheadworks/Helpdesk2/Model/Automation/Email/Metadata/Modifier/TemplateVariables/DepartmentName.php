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
namespace Aheadworks\Helpdesk2\Model\Automation\Email\Metadata\Modifier\TemplateVariables;

use Magento\Framework\Exception\NoSuchEntityException;
use Aheadworks\Helpdesk2\Model\Automation\Email\ModifierInterface;
use Aheadworks\Helpdesk2\Model\Source\Email\Variables as EmailVariables;
use Aheadworks\Helpdesk2\Api\DepartmentRepositoryInterface;

/**
 * Class DepartmentName
 *
 * @package Aheadworks\Helpdesk2\Model\Automation\Email\Metadata\Modifier\TemplateVariables
 */
class DepartmentName implements ModifierInterface
{
    /**
     * @var DepartmentRepositoryInterface
     */
    private $departmentRepository;

    /**
     * @param DepartmentRepositoryInterface $departmentRepository
     */
    public function __construct(
        DepartmentRepositoryInterface $departmentRepository
    ) {
        $this->departmentRepository = $departmentRepository;
    }

    /**
     * @inheritdoc
     */
    public function addMetadata($emailMetadata, $eventData)
    {
        $templateVariables = $emailMetadata->getTemplateVariables();
        try {
            $department = $this->departmentRepository->get(
                $eventData->getTicket()->getDepartmentId(),
                $eventData->getTicket()->getStoreId()
            );
            $departmentName = $department->getCurrentStorefrontLabel()->getContent();
        } catch (NoSuchEntityException $exception) {
            $departmentName = '';
        }
        $templateVariables[EmailVariables::DEPARTMENT_NAME] = $departmentName;
        $emailMetadata->setTemplateVariables($templateVariables);

        return $emailMetadata;
    }
}
