<?php
namespace Ssmd\Impersonation\Block\Adminhtml\Order\Create;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Sales\Model\ResourceModel\Order\Customer\CollectionFactory $collectionFactory,
        \Magento\Framework\Module\Manager $moduleManager,
        array $data = []
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->moduleManager = $moduleManager;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('postGrid');
        //$this->setDefaultSort('id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(false);
        $this->setVarNameFilter('post_filter');
    }

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = $this->collectionFactory->create();
        //$collection->addFieldToFilter('entity_id', 1);
        $this->setCollection($collection);

        parent::_prepareCollection();

        return $this;
    }

    /**
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareColumns()
    {
        
                
                $this->addColumn(
                    'email',
                    [
                        'header' => __('Email'),
                        'index' => 'email',
                        'type'      => 'text',
                    ]
                );

                    
                $this->addColumn(
                    'telephone',
                    [
                        'header' => __('Phone'),
                        'index' => 'billing_telephone',
                        'type'      => 'text',
                    ]
                );

        
        $this->addColumn(
            'edit',
            [
                'header' => __('Start Session'),
                'type' => 'action',
                'getter' => 'getId',
                'actions' => [
                    [
                        'caption' => __('Start Session'),
                        'url' => [
                            'base' => 'impersonation/index/view'
                        ],
                        'field' => 'id',
                        'target' => '_blank'
                    ]
                ],
                'filter' => false,
                'sortable' => false,
                'index' => 'stores',
                'class' => 'col-button'
            ]
        );
        

        return parent::_prepareColumns();
    }


}