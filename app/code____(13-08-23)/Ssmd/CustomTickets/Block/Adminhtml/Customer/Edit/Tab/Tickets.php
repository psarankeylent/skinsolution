<?php

namespace Ssmd\CustomTickets\Block\Adminhtml\Customer\Edit\Tab;

use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Customer\Controller\RegistryConstants;


class Tickets extends \Magento\Backend\Block\Template implements \Magento\Ui\Component\Layout\Tabs\TabInterface
{
    /**
     * Template
     *
     * @var string
     */
    protected $_template = 'tab/tickets.phtml';
    protected $formKey;
    protected $customerFactory;
    protected $customTicketsFactory;
    protected $configHelper;
    protected $curl;

    /**
     * View constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\Form\FormKey $formKey,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Ssmd\CustomTickets\Model\CustomTicketsFactory $customTicketsFactory,
        \VirtualHub\Config\Helper\Config $configHelper,
        \Magento\Framework\HTTP\Client\Curl $curl,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->formKey = $formKey;
        $this->customerFactory = $customerFactory;
        $this->customTicketsFactory = $customTicketsFactory;
        $this->configHelper = $configHelper;
        $this->curl = $curl;
        parent::__construct($context, $data);
    }

    public function searchCustomerContact($customerId){
        $customer = $this->customerFactory->create()->load($customerId);
        $email = $customer->getData('email');
        
        // Search contact api function
        $customerId = $this->getSearchContactApi($email);
        
        // Get contact conversation ticket data via customer_id
        $ticketData = [];
        $ticketData = $this->getContactConversationsApi($customerId);
        return $ticketData;
    }
    
    function getSearchContactApi_old($email){
       
        // Header call
        $headers = $this->getHeaders();

        $url = $this->configHelper->getContactSearchApiUrl().'?q='.$email;
        //$url = 'https://inbox.skinsolutions.md/api/v1/accounts/2/contacts/search?q='.$email;
        

        $apiKey = $this->configHelper->getApiAccessKey();
        $this->curl->setHeaders($headers);
        //$this->curl->setCredentials('api_access_token', $apiKey);
        $this->curl->get($url);
        $response = $this->curl->getBody();
        
        echo "<pre>"; print_r($response); exit;
        
        $customerId = null;
        if($response->meta->count > 0)
        {
            $res = $response->payload;
            $customerId = $res[0]->id;

            if(isset($customerId) && $customerId != null)
            {
                // Save data with customer_id in table.
                $this->saveData($customerId);   

                return $customerId;
            }                    
        }
        return $customerId;
    }

    function getSearchContactApi($email){
        $ch = curl_init();
        // Header call
        $headers = $this->getHeaders();

        $url = $this->configHelper->getContactSearchApiUrl().'?q='.$email;
        //$url = 'https://inbox.skinsolutions.md/api/v1/accounts/2/contacts/search?q='.$email;
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = json_decode(curl_exec($ch));
        //echo "<pre>"; print_r($response); exit;
        
        $customerId = null;
        if($response->meta->count > 0)
        {
            $res = $response->payload;
            $customerId = $res[0]->id;

            if(isset($customerId) && $customerId != null)
            {
                // Save data with customer_id in table.
                $this->saveData($customerId);   

                return $customerId;
            }                    
        }
        return $customerId;
    }
    
    public function getContactConversationsApi($customerId)
    {
        $ch = curl_init();

        // Header call
        $headers = $this->getHeaders();

        $url = $this->configHelper->getContactConversationsApiUrl().'/'.$customerId.'/conversations';
        //$url = 'https://inbox.skinsolutions.md/api/v1/accounts/2/contacts/'.$customerId.'/conversations';
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = json_decode(curl_exec($ch));

        $dataArray = [];
        if(!empty($response->payload))
        {
            foreach($response->payload as $message){
                $data['last_non_activity_message'] = $message->last_non_activity_message;
                $dataArray[] = $data;
            }
        }
        return $dataArray;        
    }

    public function saveData($customerId)
    {
        $model = $this->customTicketsFactory->create();
        $model->setData('customer_id', $customerId)
              ->save();
    }

    public function getHeaders()
    {
        $apiKey = $this->configHelper->getApiAccessKey();

        $headers = array(
            'Content-Type: application/json',
            'api_access_token: '.$apiKey.''
        );
        //echo "<pre>"; print_r($headers); exit;
        return $headers;
    }
    

    /**
     * @return string|null
     */
    public function getCustomerId()
    {
        return $this->_coreRegistry->registry(\Magento\Customer\Controller\RegistryConstants::CURRENT_CUSTOMER_ID);
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Tickets');
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Tickets');
    }

    /**
     * @return bool
     */
    public function canShowTab()
    {
        if ($this->getCustomerId()) {
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function isHidden()
    {
        if ($this->getCustomerId()) {
            return false;
        }
        return true;
    }

    /**
     * Tab class getter
     *
     * @return string
     */
    public function getTabClass()
    {
        return '';
    }

    /**
     * Return URL link to Tab content
     *
     * @return string
     */
    public function getTabUrl()
    {
        return '';
    }

    /**
     * Return URL link to Tab content
     *
     * @return string
     */
    public function getDownloadUrl()
    {
        return $this->getUrl('customtickets/index/index');
    }

    /**
     * Tab should be loaded trough Ajax call
     *
     * @return bool
     */
    public function isAjaxLoaded()
    {
        return false;
    }

    public function getFormKey()
    {
        return $this->formKey->getFormKey();
    }

}
