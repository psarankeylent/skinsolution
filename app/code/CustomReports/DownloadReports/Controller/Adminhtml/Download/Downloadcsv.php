<?php

declare(strict_types=1);

namespace CustomReports\DownloadReports\Controller\Adminhtml\Download;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponentInterface;
use Magento\Framework\Api\FilterBuilder;

class Downloadcsv extends \Magento\Backend\App\Action
{
    protected $fileFactory;
    protected $request;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->fileFactory = $fileFactory;
        $this->request = $request;
        parent::__construct($context);
    }
    public function execute()
    {
        // Authentication and validation logic here
        // Ensure the user has the rights to download files
        $csvFile = $this->request->getParam('file');
        if(isset($csvFile) && $csvFile != "")
        {
            $fileName = $csvFile; // The file you want to download

            $filePath = BP . '/var/exportcsv/' . $fileName;
            if (!file_exists($filePath)) {
                $this->messageManager->addError(__('The file does not exist.'));
                return $this->_redirect('downloadreports/index/index');
            }
            try {
                $this->fileFactory->create(
                    $fileName,
                    [
                        'type'  => 'filename',
                        'value' => 'exportcsv/' . $fileName,
                        'rm'    => false, // can set to false if you want to preserve the file
                    ],
                    \Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR,
                    'application/octet-stream',
                    ''
                );
            } catch (\Exception $e) {
                $this->messageManager->addError(__('There was an error while downloading the file: %1', $e->getMessage()));
                return $this->_redirect('downloadreports/index/index');
            }
            //exit;
        }
        else{
            $this->messageManager->addError(__('File Request is not proper.'));
            return $this->_redirect('downloadreports/index/index');
        }        
    }
}

