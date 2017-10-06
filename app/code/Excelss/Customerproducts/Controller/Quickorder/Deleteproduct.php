<?php


namespace Excelss\Customerproducts\Controller\Quickorder;
use Magento\Framework\App\Action\Action;
use Magento\Framework\Controller\ResultFactory;


class Deleteproduct extends Action
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
        $productId = $this->_request->getParam('id');

        $customer_product_Model = $this->_customerdata->getCollection()
            ->addFieldToFilter('product_id', array('eq'=> $productId ))
            ->addFieldToFilter('customer_id', array('eq'=> $customerId ));


        foreach($customer_product_Model as $Model){
            $id = $Model->getEntityId();
            try {
                $Model->delete($id);
                $this->messageManager->addSuccess( __('product was deleted sucessfully.') );
                $this->_redirect('customerproducts/quickorder/manage');
            }
            catch(Exception $e){
                $this->messageManager->addError(
                    __('Product was not Found'));
                $this->_redirect('customerproducts/quickorder/manage');

            }
        }

    }

}
