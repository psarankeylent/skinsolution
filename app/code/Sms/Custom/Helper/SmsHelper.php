<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Sms\Custom\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class SmsHelper extends AbstractHelper
{

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context
    ) {
        parent::__construct($context);
    }

    public function sendSms($smsText,$mobile){

        $data['Body']   = $smsText;
        $data['From']   = "+18559093281";
        $data['To']     = $mobile;
        $query = http_build_query($data);
        $ch = curl_init("https://api.twilio.com/2010-04-01/Accounts/AC7d9b8fda5b6962f0cebe2383d11e1ddc/Messages.json");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");    
        curl_setopt($ch, CURLOPT_USERPWD,'AC7d9b8fda5b6962f0cebe2383d11e1ddc:6d990edd30283ce81b75a36764a7bfe6');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded')); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);

        $responseHttp = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/sms.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info('response = '.$response); 


    }


}

