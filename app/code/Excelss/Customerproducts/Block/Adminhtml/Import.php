<?php
/**
 *
 * @category    ExtCommerce
 * @package     Extcommerce_Checkdelivery
 * @copyright   © 2017 ExtCommerce. All rights reserved.
 * See COPYING.txt for license details.
 *
 */
namespace Excelss\Customerproducts\Block\Adminhtml;

class Import extends \Magento\Backend\Block\Widget
{
    protected $_template = 'customerproducts/import.phtml';

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(\Magento\Backend\Block\Template\Context $context, array $data = [])
    {
        parent::__construct($context, $data);
        $this->setUseContainer(true);
    }

}
