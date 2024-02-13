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

        if(isset($paramValue) && $paramValue == 30)
        {
            $beforeDate   = $date->modify("-30 days")->format('Y-m-d H:i:s');
            $collection->addFieldToFilter('expiration_date', ['gteq' => $beforeDate]);
            $collection->addFieldToFilter('expiration_date', ['lteq' => $today]);
        }
        elseif(isset($paramValue) && $paramValue == 60)
        {
            $beforeDate = $date->modify("-60 days")->format('Y-m-d H:i:s');
            $collection->addFieldToFilter('expiration_date', ['gteq' => $beforeDate]);
            $collection->addFieldToFilter('expiration_date', ['lteq' => $today]);
        }
        elseif(isset($paramValue) && $paramValue == 90)
        {
            $beforeDate = $date->modify("-90 days")->format('Y-m-d H:i:s');
            $collection->addFieldToFilter('expiration_date', ['gteq' => $beforeDate]);
            $collection->addFieldToFilter('expiration_date', ['lteq' => $today]);
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

                if($key != 'expiration_date')
                {
                    $collection->addFieldToFilter($key, $value);
                }
            }
        }
        $sorting = $this->request->getParam('sorting');
        if(!empty($sorting))
        {
            if( !empty($sorting['field']) && !empty($sorting['direction']) )
            {
                if( $sorting['field'] == 'telephone')
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
        //echo "<pre>"; print_r($dt); exit;
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

        return $data;
    }

}


