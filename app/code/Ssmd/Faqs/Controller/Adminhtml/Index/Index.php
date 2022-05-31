<?php

namespace Ssmd\Faqs\Controller\Adminhtml\Index;

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
		$this->_view->getLayout()->initMessages();

		if($this->getRequest()->isPost())
		{
			$data = $this->getRequest()->getPostValue();

			unset($data['form_key']);
			unset($data['submit']);

			$t=0;
			foreach($data['ques'] as $key => $vi){

				if(isset($data['id']) && array_key_exists($t,$data['id']))
				{
					$faqArray[] = array('questions' => $data['ques'][$t], 'answers'=> $data['ans'][$t], 'id'=> $data['id'][$t]);
				}
				else{
					$faqArray[] = array('questions' => $data['ques'][$t], 'answers'=> $data['ans'][$t], 'id'=> '');
				}

				$t++;
			}
			try{
				foreach($faqArray as $value)
				{
					$model = $this->faqFactory->create();

					$id = $value['id'];
					if($id != "")
					{
						 $model->load($id)
						       ->addData($value)
						       ->save();
					}
					else
					{
						$model1 = $this->faqFactory->create();
						unset($value['id']);
						$model1->addData($value)
					      	      ->save();
					}
				}
			} catch(\Exception $e){
				echo $e->getMessage();
			}
		}

		$this->_view->renderLayout();
	}

	protected function _isAllowed()
	{
		return true;
	}
}
