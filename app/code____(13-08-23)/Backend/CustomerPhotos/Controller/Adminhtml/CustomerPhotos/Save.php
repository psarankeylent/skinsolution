<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Backend\CustomerPhotos\Controller\Adminhtml\CustomerPhotos;

use Magento\Framework\Exception\LocalizedException;

class Save extends \Magento\Backend\App\Action
{

    protected $dataPersistor;
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

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
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
    ) {
        $this->dataPersistor = $dataPersistor;
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
        parent::__construct($context);
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $post = $this->getRequest()->getPostValue();

        $photoFiles      = $this->getRequest()->getFiles('photo');
       // echo "<pre>"; print_r($post); exit;
        if( $photoFiles['name'] != "" && $photoFiles['size'] > 0)
        {
            // Directories Dynamically created.
            $dirPath = $this->createRecursiveDirectories($post['customer_id']);

            $isPhoto = $this->savePhoto($post, $photoFiles, $dirPath);
            if($isPhoto)
            {
                $this->_messageManager->addSuccessMessage('You saved photo successfully.');
                return $resultRedirect->setPath('*/*/');
            }
        }
        $this->_messageManager->addErrorMessage('Something went wrong, please try after sometime.');
        return $resultRedirect->setPath('*/*/');
    }

    // Upload Photo Function
    public function savePhoto($post, $face_files, $dirPath) {

        $fileName_facePhoto = ($face_files && array_key_exists('name', $face_files)) ? $face_files['name'] : null;
        $fileName_facePhoto = str_replace(" ", "_", strtolower($fileName_facePhoto));

        $result = false;
        if ($face_files && $fileName_facePhoto) {

            /** @var $uploader \Magento\MediaStorage\Model\File\UploaderFactory */
            $uploader = $this->_fileUploaderFactory->create(['fileId' => 'photo']);

            // set allowed file extensions
            $uploader->setAllowedExtensions(['jpg','jpeg','png','heic']);

            // Allow folder creation if not exists
            $uploader->setAllowCreateFolders(true);

            // Rename file if already exists it set true(like filename.jpg, filename_1.jpg etc), and set false it overrides into dir
            $uploader->setAllowRenameFiles(false);

            $fileName_facePhoto = preg_replace("/[^A-Za-z0-9-.]/", '_', $fileName_facePhoto);
            $newStr = explode('.', $fileName_facePhoto);
            $face_fileExt = end($newStr);

            $imagePath = $dirPath.'/'.$post['customer_id'].'_'.$post['photo_type'].'_'.time().'.'.$face_fileExt;
            $dirFullPath = $this->_dataHelper->getRootDirPath().$imagePath;
            $photoPath = $this->_dataHelper->getRootPath().$imagePath;

            // =================== Image Resize before save to directory and db ==============================


            $uploadedFile = $face_files['tmp_name'];
            $resizedImgURL = $this->_dataHelper->imageResizeBeforeSaveImage($dirFullPath, $uploadedFile);

            // =================== Image Resize before save to directory and db ==============================


            // Old record needs to do be set status zero.
            $photoCollection = $this->_customerPhotosFactory->create()->getCollection();
            $photoCollection->addFieldToFilter('customer_id', $post['customer_id']);
            $photoCollection->addFieldToFilter('photo_type', $post['photo_type']);
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
            $facesavephotoModelNewObj->setCustomerId($post['customer_id'])
                ->setPhotoType($post['photo_type'])
                ->setPath($imagePath)
                ->setSourceSystem($face_files['tmp_name'])
                ->setStatus(1)
                //->setIncrementId($incrementId)
                ->setCreatedAt(date('Y-m-d h:i:s'))
                ->save();

            $lastInsertedId = $facesavephotoModelNewObj->getId();
            if($lastInsertedId)
            {
                $result = true;
            }
        }
        return $result;
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

