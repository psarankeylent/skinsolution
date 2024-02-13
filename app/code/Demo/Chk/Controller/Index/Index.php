<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Demo\Chk\Controller\Index;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\PageFactory;

class Index implements HttpGetActionInterface
{

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * Constructor
     *
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        \CreditCard\Expiring\Model\CreditCardExpiringModelFactory $creditCardExpiringModelFactory,
        PageFactory $resultPageFactory)
    {
        
        $this->creditCardExpiringModelFactory = $creditCardExpiringModelFactory;
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Execute view action
     *
     * @return ResultInterface
     */
    public function execute()
    {

        echo "chk by skp for Demo Controller";

        /*

        $now                = new \DateTime();
        echo $toDate             = $now->format('Y-m-d 00:00:00');
        echo "<br>";
        echo $experationDays     = $now->modify("+30days")->format('Y-m-d 00:00:00');

        $objectManager  = \Magento\Framework\App\ObjectManager::getInstance();
        $resource       = $objectManager->get('Magento\Framework\App\ResourceConnection');
        $connection     = $resource->getConnection();

        $selectQuery = $connection->select()
            ->from(['sbs' => 'paradoxlabs_subscription'])
            ->join(['card' => 'paradoxlabs_stored_card'], 'card.customer_id = sbs.customer_id')
            ->join(['item' => 'quote_item'], 'item.quote_id = sbs.quote_id')
            ->where('sbs.status = ?', 'active')
            ->where('card.expires >= ?', $toDate)
            ->where('card.expires <= ?', $experationDays);
            //->order('sbs.next_run','ASC');
            //->group('card.id');
        */

        $objectManager  = \Magento\Framework\App\ObjectManager::getInstance();
        $resource       = $objectManager->get('Magento\Framework\App\ResourceConnection');
        $connection     = $resource->getConnection();

        $selectQuery = "select s.increment_id, s.quote_id, s.status, month(c.expires), p.cc_exp_month, year(c.expires), i.sku, s.next_run,i.name, ce.firstname, ce.lastname, ce.email, s.customer_id, c.id payment_card_id
            from paradoxlabs_subscription s
            join quote_payment p on p.quote_id = s.quote_id
            join paradoxlabs_stored_card c on c.customer_id = s.customer_id
            join quote_item i on i.quote_id=s.quote_id
            join catalog_product_entity e on e.entity_id =i.product_id
            join customer_entity ce on ce.entity_id=c.customer_id
            where c.active=1 and
            s.status = 'active'
            and p.cc_exp_month=month(c.expires)
            and c.expires>=curdate() and c.expires<=date_add(curdate(), interval 30 day)";

        $result = $connection->fetchAll($selectQuery);

        $newArr = array();
        foreach ($result as $val) {
            $newArr[$val['customer_id']] = $val;    
        }

        $result = array_values($newArr);

        echo count($result);
        echo "<pre>";
        print_r($result);





        

        


        exit;
        return $this->resultPageFactory->create();
    }
}


