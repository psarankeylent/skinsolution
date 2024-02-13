<?php
namespace CustomReports\BatchListReport\Ui\DataProvider;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\Reporting;

class BatchStatsDataProvider extends \Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider
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
        \CustomReports\BatchListReport\Model\ResourceModel\BatchStatsReport\CollectionFactory $collectionFactory,
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
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $request = $objectManager->get('Magento\Framework\App\RequestInterface');
        $collection = $this->collectionFactory->create();

        foreach ($this->data['config']['filter_url_params'] as $paramName => $paramValue) {
            if ('*' == $paramValue) {
                $paramValue = $this->request->getParam($paramName);
            }
            else{
                $paramValue = $this->request->getParam($paramName);
            }
        }
        if($paramValue != '*' && $paramValue != "")
        {
            // Map fields to avoid ambiguous column errors on filtering
            $collection->addFilterToMap('batch_id', 'main_table.batch_id');

            $collection->addFieldToFilter('batch_id', ['eq' => $paramValue]);
        }
        // Sorting
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
        ];
        //echo "<pre>"; print_r($data); exit;
        return $data;
    }

}


