<?php

namespace Ssmd\CustomerPhotos\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $_filesystem;
    protected $_customerSession;
    protected $_storeManager;
    protected $_imageFactory;
    protected $_dirList;
    protected $customerphotos;
    protected $customerPhotosOrders;
    protected $url;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Image\AdapterFactory $imageFactory,
        \Magento\Framework\Filesystem\DirectoryList $dirList,
        \Ssmd\CustomerPhotos\Model\CustomerphotosFactory $customerphotos,
        \Ssmd\CustomerPhotos\Model\PrescriptionCustomerPhotosOrdersFactory $customerPhotosOrders,
        \Magento\Backend\Model\Url $url
    ) {
        $this->_filesystem = $filesystem;
        $this->_customerSession = $customerSession;
        $this->_storeManager = $storeManager;
        $this->_imageFactory = $imageFactory;
        $this->_dirList = $dirList;
        $this->customerphotos = $customerphotos;
        $this->customerPhotosOrders = $customerPhotosOrders;
        $this->url = $url;

        parent::__construct($context);
    }


    public function imageResizeBeforeSaveImage($image, $uploadedFile, $photoDirName, $customerId)
    {

        try{
            // $imageDestination = '/var/www/enhance/pub/media/images/'.$photoDirName.'/'.$customerId.'/'.$image;
            $imageDestination = $this->getMediaDirectoryUrl().'/images/'.$photoDirName.'/'.$customerId.'/'.$image;


            $sourceProperties = getimagesize($uploadedFile);
            /*echo "<pre>";
            print_r($sourceProperties);
            exit; */
            $oldWidth  = $sourceProperties[0];
            $oldHeight = $sourceProperties[1];
            $imageType = $sourceProperties['mime'];


            switch ($imageType) {

                case 'image/png':
                    $imageSrc = imagecreatefrompng($uploadedFile);
                    $tmp = $this->imageResizePhp($uploadedFile, $imageSrc,$oldWidth,$oldHeight);
                    imagepng($tmp, $imageDestination);
                    break;
                case 'image/jpeg':
                    $imageSrc = imagecreatefromjpeg($uploadedFile);
                    $tmp = $this->imageResizePhp($uploadedFile,$imageSrc,$oldWidth,$oldHeight);
                    imagejpeg($tmp, $imageDestination);
                    break;
                /*case 'image/gif':
                    $imageSrc = imagecreatefromgif($uploadedFile);
                    $tmp = $this->imageResizePhp($uploadedFile,$imageSrc,$oldWidth,$oldHeight);
                    imagegif($tmp, $imageDestination);
                    break;
                */

            }

        }catch (\Exception $e) {
            //echo $e->getMessage(); exit;
            $response_array['error'] = 1;
            $response_array['errorMessage'] = $e->getMessage();

            echo json_encode($response_array);
            exit;

            $this->_messageManager->addError('Only jpg, jpeg, png and gif file type is allowed.');
        }


        // Image save to destination folder

        //$resizedImgURL = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).'images/'.$photoDirName.'/'.$customerId/'.$image;
        //return $resizedImgURL;
    }

    public function imageResizePhp($uploadedFile, $imageSrc, $orig_imageWidth = null, $orig_imageHeight = null)
    {
        $newImageWidth = 400;
        //$newImageHeight = 300;


        /* Calculate aspect ratio by dividing height by width */
        $aspectRatio = $orig_imageHeight / $orig_imageWidth;
        /* Keep the width fix but change the height according to the aspect ratio */
        $newImageHeight = (int)($aspectRatio * $newImageWidth);
        //$newImageWidth .= "px";


        $newImageLayer = imagecreatetruecolor($newImageWidth, $newImageHeight);


        // Handle image transparency for resized image
        if (function_exists('imagealphablending'))
        {
            imagealphablending($newImageLayer, false);
            imagesavealpha($newImageLayer, true);
        }

        imagecopyresampled($newImageLayer, $imageSrc, 0, 0, 0, 0, $newImageWidth, $newImageHeight, $orig_imageWidth,$orig_imageHeight);

        return $newImageLayer;
    }

    public function getCustomerId()
    {
        if($this->_customerSession->isLoggedIn())
        {
            return $this->_customerSession->getCustomer()->getId();
        }
        return null;

    }
    public function getCustomerSession()
    {
        return $this->_customerSession;
    }

    public function getMediaUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }
    public function getMediaDirectoryUrl()
    {
        return $this->_dirList->getPath('media');
    }

    /* Photo urls */
    public function getFacephotoUrl()
    {
        return $this->getMediaUrl().'images/facePhotoDir/';
    }
    public function getTopHeadphotoUrl()
    {
        return $this->getMediaUrl().'images/topHeadPhotoDir/';
    }
    public function getSideHeadphotoUrl()
    {
        return $this->getMediaUrl().'images/sideHeadPhotoDir/';
    }
    public function getPhotoIdUrl()
    {
        return $this->getMediaUrl().'images/govtIssuedPhoto/';
    }

    /* Directory urls */
    public function getFacephotoDirUrl()
    {
        return $this->_dirList->getPath('media').'/images/facePhotoDir/';
    }
    public function getTopHeadphotoDirUrl()
    {
        return $this->_dirList->getPath('media').'/images/topHeadPhotoDir/';
    }
    public function getSideHeadphotoDirUrl()
    {
        return $this->_dirList->getPath('media').'/images/sideHeadPhotoDir/';
    }
    public function getPhotoIdDirUrl()
    {
        return $this->_dirList->getPath('media').'/images/govtIssuedPhoto/';
    }
    /*
    * Photo Govt ID
    */
    public function getGovtPhotoId($customerId)
    {
        $customerPhotosCollection = $this->customerphotos->create()->getCollection();

        $customerPhotosCollection->addFieldToFilter('mage_customer_id',
            ['mage_customer_id'=>$customerId]);
        $customerPhotosCollection->addFieldToFilter('photo_type_id',
            ['photo_type_id'=>4]);
        $customerPhotosCollection->addFieldToFilter('status',
            ['status'=>1]);
        $customerPhotosCollection->addFieldToFilter('is_customer_level',
            ['is_customer_level'=>1]);

        $customerPhotosCollection->setOrder('customer_photo_id','DESC');
        //echo "<pre>"; print_r($customerPhotosCollection->getData()); exit;
        $photo = null;
        foreach ($customerPhotosCollection as $customerPhoto) {
            $photo = $customerPhoto->getPhotoFilePath();
            break; // Break when got first record latest
        }
        if($photo == "" || $photo == null)
        {
            return false;
        }
        return $this->getPhotoIdUrl().$customerId.'/'.$photo;
    }

    // Check if customer prescriptions is active/approved by order obj's items.
    public function checkCustomerPrescriptionStatusByOrder($order)
    {
        /*$objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        // Array
        $actualCustomerPrescriptionIdsArray = array();
        foreach($order->getAllItems() as $item)
        {
            $item_id = $item->getItemId();
            $actualPrescriptionOrder = $objectManager->create('Enhance\Prescriptions\Model\CustomerPrescriptionOrders')
                                   ->load($item_id, 'order_item_id');

            $actualCustomerPrescriptionIdsArray[] = $actualPrescriptionOrder->getCustomerPrescriptionId();
        }

        // $photo upload button disapper
        $photoUploadButton = 0;
        foreach ($actualCustomerPrescriptionIdsArray as $custPreId) {

            $actualCustomerPrescriptionObj = $objectManager->create('Enhance\Prescriptions\Model\CustomerPrescriptions')->load($custPreId);

            $actualPrescriptionItemStatus = $actualCustomerPrescriptionObj->getStatus();
            if( (strtolower($actualPrescriptionItemStatus) == 'active')||
                (strtolower($actualPrescriptionItemStatus) == 'prescription approved') )
            {
                $photoUploadButton = 1;
            }
        }

        return $photoUploadButton; */
    }

    /*
    * Customer photo orders collections by order id
    */
    public function getCustomerPhotoOrder($orderId)
    {
        $customerPhotosOrderCollection = $this->customerPhotosOrders->create()->getCollection();
        $customerPhotosOrderCollection->addFieldToFilter('order_id', array('orderId' => $orderId));
        return $customerPhotosOrderCollection;
    }

    public function getCustomerPhotoUrl()
    {
        return $this->url->getUrl('customerphotos');
    }
}
