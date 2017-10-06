<?php
/**
 *
 * @category    Excelss
 * @package     Excelss_Multipleaddtocart
 * @copyright   © 2017 Excelss. All rights reserved.
 * See COPYING.txt for license details.
 *
 */
namespace Excelss\Customerproducts\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Checkout\Model\Cart as CustomerCart;
/**
 * Product list page view
 */
class Add extends \Magento\Framework\App\Action\Action
{
    protected $_pageFactory;

	protected $request;
	
	protected $cart;
	
	protected $messageManager;
	
	protected $resultPageFactory;
	
	protected $_product;
    /**
     * Constructor
     * 
     * @param \Magento\Framework\App\Action\Context  $context
     * @param \Magento\Framework\View\Result\PageFactory $pageFactory
     */
    public function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Framework\View\Result\PageFactory $pageFactory,
		\Magento\Framework\App\Request\Http $request,
		CustomerCart $cart,
		\Magento\Framework\Message\ManagerInterface $messageManager, 
    	\Magento\Framework\View\Result\PageFactory $resultPageFactory,
		\Magento\Catalog\Model\Product $product
	)
	{
		$this->_product = $product;
    	$this->messageManager = $messageManager;
		$this->cart = $cart;
		$this->request = $request;
		$this->_pageFactory = $pageFactory;
		return parent::__construct($context);
	}

    /**
     * Execute view action
     * 
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
	{
		$products = $this->request->getParam("qty");
		$cntr = 0;
		// $quote=$this->cart->create();
		foreach($products as $id => $qty) 
		{
			$product=$this->_product->load($id);
			
			$p['qty'] = $qty; 
				if($qty) {
					try {
						$params = array(
								'product' => $id,
								'qty' => (int)$qty
							);
							$request = new \Magento\Framework\DataObject($params);
						
						$this->cart->addProduct($id,(int)$qty); 
						//$quote->save();
						$cntr+=$qty;
					} catch(\Exception $e) {
						$this->messageManager->addError($e->getMessage());
					}
				}
		}
		$this->cart->save();

		$resultRedirect = $this->resultRedirectFactory->create(); 
		
		if($cntr) {
			$this->messageManager->addSuccess(__($cntr." product(s) added to cart"));
		}
		$resultRedirect->setUrl($this->_redirect->getRefererUrl());
    	return $resultRedirect;
		
	}

}
