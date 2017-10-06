<?php
namespace Excelss\Customerproducts\Controller\Adminhtml\Index;

class Post extends \Magento\Customer\Controller\Adminhtml\Index
{
    /**
     * Customer compare grid
     *
     * @return \Magento\Framework\View\Result\Layout
     */
    public function execute()
    {
       // $this->_view->loadLayout();
        $this->initCurrentCustomer();
        $resultLayout = $this->resultLayoutFactory->create();
        //$this->_view->renderLayout();
        return $resultLayout;
    }
}