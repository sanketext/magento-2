<?php

namespace Excelss\Customerproducts\Block\Customer\Account;

class Link extends \Magento\Framework\View\Element\Html\Link\Current
{
    /**
     *  config path
     */
    const XML_PATH_SHOW_ORDER_PAGE_ONLY = 'customerproducts/general/customer_order_page';
    protected $customerSession;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\DefaultPathInterface $defaultPath,
        \Magento\Customer\Model\Session $customerSession,
        array $data = []
    )
    {
        $this->customerSession = $customerSession;
        parent::__construct($context, $defaultPath, $data);
    }

    protected function _toHtml()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        if($this->_scopeConfig->getValue(self::XML_PATH_SHOW_ORDER_PAGE_ONLY, $storeScope)){
            return parent::_toHtml();

        }

    }

}