<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 8/4/2017
 * Time: 5:07 PM
 */


namespace Excelss\Customerproducts\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\View\Result\PageFactory;

class Index extends Action
{

    public function execute()
    {
        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }

}
