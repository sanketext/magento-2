<?php

/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Excelss\Customerproducts\Block;

use Magento\Catalog\Block\Product\ListProduct;
use Magento\Catalog\Model\Layer\Resolver as LayerResolver;
use Magento\CatalogSearch\Helper\Data;
use Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Search\Model\QueryFactory;


/**
 * Product search result block
 */
class Result extends \Magento\CatalogSearch\Block\Result
{
    public function _getProductCollection()
    {
//        if (null === $this->productCollection) {
            $title = $this->catalogSearchData->getEscapedQueryText();
            echo $title;
            exit;
            $this->productCollection = $this->getListBlock()->getLoadedProductCollection();
            $this->productCollection->addAttributeToFilter('name', array(
                array('like' => '% '.$title.' %'), //spaces on each side
                array('like' => '% '.$title), //space before and ends with $needle
                array('like' => $title.' %') // starts with needle and space after
            ));

//        }

        return $this->productCollection;
    }

}
