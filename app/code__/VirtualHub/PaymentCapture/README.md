# Mage2 Module VirtualHub PaymentCapture

    ``virtualhub/module-paymentcapture``

 - [Main Functionalities](#markdown-header-main-functionalities)
 - [Installation](#markdown-header-installation)
 - [Configuration](#markdown-header-configuration)
 - [Specifications](#markdown-header-specifications)
 - [Attributes](#markdown-header-attributes)


## Main Functionalities
Test Module

## Installation

### Type 1: Zip file

 - Unzip the zip file in `app/code/VirtualHub`
 - Enable the module by running `php bin/magento module:enable VirtualHub_PaymentCapture`
 - Apply database updates by running `php bin/magento setup:upgrade --keep-generated`
 - Flush the cache by running `php bin/magento cache:flush`

### Type 2: Composer

 - Make the module available in a composer repository for example:
    - private repository `repo.magento.com`
    - public repository `packagist.org`
    - public github repository as vcs
 - Add the composer repository to the configuration by running `composer config repositories.repo.magento.com composer https://repo.magento.com/`
 - Install the module composer by running `composer require virtualhub/module-paymentcapture`
 - enable the module by running `php bin/magento module:enable VirtualHub_PaymentCapture`
 - apply database updates by running `php bin/magento setup:upgrade --keep-generated`
 - Flush the cache by running `php bin/magento cache:flush`


## Configuration




## Specifications

 - Cronjob
	- virtualhub_paymentcapture_paymentcaptureandinvoice


## Attributes



