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
 * @package    Bup
 * @version    1.0.2
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Bup\Plugin\User\Model;

use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\App\RequestInterface;
use Magento\User\Model\User;
use Aheadworks\Bup\Api\Data\UserProfileInterface;
use Aheadworks\Bup\Api\Data\UserProfileInterfaceFactory;
use Aheadworks\Bup\Model\DataProcessor\PostDataProcessorInterface;
use Aheadworks\Bup\Block\Adminhtml\User\Edit\Tab\FormElementApplier;

/**
 * Class ModelPlugin
 *
 * @package Aheadworks\Bup\Plugin\User\Model
 */
class ModelPlugin
{
    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var UserProfileInterfaceFactory
     */
    private $userProfileFactory;

    /**
     * @var PostDataProcessorInterface
     */
    private $postDataProcessor;

    /**
     * @param DataObjectHelper $dataObjectHelper
     * @param RequestInterface $request
     * @param UserProfileInterfaceFactory $userProfileFactory
     * @param PostDataProcessorInterface $postDataProcessor
     */
    public function __construct(
        DataObjectHelper $dataObjectHelper,
        RequestInterface $request,
        UserProfileInterfaceFactory $userProfileFactory,
        PostDataProcessorInterface $postDataProcessor
    ) {
        $this->dataObjectHelper = $dataObjectHelper;
        $this->request = $request;
        $this->userProfileFactory = $userProfileFactory;
        $this->postDataProcessor = $postDataProcessor;
    }

    /**
     * Add user profile to admin user
     *
     * This is the only extension point allowing to add
     * user profile to backend user object when saving account
     *
     * @param User $subject
     * @param User $result
     * @return User
     */
    public function afterPerformIdentityCheck(User $subject, User $result)
    {
        $data = $this->request->getParam(FormElementApplier::FORM_DATA_PREFIX);
        if (is_array($data) && !empty($data)) {
            $image = $this->request->getParam(FormElementApplier::IMAGE_INPUT_NAME);
            if (isset($image['delete'])) {
                $data[UserProfileInterface::IMAGE . '_delete'] = true;
            }

            $data = $this->postDataProcessor->prepareEntityData($data);

            /** @var UserProfileInterface $userProfile */
            $userProfile = $this->userProfileFactory->create();
            $this->dataObjectHelper->populateWithArray($userProfile, $data, UserProfileInterface::class);
            $result->setAwBupUserProfile($userProfile);
        }

        return $result;
    }
}
