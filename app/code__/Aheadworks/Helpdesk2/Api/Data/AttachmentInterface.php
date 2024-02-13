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
namespace Aheadworks\Helpdesk2\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface AttachmentInterface
 * @api
 */
interface AttachmentInterface extends ExtensibleDataInterface
{
    /**
     * Constants defined for keys of the data array.
     * Identical to the name of the getter in snake case
     */
    const ID = 'id';
    const FILE_NAME = 'file_name';
    const FILE_PATH = 'file_path';

    /**
     * Get file name
     *
     * @return string
     */
    public function getFileName();

    /**
     * Set file name
     *
     * @param string $fileName
     * @return $this
     */
    public function setFileName($fileName);

    /**
     * Get file path
     *
     * @return string
     */
    public function getFilePath();

    /**
     * Set file path
     *
     * @param string $filePath
     * @return $this
     */
    public function setFilePath($filePath);

    /**
     * Retrieve existing extension attributes object if exists
     *
     * @return \Aheadworks\Helpdesk2\Api\Data\AttachmentExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object
     *
     * @param \Aheadworks\Helpdesk2\Api\Data\AttachmentExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Aheadworks\Helpdesk2\Api\Data\AttachmentExtensionInterface $extensionAttributes
    );
}
