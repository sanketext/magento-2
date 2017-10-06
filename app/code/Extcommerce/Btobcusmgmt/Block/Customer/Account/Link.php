<?php

namespace Extcommerce\Btobcusmgmt\Block\Customer\Account;

class Link extends \Magento\Framework\View\Element\Html\Link\Current {
    protected $customerSession;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\DefaultPathInterface $defaultPath,
        \Magento\Customer\Model\Session $customerSession,
        array $data = []
    ){
        $this->customerSession = $customerSession;
        parent::__construct($context, $defaultPath, $data);
    }

    protected function _toHtml() {
        if ($this->customerSession->getCustomer()->getParentCustomerId()== null) {
            return parent::_toHtml();
        }
        return '';
    }

//    private function getAttributesHtml()
//    {
//        $attributesHtml = '';
//        $attributes = $this->getAttributes();
//        if ($attributes) {
//            foreach ($attributes as $attribute => $value) {
//                if($value != 'My Orderlist' )
//                {
//                    $attributesHtml .= ' ' . $attribute . '="' . $this->escapeHtml($value) . '"';
//
//                }
//
//            }
//        }
//
//        return $attributesHtml;
//    }
}