<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Excelss\Customerproducts\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory as CustomerCollectionFactory;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\DB\Helper as DbHelper;
use Magento\Framework\UrlInterface;
use Magento\Framework\Stdlib\ArrayManager;

/**
 * Data provider for categories field of product page
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class AllowedCustomers extends \Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier
{

    /**
     *
     * @var type 
     */
    protected $customerCollectionFactory;
    /**
     * @var ArrayManager
     */
    protected $arrayManager;


    /**
     * @param LocatorInterface $locator
     * @param CustomerCollectionFactory $customerCollectionFactory
     * @param DbHelper $dbHelper
     * @param UrlInterface $urlBuilder
     * @param ArrayManager $arrayManager
     */
    public function __construct(
        LocatorInterface $locator,
        CustomerCollectionFactory $customerCollectionFactory,
        DbHelper $dbHelper,
        ArrayManager $arrayManager
    ) {
        
        $this->locator = $locator;
        $this->customerCollectionFactory = $customerCollectionFactory;
        $this->dbHelper = $dbHelper;
        $this->arrayManager = $arrayManager;
    }

    

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        $meta = $this->customizeAllowcustomerField($meta);
        return $meta;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        return $data;
    }


    /**
     * Customize Categories field
     *
     * @param array $meta
     * @return array
     */
    protected function customizeAllowcustomerField(array $meta)
    {
        $fieldCode = 'allowed_customers';
        $elementPath = $this->arrayManager->findPath($fieldCode, $meta, null, 'children');
        $containerPath = $this->arrayManager->findPath(static::CONTAINER_PREFIX . $fieldCode, $meta, null, 'children');
        
        if (!$elementPath) {
            return $meta;
        }

        $meta = $this->arrayManager->merge(
            $containerPath,
            $meta,
            [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'label' => __('Allowed Customers'),
                            'dataScope' => '',
                            'breakLine' => false,
                            'formElement' => 'container',
                            'componentType' => 'container',
                            'component' => 'Magento_Ui/js/form/components/group',
                            'scopeLabel' => __('[GLOBAL]'),
                        ],
                    ],
                ],
                'children' => [
                    $fieldCode => [
                        'arguments' => [
                            'data' => [
                                'config' => [
                                    'formElement' => 'select',
                                    'componentType' => 'field',
                                    'component' => 'Magento_Ui/js/form/element/ui-select',
                                    'filterOptions' => true,
                                    'chipsEnabled' => true,
                                    'disableLabel' => true,
                                    'levelsVisibility' => '1',
                                    'elementTmpl' => 'ui/grid/filters/elements/ui-select',
                                    'options' => $this->getCustomerTree(),
                                    'listens' => [
                                        'newOption' => 'toggleOptionSelected'
                                    ],
                                    'config' => [
                                        'dataScope' => $fieldCode,
                                        'sortOrder' => 10,
                                    ],
                                ],
                            ],
                        ],
                    ],
                   
                ]
            ]
        );

        return $meta;
    }

    /**
     * Retrieve customers
     *
     * @param string|null $filter
     * @return array
     */
    protected function getCustomerTree($filter = null)
    {
        $storeId = $this->locator->getStore()->getId();
        /* @var $matchingNamesCollection \Magento\Customer\Model\ResourceModel\Customer\Collection */
        $matchingNamesCollection = $this->customerCollectionFactory->create();
        
        $shownCustomerIds = [];

        /** @var \Magento\Customer\Model\Customer $category */
        foreach ($matchingNamesCollection as $customer) {
            $shownCustomerIds[]=['value'=>$customer->getData('entity_id'),
                                'label'=>$customer->getData('firstname').' '.$customer->getData('lastname')];
        }
        
        return $shownCustomerIds;
    }
}
