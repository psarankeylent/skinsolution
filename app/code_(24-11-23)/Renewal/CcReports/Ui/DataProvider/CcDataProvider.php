<?php
namespace Renewal\CcReports\Ui\DataProvider;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\Reporting;

class CcDataProvider extends \Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider
{
    /**
     * Data Provider name
     *
     * @var string
     */
    protected $name;
    
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
        \Renewal\CcReports\Model\ResourceModel\CcReports\CollectionFactory $collectionFactory,
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
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->collectionFactory = $collectionFactory;
        $this->request = $request;

    }
    public function getData()
    {
        $collection = $this->collectionFactory->create();        
    
        $date = new \DateTime();
        $today = date('Y-m-d H:i:s');

        foreach ($this->data['config']['filter_url_params'] as $paramName => $paramValue) {
            if ('*' == $paramValue) {
                $paramValue = $this->request->getParam($paramName);
            }
            else{
                $paramValue = $this->request->getParam($paramName);
            }
        }
        /*echo $paramName;
        echo $paramValue;
        exit;*/
        
        if(isset($paramValue) && $paramValue == 30)
        {
            $beforeDate   = $date->modify("-30 days")->format('Y-m-d H:i:s');
            $collection->addFieldToFilter('expires', ['gteq' => $beforeDate]);
            $collection->addFieldToFilter('expires', ['lteq' => $today]);

           // echo $collection->getSelect(); exit;
        }
        elseif(isset($paramValue) && $paramValue == 60)
        {
            $beforeDate = $date->modify("-60 days")->format('Y-m-d H:i:s');
            $collection->addFieldToFilter('expires', ['gteq' => $beforeDate]);
            $collection->addFieldToFilter('expires', ['lteq' => $today]);
        }
        elseif(isset($paramValue) && $paramValue == 90)
        {
            $beforeDate = $date->modify("-90 days")->format('Y-m-d H:i:s');
            $collection->addFieldToFilter('expires', ['gteq' => $beforeDate]);
            $collection->addFieldToFilter('expires', ['lteq' => $today]);
        }
        
        // Filters
        $filters = $this->request->getParam('filters');
        if(isset($filters))
        {
            if(isset($filters['placeholder']))
            {
                unset($filters['placeholder']);
            }
            foreach ($filters as $key => $value) {
                
                if($key != 'expires')
                {
                    $collection->addFieldToFilter($key, $value);
                }
                
            }
        }
        
        // Sorting
        $sorting = $this->request->getParam('sorting');
        if(!empty($sorting))
        {
            if( !empty($sorting['field']) && !empty($sorting['direction']) )
             {
                if( ($sorting['field'] == 'active_subscription')||(($sorting['field'] == 'telephone')) )
                {}
                elseif($sorting['field'] == 'name')
                {
                    $sorting['field'] = 'firstname';
                    
                    $collection->addOrder(
                        $sorting['field'],
                        strtoupper($sorting['direction'])
                    );
                }
                else
                {
                    $collection->addOrder(
                        $sorting['field'],
                        strtoupper($sorting['direction'])
                    );
                }
  
            }            
        }
        $dt = $collection->getData();
        $newDt=[];
        if(!empty($dt))
        {
            foreach ($dt as $key => $value) {
                $value['id_field_name'] = 'id';

                $newDt[] = $value;
            }
        }

        // Paging code start
        
        $pagesize=20;
        $pageCurrent=1;
        $pageoffset=1;
        $page = $this->request->getParam('paging');
        if(isset($page))
        {
            $pagesize = intval($this->request->getParam('paging')['pageSize']);
            $pageCurrent = intval($this->request->getParam('paging')['current']);
            $pageoffset = ($pageCurrent - 1)*$pagesize;
        }

        // Paging code end

        $data = [
            'totalRecords' => count($newDt),
             'items' => array_slice($newDt,$pageoffset , $pagesize)
           // 'items' => array_slice($newDt,$pageoffset , $pageoffset+$pagesize)
            //'items' => $newDt
        ];
        
        return $data;
    }

        /**
     * @return int
     */
    public function getSize()
    {
        return AbstractDb::getSize();
    }

}


