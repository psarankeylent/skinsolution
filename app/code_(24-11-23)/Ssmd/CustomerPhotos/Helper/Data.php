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
    protected $url;

    const FULL_FACE = 'full_face';
    const FULL_GOVT = 'govt_id';

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Image\AdapterFactory $imageFactory,
        \Magento\Framework\Filesystem\DirectoryList $dirList,
        \Ssmd\CustomerPhotos\Model\CustomerPhotosFactory $customerphotos,
        \Magento\Backend\Model\Url $url
    ) {
        $this->_filesystem = $filesystem;
        $this->_customerSession = $customerSession;
        $this->_storeManager = $storeManager;
        $this->_imageFactory = $imageFactory;
        $this->_dirList = $dirList;
        $this->customerphotos = $customerphotos;
        $this->url = $url;

        parent::__construct($context);
    }


    public function imageResizeBeforeSaveImage($photoPath, $photoSrc)
    {
        try{
            
            $imageDestination = $photoPath;

            $sourceProperties = getimagesize($photoSrc);
            /*echo "<pre>";
            print_r($sourceProperties);
            exit; */
            $oldWidth  = $sourceProperties[0];
            $oldHeight = $sourceProperties[1];
            $imageType = $sourceProperties['mime'];

            switch ($imageType) {

                case 'image/png':
                    $imageSrc = imagecreatefrompng($photoSrc);
                    $tmp = $this->imageResizePhp($photoSrc, $imageSrc,$oldWidth,$oldHeight);
                    imagepng($tmp, $imageDestination);
                    break;
                case 'image/jpeg':
                    $imageSrc = imagecreatefromjpeg($photoSrc);
                    $tmp = $this->imageResizePhp($photoSrc,$imageSrc,$oldWidth,$oldHeight);
                    imagejpeg($tmp, $imageDestination);
                    break;
                /*case 'image/gif':
                    $imageSrc = imagecreatefromgif($photoSrc);
                    $tmp = $this->imageResizePhp($photoSrc,$imageSrc,$oldWidth,$oldHeight);
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
    public function getCustomerPhotoUrl()
    {
        return $this->url->getUrl('customerphotos');
    }
    public function getCreateDir()
    {
        return $this->_dirList->getRoot();
    }
    public function getRootDirPath()
    {
        return $this->_dirList->getRoot().'/secure/customer/photos';
    }
    public function getRootPath()
    {
        return $this->_storeManager->getStore()->getBaseUrl().'secure/customer/photos';
    }
}
