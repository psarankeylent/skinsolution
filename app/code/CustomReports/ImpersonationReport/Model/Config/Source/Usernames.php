<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace CustomReports\ImpersonationReport\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use CustomReports\ImpersonationReport\Model\ResourceModel\Impersonation\CollectionFactory;

class Usernames implements OptionSourceInterface
{

    /**
     * @var array
     */
    protected $options;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * Constructor
     *
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(CollectionFactory $collectionFactory)
    {
        $this->collectionFactory = $collectionFactory;
    }


    public function toOptionArray()
    {
        if ($this->options === null) {
            $collection = $this->collectionFactory->create();

            foreach ($collection as $value) {
                $options[] = [
                    'label' => $value->getUsername(),
                    'value' => $value->getUsername(),
                ];
            }

            //echo "<pre>"; print_r($options); exit;
            $this->options = $options;
        }
        return $this->options;

    }
}
