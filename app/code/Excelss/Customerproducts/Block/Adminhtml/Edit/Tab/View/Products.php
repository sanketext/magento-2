<?php

namespace Excelss\Customerproducts\Block\Adminhtml\Edit\Tab\View;

use Magento\Customer\Controller\RegistryConstants;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;

/**
 * Adminhtml customer recent orders grid block
 */
class Products extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry|null
     */
    protected $_status;
    protected $_coreRegistry = null;
    protected $_visibility;
    /**
     * @var \Magento\Sales\Model\Resource\Order\Grid\CollectionFactory
     */
    protected $_collectionFactory;
    protected $_productFactory;
    protected $_websiteFactory;
    protected $productCollectionFactory;
    /**
     * Constructor
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Sales\Model\Resource\Order\Grid\CollectionFactory $collectionFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Framework\Registry $coreRegistry,
        \Psr\Log\LoggerInterface $logger,
        ProductFactory $productFactory,
        ProductCollectionFactory $productCollectionFactory,
        \Magento\Store\Model\WebsiteFactory $websiteFactory,
        \Magento\Catalog\Model\Product\Visibility $visibility,
        \Magento\Catalog\Model\Product\Attribute\Source\Status $status,
        array $data = []
    ) {

        $this->_logger = $logger;
        $this->_coreRegistry = $coreRegistry;
        $this->_visibility = $visibility;
        $this->_status = $status;
        $this->_websiteFactory = $websiteFactory;
        $this->_productFactory   = $productFactory;
        $this->productCollectionFactory = $productCollectionFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * Initialize the orders grid.
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('comparedproduct_view_compared_grid');
        $this->setDefaultSort('created_at', 'desc');
        $this->setSortable(true);
        $this->setUseAjax(true);
        $this->setPagerVisibility(true);
        $this->setFilterVisibility(true);
    }
    /**
     * {@inheritdoc}
     */
    /*protected function _preparePage()
    {
        $this->getCollection()->setPageSize(5)->setCurPage(1);
    }*/

    /**
     * {@inheritdoc}
     */
    protected function _prepareCollection()
    {
        $collection = $this->productCollectionFactory->create();
        $collection->addAttributeToSelect('*');
        $customerId = (int)$this->getRequest()->getParam('id');
        $joinConditions = 'u.product_id = e.entity_id AND u.customer_id = '.$customerId;

        $collection->getSelect()->join(
            ['u' => $collection->getTable('excelss_customer_products')],
            $joinConditions,
            []
        );

        //echo $collection->getSelect(); exit;
        //echo '<pre>'; print_r($collection->getData()); exit;
        $this->setCollection($collection);
        return parent::_prepareCollection();


    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareColumns()
    {

        $this->addColumn(
            'entity_id',
            [
                'header' => __('ID'),
                'type' => 'number',
                'index' => 'entity_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
                'width' => '30px',
            ]
        );
        $this->addColumn(
            'names',
            [
                'header' => __('Name'),
                'index' => 'name',
                'class' => 'xxx',
                'width' => '60px',
            ]
        );
        $this->addColumn(
            'sku',
            [
                'header' => __('Sku'),
                'index' => 'sku',
                'class' => 'xxx',
                'width' => '30px',
            ]
        );
        $this->addColumn(
            'price',
            [
                'header' => __('Price'),
                'type' => 'currency',
                'index' => 'price',
                'width' => '20px',
            ]
        );
        $this->addColumn(
            'sku',
            [
                'header' => __('SKU'),
                'index' => 'sku',
                'header_css_class' => 'col-sku',
                'column_css_class' => 'col-sku'
            ]
        );

        $this->addColumn(
            'visibility',
            [
                'header' => __('Visibility'),
                'index' => 'visibility',
                'type' => 'options',
                'options' => $this->_visibility->getOptionArray(),
                'header_css_class' => 'col-visibility',
                'column_css_class' => 'col-visibility'
            ]
        );

        /*if (!$this->_storeManager->isSingleStoreMode()) {
            $this->addColumn(
                'websites',
                [
                    'header' => __('Websites'),
                    'sortable' => false,
                    'index' => 'websites',
                    'type' => 'options',
                    'options' => $this->_websiteFactory->create()->getCollection()->toOptionHash(),
                    'header_css_class' => 'col-websites',
                    'column_css_class' => 'col-websites',
                    'filter_condition_callback' => array($this, '_filterWebsiteConditionCallback')
                ]
            );
        }*/

        $this->addColumn(
            'status',
            [
                'header' => __('Status'),
                'index' => 'status',
                'type' => 'options',
                'options' => $this->_status->getOptionArray(),
                'header_css_class' => 'col-status',
                'column_css_class' => 'col-status',
                'frame_callback' => array(
                    $this->getLayout()->createBlock('Excelss\Customerproducts\Block\Adminhtml\Grid\Column\Statuses'),
                    'decorateStatus'
                ),
            ]
        );

        /*$this->addColumn(
            'position',
            [
                'header' => __('Position'),
                'name' => 'position',
                'type' => 'number',
                'validate_class' => 'validate-number',
                'index' => 'position',
                'editable' => true,
                'edit_only' => false,
                'sortable' => false,
                'filter' => false,
                'header_css_class' => 'col-position',
                'column_css_class' => 'col-position'
            ]
        );*/


        return parent::_prepareColumns();
    }



    /**
     * Get headers visibility
     *
     * @return bool
     *
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getHeadersVisibility()
    {
        return $this->getCollection()->getSize() >= 0;
    }
    /**
     * {@inheritdoc}
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('catalog/product/edit', ['id' => $row->getEntityId()]);
    }
}