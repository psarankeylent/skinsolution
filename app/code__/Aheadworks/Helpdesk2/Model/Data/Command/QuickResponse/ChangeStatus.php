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
namespace Aheadworks\Helpdesk2\Model\Data\Command\QuickResponse;

use Aheadworks\Helpdesk2\Model\Data\CommandInterface;
use Aheadworks\Helpdesk2\Api\QuickResponseRepositoryInterface;
use Aheadworks\Helpdesk2\Api\Data\QuickResponseInterface;

/**
 * Class ChangeStatus
 *
 * @package Aheadworks\Helpdesk2\Model\Data\Command\QuickResponse
 */
class ChangeStatus implements CommandInterface
{
    /**
     * @var QuickResponseRepositoryInterface
     */
    private $quickResponseRepository;

    /**
     * @param QuickResponseRepositoryInterface $quickResponseRepository
     */
    public function __construct(
        QuickResponseRepositoryInterface $quickResponseRepository
    ) {
        $this->quickResponseRepository = $quickResponseRepository;
    }

    /**
     * @inheritdoc
     */
    public function execute($data)
    {
        if (!isset($data[QuickResponseInterface::IS_ACTIVE]) || (!isset($data[QuickResponseInterface::ID]))) {
            throw new \InvalidArgumentException(
                'Status and ID params are required to change status'
            );
        }

        $isActive = (bool)$data[QuickResponseInterface::IS_ACTIVE];
        $quickResponse = $this->quickResponseRepository->get($data[QuickResponseInterface::ID]);

        if ($quickResponse->getIsActive() == $isActive) {
            return false;
        }

        $quickResponse->setIsActive($isActive);
        return $this->quickResponseRepository->save($quickResponse);
    }
}
