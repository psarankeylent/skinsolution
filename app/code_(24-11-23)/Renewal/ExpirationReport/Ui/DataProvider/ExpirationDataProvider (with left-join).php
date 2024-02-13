<?php
namespace Renewal\ExpirationReport\Ui\DataProvider;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\Reporting;

class ExpirationDataProvider extends \Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider
{
    
    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param Reporting $reporting
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param RequestInterface $request
     * @param FilterBuilder $filterBuilder
     * @param array $meta
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        Reporting $reporting,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        RequestInterface $request,
        FilterBuilder $filterBuilder,
        \Renewal\ExpirationReport\Model\ResourceModel\ExpirationReport\CollectionFactory $collectionFactory,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $reporting,
            $searchCriteriaBuilder,
            $request,
            $filterBuilder,
            $meta,
            $data
        );
        $this->collectionFactory = $collectionFactory;
        $this->request = $request;

    }
    public function getData()
    {
        /*foreach ($this->data['config']['filter_url_params'] as $paramName => $paramValue) {
            if ('*' == $paramValue) {
                $paramValue = $this->request->getParam($paramName);
            }
            if ($paramValue) {
                $this->data['config']['update_url'] = sprintf(
                    '%s%s/%s/',
                    $this->data['config']['update_url'],
                    $paramName,
                    $paramValue
                );

                echo $paramValue; exit;
                $this->addFilter(
                    $this->filterBuilder->setField($paramName)->setValue($paramValue)->setConditionType('leq')->create()
                );
            }
        } 
       $dd = $this->data['config']['filter_url_params'];
       echo "<pre>";
       print_r($dd);
        exit;*/

        $collection = $this->collectionFactory->create();        
    
        // Join the fields
        $query = $collection
                    ->getSelect()
                    ->joinLeft(['cust_ent' => $collection->getTable('customer_entity')], 'main_table.customer_id = cust_ent.entity_id', array('email','firstname'))
                    ->joinLeft(['mh' => $collection->getTable('medical_history')], 'main_table.customer_id = mh.customer_id', array('mh.response as telephone'))
                    //->group('mh.customer_id')
                    ->where('mh.question_id=6')                    
                    ;

        // Map fields to avoid ambigious error.
        $collection->addFilterToMap('email', 'cust_ent.email');
        $collection->addFilterToMap('firstname', 'cust_ent.firstname');
        $collection->addFilterToMap('response', 'mh.response');
        $collection->addFilterToMap('id', 'main_table.id');

        //echo $query; exit;
        echo "<pre>"; print_r($collection->getData()); exit;


        $date = new \DateTime();
        
        //$data = parent::getData();
        $expDays = $this->request->getParam('expiration_date');

        if(isset($expDays) && $expDays == 30)
        {
            $beforeDate   = $date->modify("-30 days")->format('Y-m-d H:i:s');
            $collection->addFieldToFilter('expiration_date', ['gteq' => $beforeDate]);
        }
        elseif(isset($expDays) && $expDays == 60)
        {
            $beforeDate = $date->modify("-60 days")->format('Y-m-d H:i:s');
            $collection->addFieldToFilter('expiration_date', ['gteq' => $beforeDate]);
        }
        elseif(isset($expDays) && $expDays == 90)
        {
            $beforeDate = $date->modify("-90 days")->format('Y-m-d H:i:s');
            $collection->addFieldToFilter('expiration_date', ['gteq' => $beforeDate]);
        }
        
        // Custom table filters with Join
        $filters = $this->request->getParam('filters');
        if(isset($filters))
        {
            if(isset($filters['placeholder']))
            {
                unset($filters['placeholder']);
            }            
            foreach ($filters as $key => $value) {
                if($key == 'email')
                {
                    
                    $collection->addFieldToFilter('customer_entity.email', array('like' => $value));

                }
                elseif($key == 'firstname')
                {
                   /* $collection->getSelect()->join('customer_entity', 'main_table.customer_id = customer_entity.entity_id', array('*'));*/

                   $collection->getSelect()->join(
                        ['ce' => 'customer_entity'],
                        'main_table.customer_id = ce.entity_id',
                        ['firstname' => 'firstname', 'lastname' => 'lastname']);

                   /* echo $collection->getSelect()->join(
                        ['ce' => 'customer_entity'],
                        'main_table.customer_id = ce.entity_id',
                        ['firstname' => 'firstname', 'lastname' => 'lastname']
                        )->columns(new \Zend_Db_Expr("CONCAT(`ce`.`firstname`, ' ',`ce`.`lastname`) AS name")
                        )->where("'ce.name'='Mahendra j'");
                        exit;*/
                   
                   // $collection->addFieldToFilter('ce.firstname', $value);

                    //echo "<pre>"; print_r($collection->getData()); exit;
                    //$collection->addNameToSelect();
                    //$collection->addFilterToMap('customer_name', 'customer_entity.firstname');
                    //$collection->addFilterToMap('customer_name', 'customer_entity.lastname');
                    //$collection->addFieldToFilter('customer_name', $value);

                    // Filter with OR conditions firstname, lastname
                    /*$collection->addFieldToFilter(
                        ['ce.firstname', 'ce.lastname'],
                            [
                                ['eq' => $value],
                                ['eq' => $value]               
                            ]
                        );*/

                   // 'name',['like'=>($keyword . '%')]
                   
                    //$this->filterCustomersByFullname($collection, $value);

                   
                    $collection->addFieldToFilter(
                        ['ce.firstname', 'ce.lastname'],
                            [
                                ['eq' => $value],
                                ['eq' => $value]             
                            ]
                        );
                        

                }
                elseif($key == 'telephone')
                {
                    /*$collection->getSelect()->joinLeft('sales_order as so', 'main_table.customer_id = so.customer_id', array('so.entity_id as order_id'));*/

                    echo $collection->getSelect()->join('sales_order_address soa', 'main_table.customer_id = soa.customer_id', array('soa.telephone')); exit;

                  //  echo "<pre>"; print_r($collection->getData()); exit;
                    
                    //$collection->addFieldToFilter('sales_order.telephone', ['eq' => $value]);
                }
                else{
                     $collection->addFieldToFilter($key, $value);
                }          
               
            }            
        }
        $sorting = $this->request->getParam('sorting');
        if(!empty($sorting))
        {
            if( !empty($sorting['field']) && !empty($sorting['direction']) )
             {
                $collection->addOrder(
                    $sorting['field'],
                    strtoupper($sorting['direction'])
                );
  
            }            
        }
        
        $dt = $collection->getData();
        //echo "<pre>"; print_r($dt); exit;
        $newDt=[];
        if(!empty($dt))
        {
            foreach ($dt as $key => $value) {
                $value['id_field_name'] = 'id';

                $newDt[] = $value;
            }
        }
        $page = $this->request->getParam('paging');

        $pageoffset=0;
        $pagesize=2;
        if(isset($page))
        {
            $pagesize = intval($this->request->getParam('paging')['pageSize']);
            $pageCurrent = intval($this->request->getParam('paging')['current']);
            $pageoffset = ($pageCurrent - 1)*$pagesize;
        }
        $data = [
            'totalRecords' => count($newDt),
            'items' => array_slice($newDt,$pageoffset , $pageoffset+$pagesize),
        ];

        //echo "<pre>"; print_r($data); exit;
        return $data;
    }

}


