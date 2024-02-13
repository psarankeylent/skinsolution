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

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Ssmd\CustomerPhotos\Model\CustomerPhotosFactory $customerPhotosFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor,
        \Ssmd\CustomerPhotos\Helper\Data $dataHelper,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Ssmd\MedicalHistory\Model\CustomerMedicalHistoryFactory $medicalHistoryFactory
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
    }

    public function execute()
    {
        //echo "called admin"; exit;
        $this->resultPage = $this->resultPageFactory->create();

        try{

            // Customer ID

            $response_array = array();
            $customerId = $this->getRequest()->getParam('customer_id');
            $photoType = $this->getRequest()->getParam('photo_type');
            $incrementId = $this->getRequest()->getParam('increment_id');

            // Directories Dynamically created.
            $dirPath = $this->createRecursiveDirectories($customerId, $photoType);
            
            if($this->getRequest()->getParam('photo_id') != "")
            {
                $photoId = $this->getRequest()->getParam('photo_id');
            }
            else
            {
                $photoId = null;
            }

//===================================================== FACE PHOTO UPLOAD CODE ========================================

            if(isset($photoType) && $photoType == 'full_face')
            {

                $face_files = $this->getRequest()->getFiles('face_photo');
                $photoType = $photoType;


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

                    $imagePath = $dirPath.'/'.$customerId.'_'.$photoType.'_'.time().'.'.$face_fileExt;
                    $dirFullPath = $this->_dataHelper->getRootDirPath().$imagePath;
                    $photoPath = $this->_dataHelper->getRootPath().$imagePath;
                    
                    // =================== Image Resize before save to directory and db ==============================

                    
                    $uploadedFile = $face_files['tmp_name'];
                    $resizedImgURL = $this->_dataHelper->imageResizeBeforeSaveImage($dirFullPath, $uploadedFile);
                   
                    // =================== Image Resize before save to directory and db ==============================


                    // Old record needs to do be set status zero.
                    $photoCollection = $this->_customerPhotosFactory->create()->getCollection();
                    $photoCollection->addFieldToFilter('customer_id', $customerId);
                    $photoCollection->addFieldToFilter('photo_type', $photoType);
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
                        ->setPhotoType($photoType)
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
                    $medicalHistoryCollection->addFieldToFilter('full_face', $photoId);

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
                else
                    $response_array['face_image_path'] = "";
                }                {


                $response_array['face_image_path'] = '';
                try{

                    if($photoPath != "" || $photoPath != null)
                    {
                        $response_array['face_image_path'] = '<a data-fancybox="gallery" href="'.$photoPath.'"><img src="'.$photoPath.'" style="max-height: 185px"></a>';
                        $response_array['face_image_success'] = 1;
                    }
                    else
                    {
                        $response_array['face_image_path'] = '';
                        $response_array['face_image_success'] = 2;
                    }

                    echo json_encode($response_array);
                    exit;


                }catch (\Exception $e) {

                    // echo $e->getMessage(); exit;
                    $response_array['face_image_success'] = 2;
                }
            }



//====================================================================== Govt PHOTOID UPLOAD CODE ==============================================================================================

            if(isset($photoType) && $photoType == 'govt_id')
            {

                $files = $this->getRequest()->getFiles('govt_photo');
                $photoType = $photoType;


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
                   
                    $imagePath = $dirPath.'/'.$customerId.'_'.$photoType.'_'.time().'.'.$fileExt;
                    $dirFullPath = $this->_dataHelper->getRootDirPath().$imagePath;
                    $photoPath = $this->_dataHelper->getRootPath().$imagePath;

                    
                    // =================== Image Resize before save to directory and db ==============================

                    $uploadedFile = $files['tmp_name'];
                    $resizedImgURL = $this->_dataHelper->imageResizeBeforeSaveImage($dirFullPath, $uploadedFile);
                    
                    // =================== Image Resize before save to directory and db ==============================


                    // Old record needs to do be set status zero.
                    $photoCollection = $this->_customerPhotosFactory->create()->getCollection();
                    $photoCollection->addFieldToFilter('customer_id', $customerId);
                    $photoCollection->addFieldToFilter('photo_type', $photoType);
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
                                    ->setPhotoType($photoType)
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
                    $medicalHistoryCollection->addFieldToFilter('govt_id', $photoId);

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
                else
                {
                    $response_array['photoid_image_path'] = "";
                }

                $response_array['photoid_image_path'] = '';

                try{

                    if($photoPath != "" || $photoPath != null)
                    {
                        $response_array['photoid_image_path'] = '<a data-fancybox="gallery" href="'.$photoPath.'"><img src="'.$photoPath.'" style="max-height: 185px"></a>';
                        $response_array['success'] = 1;


                    }
                    else
                    {
                        $response_array['photoid_image_path'] = '';
                        $response_array['success'] = 2;
                    }

                    echo json_encode($response_array);
                    exit;


                }catch (\Exception $e) {

                     echo $e->getMessage(); exit;
                    $response_array['errorMessage'] = $e->getMessage();
                    $response_array['success'] = 2;
                }
            }

        } catch (\Exception $e) {
            //echo $e->getMessage(); exit;
            $response_array['error'] = 1;
            $response_array['errorMessage'] = $e->getMessage();

            echo json_encode($response_array);
            exit;

            $this->_messageManager->addError('Only jpg, jpeg, png and heic file type is allowed.');
        }

        return $this->resultPage;
    }

    // Create directory and return path
    public function createRecursiveDirectories($customerId, $photoType) {

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

