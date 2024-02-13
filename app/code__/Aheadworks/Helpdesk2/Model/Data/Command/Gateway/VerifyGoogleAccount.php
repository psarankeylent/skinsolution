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
namespace Aheadworks\Helpdesk2\Model\Data\Command\Gateway;

use Magento\Framework\Session\SessionManagerInterface;
use Aheadworks\Helpdesk2\Model\Data\CommandInterface;
use Aheadworks\Helpdesk2\Api\GatewayRepositoryInterface;
use Aheadworks\Helpdesk2\Api\Data\GatewayDataInterface;
use Aheadworks\Helpdesk2\Model\Gateway\Email\Connection\AuthType\Google as GoogleAuthModel;
use Aheadworks\Helpdesk2\Model\Data\Processor\Form\Gateway\GoogleVerification;
use Aheadworks\Helpdesk2\Controller\Adminhtml\Gateway\Google\BeforeVerify;
use Magento\Framework\Api\DataObjectHelper;

/**
 * Class VerifyGoogleAccount
 *
 * @package Aheadworks\Helpdesk2\Model\Data\Command\Gateway
 */
class VerifyGoogleAccount implements CommandInterface
{
    /**#@+
     * Google related data
     */
    const IS_VERIFIED = 'aw_helpdesk2_is_google_verified';
    const GOOGLE_VERIFY_ERROR = 'aw_helpdesk2_google_verify_error';
    const VERIFIED_DEPARTMENT_ID = 'aw_helpdesk2_google_verified_department_id';
    /**#@-*/

    /**
     * @var SessionManagerInterface
     */
    private $sessionManager;

    /**
     * @var GatewayRepositoryInterface
     */
    private $gatewayRepository;

    /**
     * @var GoogleAuthModel
     */
    private $googleAuthModel;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @param SessionManagerInterface $sessionManager
     * @param GatewayRepositoryInterface $gatewayRepository
     * @param GoogleAuthModel $googleAuthModel
     * @param DataObjectHelper $dataObjectHelper
     */
    public function __construct(
        SessionManagerInterface $sessionManager,
        GatewayRepositoryInterface $gatewayRepository,
        GoogleAuthModel $googleAuthModel,
        DataObjectHelper $dataObjectHelper
    ) {
        $this->sessionManager = $sessionManager;
        $this->gatewayRepository = $gatewayRepository;
        $this->googleAuthModel = $googleAuthModel;
        $this->dataObjectHelper = $dataObjectHelper;
    }

    /**
     * @inheritdoc
     */
    public function execute($params)
    {
        $gatewayId = $this->sessionManager->getData(GoogleVerification::GATEWAY_ID_TO_VERIFY);
        if ((!isset($params['code'])) || !$gatewayId) {
            throw new \InvalidArgumentException(
                sprintf('Google account cannot be verified')
            );
        }

        try {
            $gateway = $this->gatewayRepository->get($gatewayId);
            $this->mergeGatewayWithSessionData($gateway);
            $gateway = $this->googleAuthModel->actualizeGoogleAuthToken($gateway, $params['code']);
            $this->sessionManager->setData(
                self::IS_VERIFIED,
                [
                    GatewayDataInterface::IS_VERIFIED => (bool)$gateway->getIsVerified()
                ]
            );
        } catch (\Exception $e) {
            $gateway = null;
            $this->sessionManager->setData(self::GOOGLE_VERIFY_ERROR, $e->getMessage());
        }

        return $gateway;
    }

    /**
     * Merge gateway data with stored in session
     *
     * @param GatewayDataInterface $gateway
     * @return GatewayDataInterface
     */
    private function mergeGatewayWithSessionData($gateway)
    {
        $gatewayStoredParams = $this->sessionManager->getData(BeforeVerify::GATEWAY_DATA);
        if (is_array($gatewayStoredParams)) {
            unset($gatewayStoredParams[GatewayDataInterface::ACCESS_TOKEN]);
            $clientSecret = $gateway->getClientSecret();
            $this->dataObjectHelper->populateWithArray(
                $gateway,
                $gatewayStoredParams,
                GatewayDataInterface::class
            );
            $gateway->setClientSecret($clientSecret);
        }

        return $gateway;
    }
}
