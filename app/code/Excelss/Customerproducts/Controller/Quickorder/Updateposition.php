<?php

namespace Excelss\Customerproducts\Controller\Quickorder;
use Magento\Framework\App\Action\Action;

class Updateposition extends Action
{
    protected $_request;
    protected $_customerSession;
    protected $_customerdata;
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Customer\Model\Session $customerSession,
        \Excelss\Customerproducts\Model\CustomerProducts $customerdata,
        array $data = []
    ) {
        $this->_request = $request;
        $this->_customerSession = $customerSession;
        $this->_customerdata = $customerdata;
        parent::__construct($context);
    }

    public function execute()
    {
        $customerId = $this->_customerSession->getCustomer()->getId();
        $position = $this->_request->getPost('position');

        $productIds = array_keys($position);
        $customer_product_Model = $this->_customerdata->getCollection()
            ->addFieldToFilter('product_id', array('in'=> $productIds ))
            ->addFieldToFilter('customer_id', array('eq'=> $customerId ));

        foreach($customer_product_Model as $Model){
            $Model->setProductPosition($position[$Model->getProductId()]);
            $Model->save();
        }
    }

}
