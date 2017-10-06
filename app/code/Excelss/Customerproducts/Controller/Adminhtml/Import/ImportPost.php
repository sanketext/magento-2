<?php

namespace Excelss\Customerproducts\Controller\Adminhtml\Import;

use Magento\Framework\Controller\ResultFactory;

/**
 * Class ImportPost
 *
 * @package Excelss\Customerproducts\Controller\Adminhtml\Import
 */
class ImportPost extends \Magento\Backend\App\Action
{
    /**
     * ACL resource
     */
    const ADMIN_RESOURCE = 'Excelss_Customerproducts::customerproducts';

    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $fileFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory
    ) {
        $this->fileFactory = $fileFactory;
        parent::__construct($context);
    }

    /**
     * import action from import/export csv
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        if ($this->getRequest()->isPost() && !empty($_FILES['import_csv_file']['tmp_name'])) {
            try {
                /** @var $importHandler \Excelss\Customerproducts\Model\Products\CsvImportHandler */
                $importHandler = $this->_objectManager->create('Excelss\Customerproducts\Model\Products\CsvImportHandler');
                $importHandler->importFromCsvFile($this->getRequest()->getFiles('import_csv_file'));

                $this->messageManager->addSuccess(__('The data has been imported.'));
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addError(__('Invalid file upload attempt'));
            }
        } else {
            $this->messageManager->addError(__('Invalid file upload attempt'));
        }
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($this->_redirect->getRedirectUrl());
        return $resultRedirect;
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(self::ADMIN_RESOURCE);
    }
}
