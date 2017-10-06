<?php



namespace Excelss\Customerproducts\Controller\Quickorder;
use Magento\Framework\App\Action\Action;
use Magento\Framework\Controller\ResultFactory;


class Addproduct extends Action
{
    protected $_request;
    protected $_customerSession;
    protected $_customerdatafactory;
    protected $_customerdata;
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Customer\Model\Session $customerSession,
        \Excelss\Customerproducts\Model\Customerproducts $customerdata,
        \Excelss\Customerproducts\Model\CustomerproductsFactory $customerdatafactory,
        array $data = []
    ) {
        $this->_request = $request;
        $this->_customerSession = $customerSession;
        $this->_customerdatafactory = $customerdatafactory;
        $this->_customerdata = $customerdata;
        parent::__construct($context);
    }

    public function execute()
    {
        $customerId = $this->_customerSession->getCustomer()->getId();
        $productId = $this->_request->getParam('productid');

        $customer_product_check = $this->_customerdata->getCollection()
            ->addFieldToFilter('product_id', array('eq'=> $productId ))
            ->addFieldToFilter('customer_id', array('eq'=> $customerId ));

        $hasProduct = count($customer_product_check);
        if($hasProduct){

            $this->messageManager->addError(
                __('Product was already in the list'));
            $this->_redirect('customerproducts/quickorder/index/');
        }

       else {
           $customer_product_Model = $this->_customerdatafactory->create();
           $customer_product_Model->setProductId($productId);
           $customer_product_Model->setCustomerId($customerId);
           try {
               $customer_product_Model->save();
               $this->messageManager->addSuccess(__('product was saved sucessfully.'));
               $this->_redirect('customerproducts/quickorder/index/');
           } catch (Exception $e) {
               $this->messageManager->addError(
                   __('Product was not Found'));
               $this->_redirect('customerproducts/quickorder/index/');
           }

       }
    }

}
