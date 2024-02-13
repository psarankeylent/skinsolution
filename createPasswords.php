<?php

// Ref URL : https://devdocs.magento.com/guides/v2.3/config-guide/secy/hashing.html

//NOtes : 
// php bin/magento customer:hash:upgrade command upgrades a customer password hash to the latest hash algorithm.

// 1) The \Magento\Framework\Encryption\Encryptor class is responsible for password hash generation and verification

// RUN this file on browser : http://127.0.0.1/ssmd/createPasswords.php

ini_set('display_errors', 1);
ini_set('memory_limit', '5G');
error_reporting(E_ALL);

use Magento\Framework\App\Bootstrap;
require 'app/bootstrap.php';

$bootstrap = Bootstrap::create(BP, $_SERVER);

$objectManager = $bootstrap->getObjectManager();

//echo "call"; exit;

$state = $objectManager->get('Magento\Framework\App\State');
$state->setAreaCode('frontend');

?>

<?php

$emailArray = ['hitesh@skinsolutions.md'];      // array of email ids.

$encryptorObj = $objectManager->get('Magento\Framework\Encryption\EncryptorInterface');
$customerFactory = $objectManager->get('Magento\Customer\Model\CustomerFactory');

if(!empty($emailArray))
{
    $password = "Hitesh&123";           // Make a password has for which you want.
    foreach ($emailArray as $email) {
        $customerConnection = $customerFactory->create()->getCollection();

        $customerData = $customerConnection
                                        ->addFieldToFilter('email', array('email', $email))
                                        ->getFirstItem();

        $customerId = $customerData->getData('entity_id');
        $customer = $customerFactory->create()->load($customerId);

        $passwdHash = $encryptorObj->getHash($password,true);

        $customer->setPasswordHash($passwdHash)
                ->save();

    }

    echo "Password succesfully updated."
}

        
        




