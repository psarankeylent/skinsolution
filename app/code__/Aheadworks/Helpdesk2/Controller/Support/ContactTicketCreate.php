<?php

namespace Aheadworks\Helpdesk2\Controller\Support;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\Redirect as ResultRedirect;
use Magento\Customer\Model\Session;
use Aheadworks\Helpdesk2\Api\TicketRepositoryInterface;
use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Model\Data\CommandInterface;
use Aheadworks\Helpdesk2\Model\Data\Processor\Post\ProcessorInterface;
use Aheadworks\Helpdesk2\Ui\DataProvider\Ticket\FormDataProvider as TicketFormDataProvider;


class ContactTicketCreate extends \Magento\Framework\App\Action\Action
{
    public function __construct(
        Context $context,
     //   TicketRepositoryInterface $ticketRepository,
       // Session $customerSession,
        DataPersistorInterface $dataPersistor,
        CommandInterface $saveCommand,
        ProcessorInterface $postDataProcessor
    ) {
        parent::__construct($context);
        $this->dataPersistor = $dataPersistor;
        $this->saveCommand = $saveCommand;
        $this->postDataProcessor = $postDataProcessor;
    }

    public function execute()
    {
        
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $resultFactory = $objectManager->create('\Magento\Framework\Controller\ResultFactory');


       
            $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
            $resultJson->setData([
                          'message' => 'Got it',
                          'status' => 1
                    ]);
            return $resultJson;

           
    } 
   
}
