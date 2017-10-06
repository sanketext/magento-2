<?php

namespace Excelss\Customerproducts\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

   /**
    *  config path
    */
   const XML_PATH_SHOW_PRODUCT_DATA_ONLY = 'customerproducts/general/show_product_data_only';
   
   
    /**
     * @var \Magento\Backend\Model\UrlInterface
     */
    protected $_backendUrl;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    protected $storeManager;

    /**
     * @param \Magento\Framework\App\Helper\Context   $context
     * @param \Magento\Backend\Model\UrlInterface $backendUrl
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Backend\Model\UrlInterface $backendUrl,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->_backendUrl = $backendUrl;
        $this->storeManager = $storeManager;
    }

    /**
     * get products tab Url in admin
     * @return string
     */
    public function getProductsGridUrl()
    {
        return $this->_backendUrl->getUrl('customerproducts/customer/products', ['_current' => true]);
    }
    
    /**
     * 
     */
    public function isEnableShowProductDataOnly()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(self::XML_PATH_SHOW_PRODUCT_DATA_ONLY, $storeScope);
    }
}
