<?php
namespace Ssmd\Impersonation\Block\Adminhtml\Impersonation;

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
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Framework\Module\Manager $moduleManager,
        array $data = []
    ) {
        $this->customerFactory = $customerFactory;
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
        $collection = $this->customerFactory->create()->getCollection();
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
            'button',
            [
                'header' => __('Start Impersonation'),
                'type' => 'action',
                'getter' => 'getId',
                'actions' => [
                    [
                        'caption' => __('Start Impersonation'),
                        'url' => [
                            'base' => '*/*/view'
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
