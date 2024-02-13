<?php

namespace Ssmd\AlleCustom\Model\Config\Backend\Cron;

//use Exception;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Value;
use Magento\Framework\App\Config\ValueFactory;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;

class AlleCustomCronConfig extends \Magento\Framework\App\Config\Value
{

    const CRON_STRING_PATH = 'crontab/default/jobs/send_email_allepoints_to_customer/schedule/cron_expr';
    
     /**
     * @var \Magento\Framework\App\Config\ValueFactory
     */
 
    protected $_configValueFactory;
 
    /**
     * @var mixed|string
     */
 
    protected $_runModelPath = '';
 
    /**
     * CronConfig1 constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @param \Magento\Framework\App\Config\ValueFactory $configValueFactory
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param string $runModelPath
     * @param array $data
     */
 
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\App\Config\ValueFactory $configValueFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        $runModelPath = '',
        array $data = [])
    {
        $this->_runModelPath = $runModelPath;
        $this->_configValueFactory = $configValueFactory;
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
    }
 
    /**
     * @return CronConfig1
     * @throws \Exception
     */
 
    public function afterSave()
    {
        //$frequency      = $this->getData('groups/subs_next_reminder_cronschedule/fields/subscription_frequency/value');
        $cronExprString = $this->getData('groups/alle_points_cronschedule/fields/alle_points_addition_cron/value');
       
        try
        {
            $this->_configValueFactory->create()->load(
                self::CRON_STRING_PATH,
                'path'
            )->setValue(
                $cronExprString
            )->setPath(
                self::CRON_STRING_PATH
            )->save();
        }
        catch (\Exception $e)
        {
            throw new \Exception(__('Some Thing Want Wrong , We can\'t save the cron expression.'));
        }
        return parent::afterSave();
    }
   
}
