<?php

namespace Excelss\Customerproducts\Ui\DataProvider\Product\Form;

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Magento\Ui\DataProvider\Modifier\PoolInterface;

/**
 * DataProvider for product edit form
 */
class ProductDataProvider extends \Magento\Catalog\Ui\DataProvider\Product\Form\ProductDataProvider
{
    
    /**
     * @var PoolInterface
     */
    private $pool;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param PoolInterface $pool
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        PoolInterface $pool,
        array $meta = [],
        array $data = []
    ) {
        
        parent::__construct($name, $primaryFieldName, $requestFieldName, $collectionFactory, $pool, $meta, $data);
        $this->collection = $collectionFactory->create();
        $this->pool = $pool;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        $_productCollection = $this->collection->getData();
        foreach($_productCollection as $data){
            $_entityId = $data['entity_id'];
        }
        
        /** @var ModifierInterface $modifier */
        foreach ($this->pool->getModifiersInstances() as $modifier) {
            $this->data = $modifier->modifyData($this->data);
            if(isset($this->data[$_entityId]['product']['allowed_customergroup']) && $this->data[$_entityId]['product']['allowed_customergroup'] != '')
            {
                $_allowedCustomersGroup = $this->data[$_entityId]['product']['allowed_customergroup'];
                $_allowedCustomersGroup1 = explode(",", $_allowedCustomersGroup);
            }
            if(isset($this->data[$_entityId]['product']['allowed_customers']) && $this->data[$_entityId]['product']['allowed_customers'] != '')
            {
                $_allowedCustomers = $this->data[$_entityId]['product']['allowed_customers'];
                $_allowedCustomers1 = explode(",", $_allowedCustomers);
            }
        }
        if(!empty($_allowedCustomers1)){
        $this->data[$_entityId]['product']['allowed_customers'] = $_allowedCustomers1;
        }
        if(!empty($_allowedCustomersGroup1)){
        $this->data[$_entityId]['product']['allowed_customergroup'] = $_allowedCustomersGroup1;
        }

        return $this->data;
    }
    
    
}
