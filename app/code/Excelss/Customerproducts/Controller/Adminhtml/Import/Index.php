<?php

namespace Excelss\Customerproducts\Controller\Adminhtml\Import;

/**
 * Class Index
 *
 * @package Excelss\Customerproducts\Controller\Adminhtml\Import
 */
class Index extends \Magento\Backend\App\Action
{
    /**
     * ACL resource
     */
    const ADMIN_RESOURCE = 'Excelss_Customerproducts::customerproducts';

    /**
     * @var PageFactory
     */
    private $_resultPageFactory;

    /**
     * @var LayoutPageFactory
     */
    private $_resultLayoutFactory;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
    ) {
        parent::__construct($context);
        $this->_resultPageFactory = $resultPageFactory;
        $this->_resultLayoutFactory = $resultLayoutFactory;
    }

    /**
     * Index action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /**
         * @var \Magento\Backend\Model\View\Result\Page $resultPage
         */
        $resultPage = $this->_resultPageFactory->create();

        return $resultPage;
    }

    /**
     * @return bool
     */
    // @codingStandardsIgnoreLine
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(self::ADMIN_RESOURCE);
    }
}
