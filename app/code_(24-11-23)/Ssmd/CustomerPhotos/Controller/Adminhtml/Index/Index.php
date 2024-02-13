<?php
namespace Ssmd\CustomerPhotos\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;


class Index extends Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Backend\Model\View\Result\Page
     */
    protected $resultPage;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */

    protected $_fileUploaderFactory;
    protected $_messageManager;
    protected $_customerphotosFactory;
    protected $prescriptionCustomerPhotosOrdersFactory;
    protected $storeManager;
    protected $_encryptor;
    protected $_dataHelper;
    protected $orderFactory;
    protected $medicalHistoryFactory;
    protected $redirectFactory;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Magento\Framework\HTTP\Client\Curl $curl,
        \VirtualHub\Config\Helper\Config $configHelper,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Ssmd\CustomerPhotos\Model\CustomerPhotosFactory $customerPhotosFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor,
        \Ssmd\CustomerPhotos\Helper\Data $dataHelper,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Ssmd\MedicalHistory\Model\CustomerMedicalHistoryFactory $medicalHistoryFactory,
        \Magento\Framework\Controller\Result\RedirectFactory $redirectFactory
    )
    {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->_mediaDirectory = $filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        $this->_fileUploaderFactory = $fileUploaderFactory;
        $this->_messageManager = $messageManager;
        $this->_customerPhotosFactory = $customerPhotosFactory;
        $this->storeManager = $storeManager;
        $this->_encryptor = $encryptor;
        $this->_dataHelper = $dataHelper;
        $this->orderFactory = $orderFactory;
        $this->medicalHistoryFactory = $medicalHistoryFactory;
        $this->redirectFactory = $redirectFactory;
        $this->curl = $curl;
        $this->configHelper = $configHelper;
    }

    public function execute()
    {
        //echo "called admin"; exit;
        $this->resultPage = $this->resultPageFactory->create();

        try{

            // Customer ID

            $response_array = array();
            $customerId = $this->getRequest()->getParam('customer_id');
            $incrementId = $this->getRequest()->getParam('increment_id');
            $orderId = $this->getRequest()->getParam('order_id');
            $facePhotoType = $this->getRequest()->getParam('face_photo_type');
            $govtPhotoType = $this->getRequest()->getParam('govt_photo_type');

            // Directories Dynamically created.
            $dirPath = $this->createRecursiveDirectories($customerId);

            $facePhotoId = null;
            if($this->getRequest()->getParam('face_photo_id') != "")
            {
                $facePhotoId = $this->getRequest()->getParam('face_photo_id');
            }
            $govtPhotoId = null;
            if($this->getRequest()->getParam('govt_photo_id') != "")
            {
                $govtPhotoId = $this->getRequest()->getParam('govt_photo_id');
            }

//===================================================== FACE PHOTO UPLOAD CODE ========================================

            $face_files = $this->getRequest()->getFiles('face_photo');
            $files      = $this->getRequest()->getFiles('govt_photo');

            if( (isset($face_files['size']) && $face_files['size'] == 0)&&( isset($files['size']) && $files['size'] == 0) )
            {
                $this->_messageManager->addError('You have to upload at least one photo');

                $resultRedirect = $this->redirectFactory->create();
                $resultRedirect->setPath('sales/order/view/order_id/'.$orderId);
                return $resultRedirect;

            }

            $fileName_facePhoto = ($face_files && array_key_exists('name', $face_files)) ? $face_files['name'] : null;
            $fileName_facePhoto = str_replace(" ", "_", strtolower($fileName_facePhoto));
            if ($face_files && $fileName_facePhoto) {

                /** @var $uploader \Magento\MediaStorage\Model\File\UploaderFactory */
                $uploader = $this->_fileUploaderFactory->create(['fileId' => 'face_photo']);

                // set allowed file extensions
                $uploader->setAllowedExtensions(['jpg','jpeg','png','heic']);

                // Allow folder creation if not exists
                $uploader->setAllowCreateFolders(true);

                // Rename file if already exists it set true(like filename.jpg, filename_1.jpg etc), and set false it overrides into dir
                $uploader->setAllowRenameFiles(false);

                $fileName_facePhoto = preg_replace("/[^A-Za-z0-9-.]/", '_', $fileName_facePhoto);
                $newStr = explode('.', $fileName_facePhoto);
                $face_fileExt = end($newStr);

                $imagePath = $dirPath.'/'.$customerId.'_'.$facePhotoType.'_'.time().'.'.$face_fileExt;
                $dirFullPath = $this->_dataHelper->getRootDirPath().$imagePath;
                $photoPath = $this->_dataHelper->getRootPath().$imagePath;

                // =================== Image Resize before save to directory and db ==============================


                $uploadedFile = $face_files['tmp_name'];
                $resizedImgURL = $this->_dataHelper->imageResizeBeforeSaveImage($dirFullPath, $uploadedFile);

                // =================== Image Resize before save to directory and db ==============================


                // Old record needs to do be set status zero.
                $photoCollection = $this->_customerPhotosFactory->create()->getCollection();
                $photoCollection->addFieldToFilter('customer_id', $customerId);
                $photoCollection->addFieldToFilter('photo_type', $facePhotoType);
                $photoCollection->addFieldToFilter('status', 1);

                //print_r($photoCollection->getData()); exit;

                if(!empty($photoCollection))
                {
                    $photoModel = $this->_customerPhotosFactory->create();
                    foreach ($photoCollection as $photo) {
                        $photoModel->load($photo->getPhotoId())
                            ->setStatus(0)
                            ->save();
                    }

                }

                // New record will be adding by creating new Object
                $facesavephotoModelNewObj = $this->_customerPhotosFactory->create();
                $facesavephotoModelNewObj->setCustomerId($customerId)
                    ->setPhotoType($facePhotoType)
                    ->setPath($imagePath)
                    ->setSourceSystem($face_files['tmp_name'])
                    ->setStatus(1)
                    //->setIncrementId($incrementId)
                    ->setCreatedAt(date('Y-m-d h:i:s'))
                    ->save();

                $newFullFaceId = $facesavephotoModelNewObj->getId();

                // ================== Update Medcial History Start =============

                // Get data
                $medicalHistoryCollection = $this->medicalHistoryFactory->create()->getCollection();
                $medicalHistoryCollection->addFieldToFilter('order_number', $incrementId);
                $medicalHistoryCollection->addFieldToFilter('customer_id', $customerId);
                $medicalHistoryCollection->addFieldToFilter('full_face', $facePhotoId);

                //echo "<pre>"; print_r($medicalHistoryCollection->getData()); exit;

                // Save data
                if(!empty($medicalHistoryCollection->getData()))
                {
                    // Creating Medical History New Model Obj
                    $medicalHistoryModel = $this->medicalHistoryFactory->create();

                    foreach ($medicalHistoryCollection->getData() as $medicalHistory) {
                        $fullFaceId = $medicalHistory['id'];

                        if(isset($fullFaceId) && $fullFaceId != null)
                        {
                            $medicalHistoryModel->load($fullFaceId);
                        }

                        $medicalHistoryModel->setData('full_face', $newFullFaceId)
                            ->save();
                    }
                }

                // ================== Update Medcial History End =============

            }


            //================================= Govt PHOTOID UPLOAD CODE ================================================================


            $fileName = ($files && array_key_exists('name', $files)) ? $files['name'] : null;
            $fileName = str_replace(" ", "_", strtolower($fileName));

            if ($files && $fileName) {

                /** @var $uploader \Magento\MediaStorage\Model\File\UploaderFactory */
                $uploader = $this->_fileUploaderFactory->create(['fileId' => 'govt_photo']);

                // set allowed file extensions
                $uploader->setAllowedExtensions(['jpg','jpeg','png','heic']);

                // Allow folder creation if not exists
                $uploader->setAllowCreateFolders(true);

                // Rename file if already exists it set true(like filename.jpg, filename_1.jpg etc), and set false it overrides into dir
                $uploader->setAllowRenameFiles(false);


                $fileName = preg_replace("/[^A-Za-z0-9-.]/", '_', $fileName);
                $newStr = explode('.', $fileName);
                $fileExt = end($newStr);

                $imagePath = $dirPath.'/'.$customerId.'_'.$govtPhotoType.'_'.time().'.'.$fileExt;
                $dirFullPath = $this->_dataHelper->getRootDirPath().$imagePath;
                $photoPath = $this->_dataHelper->getRootPath().$imagePath;


                // =================== Image Resize before save to directory and db ==============================

                $uploadedFile = $files['tmp_name'];
                $resizedImgURL = $this->_dataHelper->imageResizeBeforeSaveImage($dirFullPath, $uploadedFile);

                // =================== Image Resize before save to directory and db ==============================


                // Old record needs to do be set status zero.
                $photoCollection = $this->_customerPhotosFactory->create()->getCollection();
                $photoCollection->addFieldToFilter('customer_id', $customerId);
                $photoCollection->addFieldToFilter('photo_type', $govtPhotoType);
                $photoCollection->addFieldToFilter('status', 1);

                //print_r($photoCollection->getData()); exit;

                if(!empty($photoCollection))
                {
                    $photoModel = $this->_customerPhotosFactory->create();
                    foreach ($photoCollection as $photo) {
                        $photoModel->load($photo->getPhotoId())
                            ->setStatus(0)
                            ->save();
                    }

                }

                // New record will be adding by creating new Object
                $photoModelNewObj = $this->_customerPhotosFactory->create();
                $photoModelNewObj->setCustomerId($customerId)
                    ->setPhotoType($govtPhotoType)
                    ->setPath($imagePath)
                    ->setSourceSystem($files['tmp_name'])
                    ->setStatus(1)
                    //->setIncrementId($incrementId)
                    ->setCreatedAt(date('Y-m-d h:i:s'))
                    ->save();




                $newGovtPhotoId = $photoModelNewObj->getId();

                // ================== Update Medcial History Start =============

                // Get data
                $medicalHistoryCollection = $this->medicalHistoryFactory->create()->getCollection();
                $medicalHistoryCollection->addFieldToFilter('order_number', $incrementId);
                $medicalHistoryCollection->addFieldToFilter('customer_id', $customerId);
                $medicalHistoryCollection->addFieldToFilter('govt_id', $govtPhotoId);

                //echo "<pre>"; print_r($medicalHistoryCollection->getData()); exit;

                // Save data
                if(!empty($medicalHistoryCollection->getData()))
                {
                    // Creating Medical History New Model Obj
                    $medicalHistoryModel = $this->medicalHistoryFactory->create();

                    foreach ($medicalHistoryCollection->getData() as $medicalHistory) {
                        $govtId = $medicalHistory['id'];

                        if(isset($govtId) && $govtId != null)
                        {
                            $medicalHistoryModel->load($govtId);
                        }
                        $medicalHistoryModel->setData('govt_id', $newGovtPhotoId)
                            ->save();
                    }
                }

                // ================== Update Medcial History End =============
            }

            $this->_messageManager->addSuccess('You have uploaded photos successfully.');

            // code for sending data to virtual hub //
            $request['customer_id'] = $customerId;
            $bearerToken = $this->configHelper->getVirtualHubBearerToken();
            //$vhUrl = "https://stage.zeelify.md/api/v1/ssmdupdatemedicalinfo";
            if ($bearerToken['success'] == True) {
                $token = $bearerToken['token'];
                $vhUrl = $this->configHelper->getUpdatePhoto();
                $headers = ["Content-Type" => "application/json", "Authorization" => 'Bearer ' . $token];
                $this->curl->setHeaders($headers);
                $this->curl->post($vhUrl, json_encode($request));
                $response = $this->curl->getBody();
                            
                $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/medical_photo_admin_edit_response.log');
                $logger = new \Zend\Log\Logger();
                $logger->addWriter($writer);
                $logger->info("======START==============");
                $logger->info($request);
                $logger->info($response);
                $logger->info("===========END=========");
                //$logger->info($vhUrl);
        
            }

            $resultRedirect = $this->redirectFactory->create();
            $resultRedirect->setPath('sales/order/view/order_id/'.$orderId);
            return $resultRedirect;

        }catch (\Exception $e) {

            echo $e->getMessage(); exit;
            //$response_array['face_image_success'] = 2;
        }
    }

    // Create directory and return path
    public function createRecursiveDirectories($customerId) {

        $secureDir  =  $this->_dataHelper->getRootDirPath();
        if (!file_exists($secureDir)) {
            mkdir($secureDir, 0777, true);
        }

        $dirPath = "";
        for($i=0; $i < strlen($customerId); $i++) {

            $dirPath = $dirPath.'/'.$customerId[$i];

            if (!file_exists($secureDir.'/'.$customerId[$i])) {
                mkdir($secureDir.'/'.$customerId[$i], 0777, true);
                $secureDir = $secureDir.'/'.$customerId[$i];
            }
            else
            {
                $secureDir = $secureDir.'/'.$customerId[$i];
            }
        }
        return $dirPath;
    }
}

