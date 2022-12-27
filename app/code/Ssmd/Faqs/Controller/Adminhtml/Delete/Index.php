<?php

namespace Ssmd\Faqs\Controller\Adminhtml\Delete;

class Index extends \Magento\Backend\App\Action
{
	protected $faqFactory;

	public function __construct(
		\Magento\Backend\App\Action\Context $context,
	    	\Magento\Framework\Controller\ResultFactory $resultFactory,	    
	    	\Ssmd\Faqs\Model\FaqFactory $faqFactory
	)
	{
		parent::__construct($context);
	   	$this->resultFactory = $resultFactory;
		$this->faqFactory = $faqFactory;	   
	}

	public function execute()
	{
		$this->_view->loadLayout();
		//$this->_view->getLayout()->initMessages();

		try{
			
			$id = $this->getRequest()->getParam('id');
			
			if($id)
			{
				$model = $this->faqFactory->create();
				$model->load($id)
				              ->delete();
				              
				$response = $this->resultFactory
					    	 ->create(\Magento\Framework\Controller\ResultFactory::TYPE_JSON)
						 ->setData([
							'status'  => 1,
							'message' => "Record successfully deleted"
						    ]);

				return $response;
			}
       	} catch(\Exception $e){
			
			$response = $this->resultFactory
			    ->create(\Magento\Framework\Controller\ResultFactory::TYPE_JSON)
			    ->setData([
				'status'  => 0,
				'message' => $e->getMessage()
			    ]);

			return $response;
		}
		
		$this->_view->renderLayout();
	}
}