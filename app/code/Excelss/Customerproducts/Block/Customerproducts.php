<?php

namespace Excelss\Customerproducts\Block;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;

/**
 * List index block
 */
class Customerproducts extends \Magento\Catalog\Block\Product\AbstractProduct implements
    \Magento\Framework\DataObject\IdentityInterface{


    const XML_PATH_MULTIPLEADDTOCART = 'customerproducts/general/';


    /**
     * Default value for products count that will be shown
     */
    const DEFAULT_PRODUCTS_COUNT = 5;

    protected $_request;

    /**
     * Products count
     *
     * @var int
     */
    protected $_productsCount;

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

    /**
     * Catalog product visibility
     *
     * @var \Magento\Catalog\Model\Product\Visibility
     */
    protected $_catalogProductVisibility;

    protected $category;

    protected $helper;

    protected $scopeConfig;

    protected $_customerSession;
    /**
     * Product collection factory
     *
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $_productCollectionFactory;

    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Excelss\Customerproducts\Model\CustomerProducts $customerdata,
        array $data = []
    ) {		$this->scopeConfig = $scopeConfig;
        $this->category = $categoryFactory;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_catalogProductVisibility = $catalogProductVisibility;
        $this->httpContext = $httpContext;
        $this->_request = $request;
        $this->_customerSession = $customerSession;
        $this->customerdata = $customerdata;

        parent::__construct($context, $data);


    }
    /**
     * Preparing global layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        //$this->_addBreadcrumbs();
        $this->pageConfig->getTitle()->set('Quick Order');

        if ($this->getProductCollection()) {
            $pager = $this->getLayout()->createBlock(
                'Magento\Theme\Block\Html\Pager',
                'fme.wholesale.pager'
            )->setAvailableLimit(array('20'=>'20'))->setShowPerPage(true)->setCollection(
                $this->getProductCollection()
            );
            $this->setChild('pager', $pager);

        }

        return parent::_prepareLayout();
    }

    /**
     * Prepare breadcrumbs
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return void
     */
    protected function _addBreadcrumbs()
    {
        if ($this->_scopeConfig->getValue('web/default/show_cms_breadcrumbs', ScopeInterface::SCOPE_STORE)
            && ($breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs'))
        ) {
            $breadcrumbsBlock->addCrumb(
                'home',
                [
                    'label' => __('Home'),
                    'title' => __('Go to Home Page'),
                    'link' => $this->_storeManager->getStore()->getBaseUrl()
                ]
            );
            $breadcrumbsBlock->addCrumb(
                'multiplecart',
                [
                    'label' => __('Quick Order'),
                    'title' => __(sprintf('Quick Order'))
                ]
            );
        }
    }

    /**
     * Retrieve wholesale setting
     * @return string
     */


    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * Get how much products should be displayed at once.
     *
     * @return int
     */
    public function getProductsCount()
    {
        return $pageSize=($this->getRequest()->getParam('limit'))? $this->getRequest()->getParam('limit') : 20;
    }

    public function getProductCollection()
    {

        $page=($this->getRequest()->getParam('p'))? $this->getRequest()->getParam('p') : 1;
        $customerId = $this->_customerSession->getCustomer()->getId();
        if($customerId) {
            $productCollection = $this->_productCollectionFactory->create();
            $productCollection->addAttributeToSelect('*');
            $joinConditions[] = "product.product_id = e.entity_id";
            $joinConditions[] = "product.customer_id= $customerId";
            $joinConditions = implode(
                ' AND ', $joinConditions
            );
            $productCollection->getSelect()->join(
                ['product' => $productCollection->getTable('excelss_customer_products')],
                $joinConditions,
                ['product.product_position']
            )->Order('product.product_position asc');
            $productCollection->setPageSize('20')->setCurPage($page);

            return $productCollection;
        }
    }

    /**
     * Return identifiers for produced content
     *
     * @return array
     */
    public function getIdentities()
    {
        return [\Magento\Catalog\Model\Product::CACHE_TAG];
    }

    public function getConfigValue($field, $storeId = null)
    {
        return $this->_scopeConfig->getValue(
            $field, ScopeInterface::SCOPE_STORE, $storeId
        );
    }

    public function getGeneralConfig($code, $storeId = null)
    {

        return $this->getConfigValue(self::XML_PATH_MULTIPLEADDTOCART . $code, $storeId);
    }

    public function getDeleteUrl($pid)
    {

        return $this->getUrl('customerproducts/quickorder/deleteproduct', array('id' => $pid));
    }

}
