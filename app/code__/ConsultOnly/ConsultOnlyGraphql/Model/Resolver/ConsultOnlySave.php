<?php

declare(strict_types=1);

namespace ConsultOnly\ConsultOnlyGraphql\Model\Resolver;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class ConsultOnlySave implements ResolverInterface
{

    private $consultOnlyDataProvider;

    /**
     * @param DataProvider\ConsultOnly $consultOnlyRepository
     */
    public function __construct(
        \Magento\Framework\HTTP\Client\Curl $curl,
        \VirtualHub\Config\Helper\Config $configHelper,
        \ConsultOnly\ConsultOnlyGraphql\Model\GraphQL $graphQL,
        \ConsultOnly\ConsultOnlyCollection\Model\ConsultOnlyFactory  $consultOnlyFactory
    ) {
        $this->curl = $curl;
        $this->graphQL = $graphQL;
        $this->configHelper = $configHelper;
        $this->consultOnlyFactory = $consultOnlyFactory;
    }

    /**
     * @inheritdoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null) {

        //$customer_id = $args['input']['customer_id'];
        $request['customer_id'] = $args['input']['customer_id'];
        $request['prescription_id'] = '';
        $request['action'] = "consult_only";
        
        $bearerToken = $this->configHelper->getVirtualHubBearerToken();
        if($bearerToken['success'] == True){
            $token = $bearerToken['token'];
            $vhUrl = $this->configHelper->getConsultOnlyStatus();
            $headers = ["Content-Type" => "application/json", "Authorization" => 'Bearer '.$token];
            $this->curl->setHeaders($headers);
            $this->curl->post($vhUrl, json_encode($request));
            $response = $this->curl->getBody();
        }


        return true;
    }


    protected function validateInput($args)
    {
        $inputs = ['prescription_id','customer_id'];

        if(!empty($inputs))
        {
            foreach ($inputs as $key => $value) {
                $inputValue = $args['input'][$value];
                if (!isset($inputValue) || empty($inputValue) ) {
                    throw new GraphQlInputException(__($inputs[$key]." value must be specified"));
                }
            }
            
        }
    }

}

