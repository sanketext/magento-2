<?php
namespace Excelss\Customerproducts\Model\ResourceModel\Fulltext;

use Magento\CatalogSearch\Model\Search\RequestGenerator;
use Magento\Framework\DB\Select;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Search\Adapter\Mysql\TemporaryStorage;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Search\Api\SearchInterface;
use Magento\Framework\Search\Response\QueryResponse;
use Magento\Framework\Search\Request\EmptyRequestDataException;
use Magento\Framework\Search\Request\NonExistingRequestNameException;
use Magento\Framework\Api\Search\SearchResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\App\ObjectManager;
use Excelss\Customerproducts\Model\CustomerProducts;
/**
 * Fulltext Collection
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class SearchCollection extends \Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection
{
    /**
     * @var  QueryResponse
     * @deprecated
     */
    protected $queryResponse;

    /**
     * Catalog search data
     *
     * @var \Magento\Search\Model\QueryFactory
     * @deprecated
     */
    protected $queryFactory = null;

    /**
     * @var \Magento\Framework\Search\Request\Builder
     * @deprecated
     */
    private $requestBuilder;

    /**
     * @var \Magento\Search\Model\SearchEngine
     * @deprecated
     */
    private $searchEngine;

    /**
     * @var string
     */
    private $queryText;

    /**
     * @var string|null
     */
    private $order = null;

    /**
     * @var string
     */
    private $searchRequestName;

    /**
     * @var \Magento\Framework\Search\Adapter\Mysql\TemporaryStorageFactory
     */
    private $temporaryStorageFactory;

    /**
     * @var \Magento\Search\Api\SearchInterface
     */
    private $search;

    /**
     * @var \Magento\Search\Api\SearchInterface
     */
    private $searchInterface;

    /**
     * @var \Magento\Framework\Api\Search\SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var \Magento\Framework\Api\Search\SearchResultInterface
     */
    private $searchResult;

    /**
     * @var SearchResultFactory
     */
    private $searchResultFactory;

    /**
     * @var \Magento\Framework\Api\FilterBuilder
     */
    private $filterBuilder;


    /**
     *  config path
     */
    const XML_PATH_HIDE_ALL_PRODUCT = 'customerproducts/general/hide_all_products';

    /**
     * @param \Magento\Framework\Data\Collection\EntityFactory $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Magento\Eav\Model\EntityFactory $eavEntityFactory
     * @param \Magento\Catalog\Model\ResourceModel\Helper $resourceHelper
     * @param \Magento\Framework\Validator\UniversalFactory $universalFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param \Magento\Catalog\Model\Indexer\Product\Flat\State $catalogProductFlatState
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Catalog\Model\Product\OptionFactory $productOptionFactory
     * @param \Magento\Catalog\Model\ResourceModel\Url $catalogUrl
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\Stdlib\DateTime $dateTime
     * @param \Magento\Customer\Api\GroupManagementInterface $groupManagement
     * @param \Magento\Search\Model\QueryFactory $catalogSearchData
     * @param \Magento\Framework\Search\Request\Builder $requestBuilder
     * @param \Magento\Search\Model\SearchEngine $searchEngine
     * @param \Magento\Framework\Search\Adapter\Mysql\TemporaryStorageFactory $temporaryStorageFactory
     * @param \Magento\Framework\DB\Adapter\AdapterInterface $connection
     * @param string $searchRequestName
     * @param SearchResultFactory $searchResultFactory
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactory $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Eav\Model\EntityFactory $eavEntityFactory,
        \Magento\Catalog\Model\ResourceModel\Helper $resourceHelper,
        \Magento\Framework\Validator\UniversalFactory $universalFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Catalog\Model\Indexer\Product\Flat\State $catalogProductFlatState,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Catalog\Model\Product\OptionFactory $productOptionFactory,
        \Magento\Catalog\Model\ResourceModel\Url $catalogUrl,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        \Magento\Customer\Api\GroupManagementInterface $groupManagement,
        \Magento\Search\Model\QueryFactory $catalogSearchData,
        \Magento\Framework\Search\Request\Builder $requestBuilder,
        \Magento\Framework\Api\FilterBuilder $filterBuilder,
        \Magento\Search\Api\SearchInterface $searchInterface,
        \Magento\Framework\Api\Search\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Search\Model\SearchEngine $searchEngine,
        \Magento\Framework\Search\Adapter\Mysql\TemporaryStorageFactory $temporaryStorageFactory,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        $searchRequestName = 'catalog_view_container',
        SearchResultFactory $searchResultFactory = null
    ) {
        $this->queryFactory = $catalogSearchData;
        if ($searchResultFactory === null) {
            $this->searchResultFactory = \Magento\Framework\App\ObjectManager::getInstance()
                ->get('Magento\Framework\Api\Search\SearchResultFactory');
        }
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $eavConfig,
            $resource,
            $eavEntityFactory,
            $resourceHelper,
            $universalFactory,
            $storeManager,
            $moduleManager,
            $catalogProductFlatState,
            $scopeConfig,
            $productOptionFactory,
            $catalogUrl,
            $localeDate,
            $customerSession,
            $dateTime,
            $groupManagement,
            $catalogSearchData,
            $requestBuilder,
            $searchEngine,
            $temporaryStorageFactory,
            $connection,
            $searchRequestName,
            $searchResultFactory
        );
        $this->requestBuilder = $requestBuilder;
        $this->searchEngine = $searchEngine;
        $this->temporaryStorageFactory = $temporaryStorageFactory;
        $this->searchRequestName = $searchRequestName;
        $this->filterBuilder = $filterBuilder;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->searchInterface = $searchInterface;
    }

    /**
     * @deprecated
     * @return \Magento\Search\Api\SearchInterface
     */
    private function getSearch()
    {
        if ($this->search === null) {
            $this->search = ObjectManager::getInstance()->get('\Magento\Search\Api\SearchInterface');
        }
        return $this->search;
    }

    /**
     * @deprecated
     * @return \Magento\Framework\Api\Search\SearchCriteriaBuilder
     */
    private function getSearchCriteriaBuilder()
    {
        if ($this->searchCriteriaBuilder === null) {
            $this->searchCriteriaBuilder = ObjectManager::getInstance()
                ->get('\Magento\Framework\Api\Search\SearchCriteriaBuilder');
        }
        return $this->searchCriteriaBuilder;
    }

    /**
     * @deprecated
     * @return \Magento\Framework\Api\FilterBuilder
     */
    private function getFilterBuilder()
    {
        if ($this->filterBuilder === null) {
            $this->filterBuilder = ObjectManager::getInstance()->get('\Magento\Framework\Api\FilterBuilder');
        }
        return $this->filterBuilder;
    }

    /**
     * @inheritdoc
     */

    public function addSearchFilter($query)
    {
        $this->queryText = trim($this->queryText . ' ' . $query);
        return $this;
    }
    protected function _renderFiltersBefore()
    {
        $this->getSearchCriteriaBuilder();
        $this->getFilterBuilder();
        $this->getSearch();

//    print_r($this->queryText); exit;

        $objectManager = ObjectManager::getInstance();

        $customerSession = $objectManager->create('\Magento\Customer\Model\Session');
        $isLoggedIn = $customerSession->isLoggedIn();
        $cid = $customerSession->getCustomer()->getId();
        $cgroupid = $customerSession->getCustomer()->getGroupId();

        //create your customer product collection and apply your custom field filterBuilder
        $customerProductsCollection = $objectManager->create('Excelss\Customerproducts\Model\CustomerProducts')->getCollection()->load();

        //create your product collection and apply your custom field filterBuilder
        $productCollection = $objectManager->create('Magento\Catalog\Model\ResourceModel\Product\Collection');
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;

        if($isLoggedIn) {

            if($this->_scopeConfig->getValue(self::XML_PATH_HIDE_ALL_PRODUCT, $storeScope) == 1)
            {

                /** Apply filters here */
                $customerProductsCollection->addFieldToSelect('product_id');
                $customerProductsCollection->addFieldToFilter('customer_id', array('eq' => $cid));
                $productIds = $customerProductsCollection->getColumnValues('product_id');

                $productCollection->addAttributeToSelect('*');
                if(!empty($productIds))
                    $productCollection->addAttributeToFilter('entity_id', array('in' => $productIds));
                $attInfo = $objectManager->create('Magento\Eav\Model\ResourceModel\Entity\Attribute');
                $attId = $attInfo->getIdByCode('catalog_product', 'allowed_customergroup');

                $joinConditions = 'cp.entity_id = e.entity_id AND cp.store_id = 0 AND cp.attribute_id = '.$attId;
                $where = "";
                $productCollection->getSelect()->joinLeft(
                    ['cp' => $productCollection->getTable('catalog_product_entity_text')],
                    $joinConditions,
                    []
                )->orWhere("cp.value like '$cgroupid,%' OR cp.value like '%,$cgroupid,%' OR cp.value like '%,$cgroupid' OR cp.value like '%$cgroupid' OR cp.value like '%$cgroupid%' OR cp.value like '$cgroupid%' OR cp.value = 999");

                 $productCollection->addAttributeToFilter('name', array(
                    array('like' => '% '.$this->queryText.' %'), //spaces on each side
                    array('like' => '% '.$this->queryText), //space before and ends with $needle
                    array('like' => $this->queryText.' %') // starts with needle and space after
                ));
                $skus = $productCollection->getColumnValues('sku');
//                $skus = array_unique($skus);

//                echo "<pre>"; print_r($skus);
//                exit;
                $this->filterBuilder->setField('sku');
                $this->filterBuilder->setValue($skus);
                $this->filterBuilder->setConditionType('in');

                $this->searchCriteriaBuilder->addFilter($this->filterBuilder->create());
        }
        } else {
            if($this->_scopeConfig->getValue(self::XML_PATH_HIDE_ALL_PRODUCT, $storeScope) == 1)
            {
                $productCollection->addAttributeToSelect('*');
                $productCollection->addAttributeToFilter(array(
                    array('attribute' => 'allowed_customergroup', 'like' => intval(999).',%'),
                    array('attribute' => 'allowed_customergroup', 'eq' => 999),
                    array('attribute' => 'allowed_customergroup', 'like' => '%,'.intval(999).',%'),
                    array('attribute' => 'allowed_customergroup', 'like' => '%,'.intval(999)),
                    array('attribute' => 'allowed_customergroup', 'like' => '%'.intval(999)),
                    array('attribute' => 'allowed_customergroup', 'like' => '%'.intval(999).'%'),
                    array('attribute' => 'allowed_customergroup', 'like' => intval(999).'%')
                ));
                $productCollection->load();
                $skus = $productCollection->getColumnValues('sku');
                $skus = array_unique($skus);

                $this->filterBuilder->setField('sku');
                $this->filterBuilder->setValue($skus);
                $this->filterBuilder->setConditionType('in');

                $this->searchCriteriaBuilder->addFilter($this->filterBuilder->create());


            }
        }


        if ($this->queryText) {
            $this->filterBuilder->setField('search_term');
            $this->filterBuilder->setValue($this->queryText);
            $this->searchCriteriaBuilder->addFilter($this->filterBuilder->create());
        }

        $priceRangeCalculation = $this->_scopeConfig->getValue(
            \Magento\Catalog\Model\Layer\Filter\Dynamic\AlgorithmFactory::XML_PATH_RANGE_CALCULATION,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if ($priceRangeCalculation) {
            $this->filterBuilder->setField('price_dynamic_algorithm');
            $this->filterBuilder->setValue($priceRangeCalculation);
            $this->searchCriteriaBuilder->addFilter($this->filterBuilder->create());
        }

        $searchCriteria = $this->searchCriteriaBuilder->create();
        $searchCriteria->setRequestName($this->searchRequestName);
        try {
            $this->searchResult = $this->searchInterface->search($searchCriteria);
        } catch (EmptyRequestDataException $e) {
            /** @var \Magento\Framework\Api\Search\SearchResultInterface $searchResult */
            $this->searchResult = $this->searchResultFactory->create()->setItems([]);
        } catch (NonExistingRequestNameException $e) {
            $this->_logger->error($e->getMessage());
            throw new LocalizedException(__('Sorry, something went wrong. You can find out more in the error log.'));
        }

        $temporaryStorage = $this->temporaryStorageFactory->create();
        $table = $temporaryStorage->storeApiDocuments($this->searchResult->getItems());

        $this->getSelect()->joinInner(
            [
                'search_result' => $table->getName(),
            ],
            'e.entity_id = search_result.' . TemporaryStorage::FIELD_ENTITY_ID,
            []
        );

        $this->_totalRecords = $this->searchResult->getTotalCount();

        if ($this->order && 'relevance' === $this->order['field']) {
            $this->getSelect()->order('search_result.'. TemporaryStorage::FIELD_SCORE . ' ' . $this->order['dir']);
        }
        //return parent::_renderFiltersBefore();
        return \Magento\Catalog\Model\ResourceModel\Product\Collection::_renderFiltersBefore();
    }

    /**
     * Return field faceted data from faceted search result
     *
     * @param string $field
     * @return array
     * @throws StateException
     */
    public function getFacetedData($field)
    {
        $this->_renderFilters();
        $result = [];
        $aggregations = $this->searchResult->getAggregations();
        // This behavior is for case with empty object when we got EmptyRequestDataException
        if (null !== $aggregations) {
            $bucket = $aggregations->getBucket($field . RequestGenerator::BUCKET_SUFFIX);
            if ($bucket) {
                foreach ($bucket->getValues() as $value) {
                    $metrics = $value->getMetrics();
                    $result[$metrics['value']] = $metrics;
                }
            } else {
                throw new StateException(__('Bucket does not exist'));
            }
        }
        return $result;
    }

}
