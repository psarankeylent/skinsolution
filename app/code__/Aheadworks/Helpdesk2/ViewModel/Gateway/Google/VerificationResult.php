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
namespace Aheadworks\Helpdesk2\ViewModel\Gateway\Google;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;
use Aheadworks\Helpdesk2\Model\Data\Command\Gateway\VerifyGoogleAccount;

/**
 * Class VerificationResult
 *
 * @package Aheadworks\Helpdesk2\ViewModel\Gateway\Google
 */
class VerificationResult implements ArgumentInterface
{
    /**
     * @var SessionManagerInterface
     */
    private $sessionManagement;

    /**
     * @var JsonSerializer
     */
    private $jsonSerializer;

    /**
     * @param SessionManagerInterface $sessionManagement
     * @param JsonSerializer $jsonSerializer
     */
    public function __construct(
        SessionManagerInterface $sessionManagement,
        JsonSerializer $jsonSerializer
    ) {
        $this->sessionManagement = $sessionManagement;
        $this->jsonSerializer = $jsonSerializer;
    }

    /**
     * Get verification result
     *
     * @return string
     */
    public function getResult()
    {
        $result = $this->sessionManagement->getData(VerifyGoogleAccount::IS_VERIFIED) ?: false;
        if ($message = $this->sessionManagement->getData(VerifyGoogleAccount::GOOGLE_VERIFY_ERROR)) {
            $result = ['error' => $message];
            $this->sessionManagement->unsetData(VerifyGoogleAccount::GOOGLE_VERIFY_ERROR);
        }

        return $this->jsonSerializer->serialize($result);
    }
}
