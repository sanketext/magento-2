<?php

namespace Excelss\Customerproducts\Model\ResourceModel\CustomerProducts;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * Initialize resource collection
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('Excelss\Customerproducts\Model\Customerproducts', 'Excelss\Customerproducts\Model\ResourceModel\Customerproducts');
    }
}