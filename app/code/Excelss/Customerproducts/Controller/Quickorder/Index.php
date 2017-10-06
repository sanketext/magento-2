<?php

namespace Excelss\Customerproducts\Controller\Quickorder;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\View\Result\PageFactory;

class Index extends Action
{
	protected $_customerSession;
	protected $_resultForwardFactory;

	/*public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
        array $data = []
    ) {

        $this->_customerSession = $customerSession;
        $this->_resultForwardFactory = $resultForwardFactory;
        parent::__construct($context, $data);


    }

	public function preDispatch()
    {
    	if(!$this->_customerSession->getCustomer()->isLoggedIn()) {
    		$this->_redirect('customer/account/login');
                return;
    	}
    }*/

    public function execute()
    {
        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }

}
