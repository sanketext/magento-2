<?php

namespace Excelss\Customerproducts\Model;

use Magento\Cron\Exception;
use Magento\Framework\Model\AbstractModel;

/**
 * CustomerProducts Model
 *
 */
class CustomerProducts extends AbstractModel
{
    /**
     * @var \Magento\Framework\Stdlib\DateTime
     */
    protected $_dateTime;

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Excelss\Customerproducts\Model\ResourceModel\CustomerProducts::class);
    }

}
