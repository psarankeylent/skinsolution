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
namespace Aheadworks\Helpdesk2\Model\Data\Command\Rejection\Message;

use Aheadworks\Helpdesk2\Model\Data\CommandInterface;
use Aheadworks\Helpdesk2\Model\Rejection\MessageRepository;

/**
 * Class Delete
 *
 * @package Aheadworks\Helpdesk2\Model\Data\Command\Rejection\Email
 */
class Delete implements CommandInterface
{
    /**
     * @var MessageRepository
     */
    private $rejectedMessageRepository;

    /**
     * @param MessageRepository $rejectedMessageRepository
     */
    public function __construct(
        MessageRepository $rejectedMessageRepository
    ) {
        $this->rejectedMessageRepository = $rejectedMessageRepository;
    }

    /**
     * @inheritdoc
     */
    public function execute($data)
    {
        if (!isset($data['item'])) {
            throw new \InvalidArgumentException(
                'Rejected email item param is required'
            );
        }

        $this->rejectedMessageRepository->delete($data['item']);

        return true;
    }
}
