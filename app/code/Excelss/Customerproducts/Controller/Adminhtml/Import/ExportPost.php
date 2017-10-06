<?php

namespace Excelss\Customerproducts\Controller\Adminhtml\Import;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\Filesystem\DirectoryList;

class ExportPost extends \Magento\TaxImportExport\Controller\Adminhtml\Rate
{
    /**
     * ACL resource
     */
    const ADMIN_RESOURCE = 'Excelss_Customerproducts::customerproducts';

    /**
     * Export action from import/export
     *
     * @return ResponseInterface
     */
    public function execute()
    {
        /** start csv content and set template */
        $headers = new \Magento\Framework\DataObject(
            [
                'customer_id' => __('Customer ID'),
                'product_id' => __('Product ID'),
                'product_position' => __('Position')
            ]
        );
        $template = '"{{customer_id}}","{{product_id}}","{{product_position}}"';
        $content = $headers->toString($template);
        $content .= "\n";

        $collection = $this->_objectManager->create('Excelss\Customerproducts\Model\CustomerProducts')->getCollection();

        while ($zipcodedata = $collection->fetchItem()) {

            $content .= $zipcodedata->toString($template) . "\n";
        }
        return $this->fileFactory->create('customer_products.csv', $content, DirectoryList::VAR_DIR);
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(self::ADMIN_RESOURCE);
    }
}
