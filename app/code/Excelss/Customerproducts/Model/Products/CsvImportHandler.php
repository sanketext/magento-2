<?php

namespace Excelss\Customerproducts\Model\Products;

/**
 * Customer Products CSV Import Handler
 */
class CsvImportHandler
{
    /**
     * Collection of publicly available stores
     *
     * @var \Magento\Store\Model\ResourceModel\Store\Collection
     */
    protected $_publicStores;

    /**
     * CSV Processor
     *
     * @var \Magento\Framework\File\Csv
     */
    protected $csvProcessor;

    /**
     * CustomerProducts factory
     *
     * @var \Excelss\Customerproducts\Model\ResourceModel\Customerproducts\Collection
     */
    protected $_customerproductsFactory;

    /**
     * @param \Magento\Store\Model\ResourceModel\Store\Collection $storeCollection
     * @param \Magento\Framework\File\Csv $csvProcessor
     */
    public function __construct(
        \Magento\Store\Model\ResourceModel\Store\Collection $storeCollection,
        \Excelss\Customerproducts\Model\CustomerProductsFactory $customerproductsFactory,
        \Magento\Framework\File\Csv $csvProcessor
    ) {
        // prevent admin store from loading
        $this->_publicStores = $storeCollection->setLoadDefault(false);
        $this->_customerproductsFactory = $customerproductsFactory;
        $this->csvProcessor = $csvProcessor;
    }

    /**
     * Retrieve a list of fields required for CSV file (order is important!)
     *
     * @return array
     */
    public function getRequiredCsvFields()
    {
        // indexes are specified for clarity, they are used during import
        return [
            0 => __('Customer ID'),
            1 => __('Product ID'),
            2 => __('Position')
        ];
    }

    /**
     * Customer Products from CSV file
     *
     * @param array $file file info retrieved from $_FILES array
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function importFromCsvFile($file)
    {
        if (!isset($file['tmp_name'])) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Invalid file upload attempt.'));
        }
        $ratesRawData = $this->csvProcessor->getData($file['tmp_name']);
        // first row of file represents headers
        $fileFields = $ratesRawData[0];
        $validFields = $this->_filterFileFields($fileFields);
        $invalidFields = array_diff_key($fileFields, $validFields);
        $custproData = $this->_filterCustomerproData($ratesRawData, $invalidFields, $validFields);
       
        foreach ($custproData as $rowIndex => $dataRow) {
            // skip headers
            if ($rowIndex == 0) {
                continue;
            }
            $this->_importCustomerproducts($dataRow);
        }
    }

    /**
     * Filter file fields (i.e. unset invalid fields)
     *
     * @param array $fileFields
     * @return string[] filtered fields
     */
    protected function _filterFileFields(array $fileFields)
    {
        $filteredFields = $this->getRequiredCsvFields();
        $requiredFieldsNum = count($this->getRequiredCsvFields());
        $fileFieldsNum = count($fileFields);

        // process title-related fields that are located right after required fields with store code as field name)
        for ($index = $requiredFieldsNum; $index < $fileFieldsNum; $index++) {
            $titleFieldName = $fileFields[$index];
            $filteredFields[$index] = $titleFieldName;
        }

        return $filteredFields;
    }

    /**
     * Filter rates data (i.e. unset all invalid fields and check consistency)
     *
     * @param array $rateRawData
     * @param array $invalidFields assoc array of invalid file fields
     * @param array $validFields assoc array of valid file fields
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    protected function _filterCustomerproData(array $rateRawData, array $invalidFields, array $validFields)
    {
        $validFieldsNum = count($validFields);
        foreach ($rateRawData as $rowIndex => $dataRow) {
            // skip empty rows
            if (count($dataRow) <= 1) {
                unset($rateRawData[$rowIndex]);
                continue;
            }
            // unset invalid fields from data row
            foreach ($dataRow as $fieldIndex => $fieldValue) {
                if (isset($invalidFields[$fieldIndex])) {
                    unset($rateRawData[$rowIndex][$fieldIndex]);
                }
            }
            // check if number of fields in row match with number of valid fields
            if (count($rateRawData[$rowIndex]) != $validFieldsNum) {
                throw new \Magento\Framework\Exception\LocalizedException(__('Invalid file format.'));
            }
        }
        return $rateRawData;
    }

    /**
     * Import single products
     *
     * @param array $customerproData
     * @param array $storesCache cache of stores related to tax rate titles
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _importCustomerproducts(array $customerproData)
    {
        // create model data to import csv to database
        $modelData = [
            'customer_id' => $customerproData[0],
            'product_id' => $customerproData[1],
            'product_position' => $customerproData[2],
        ];

        // try to load existing ID
        $customerProductsModel = $this->_customerproductsFactory->create()->load($modelData['product_id'], 'product_id');
        $customerProductsModel->addData($modelData);
        $customerProductsModel->save();
    }

}
