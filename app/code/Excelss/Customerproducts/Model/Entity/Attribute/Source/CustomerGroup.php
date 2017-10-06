<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Excelss\Customerproducts\Model\Entity\Attribute\Source;

use Magento\Eav\Model\ResourceModel\Entity\Attribute\OptionFactory;
use Magento\Framework\DB\Ddl\Table;

class CustomerGroup extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /**
     * @var GroupManagementInterface
     */
    protected $_customerGroup;
    
    protected $_groupManagement;

    /**
        * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory $attrOptionCollectionFactory
        * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\OptionFactory $attrOptionFactory
        * @param \Magento\Customer\Model\ResourceModel\Group\Collection $customerGroup
        * @param array $data
    */
    
    public function __construct(
        \Magento\Customer\Model\ResourceModel\Group\Collection $customerGroup,        
        array $data = []
    ) {
        $this->_customerGroup = $customerGroup;
    }

    /**
     * Get all options
     *
     * @return array
     */
    public function getAllOptions()
    {
        $customerGroups = $this->_customerGroup->toOptionArray();
        $customerGroups[0]['value']='999';
        return $customerGroups;
    }
    
}
