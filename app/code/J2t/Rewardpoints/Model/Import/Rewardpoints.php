<?php

namespace J2t\Rewardpoints\Model\Import;
use J2t\Rewardpoints\Model\Import\Rewardpoints\RowValidatorInterface as ValidatorInterface;
use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingErrorAggregatorInterface;

class Rewardpoints extends \Magento\ImportExport\Model\Import\Entity\AbstractEntity
{
    const POINTS = 'points';
    const ORDER_ID = 'order_id';
    const WEBSITE_ID = 'website_id';
    const CUSTOMER_EMAIL = 'email';
    const TABLE_Entity = 'rewardpoints_account';
    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $_messageTemplates = [
        ValidatorInterface::ERROR_POINTS_IS_EMPTY => 'Points is empty',
        ValidatorInterface::ERROR_EMAIL_IS_EMPTY => 'Email is empty',
        ValidatorInterface::ERROR_CUSTOMER_IS_EMPTY => 'Unable to identify customer',
    ];
     protected $_permanentAttributes = [self::POINTS, self::CUSTOMER_EMAIL];
    /**
     * If we should check column names
     *
     * @var bool
     */
    protected $needColumnCheck = true;
    protected $customerFactory;
    /**
     * Valid column names
     *
     * @array
     */
    protected $validColumnNames = [
        self::POINTS,
        self::ORDER_ID,
        self::WEBSITE_ID,
        self::CUSTOMER_EMAIL
    ];
    /**
     * Need to log in import history
     *
     * @var bool
     */
    protected $logInHistory = true;
    protected $_validators = [];
    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_connection;
    protected $_resource;
    /**
     * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
     */
    public function __construct(
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\ImportExport\Helper\Data $importExportData,
        \Magento\ImportExport\Model\ResourceModel\Import\Data $importData,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\ImportExport\Model\ResourceModel\Helper $resourceHelper,
        \Magento\Framework\Stdlib\StringUtils $string,
        ProcessingErrorAggregatorInterface $errorAggregator,
        \Magento\Customer\Model\CustomerFactory $customerFactory
    ) {
        $this->jsonHelper = $jsonHelper;
        $this->_importExportData = $importExportData;
        $this->_resourceHelper = $resourceHelper;
        $this->_dataSourceModel = $importData;
        $this->_resource = $resource;
        $this->_connection = $resource->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);
        $this->errorAggregator = $errorAggregator;
        $this->customerFactory = $customerFactory;
    }
    /**
     * Entity type code getter.
     *
     * @return string
     */
    public function getEntityTypeCode()
    {
        return 'rewardpoints_account';
    }
    /**
     * Row validation.
     *
     * @param array $rowData
     * @param int $rowNum
     * @return bool
     */
    public function validateRow(array $rowData, $rowNum)
    {
        $title = false;
        if (isset($this->_validatedRows[$rowNum])) {
            return !$this->getErrorAggregator()->isRowInvalid($rowNum);
        }
        $this->_validatedRows[$rowNum] = true;
        // BEHAVIOR_DELETE use specific validation logic
       // if (\Magento\ImportExport\Model\Import::BEHAVIOR_DELETE == $this->getBehavior()) {
            if (!isset($rowData[self::POINTS]) || empty($rowData[self::POINTS])) {
                $this->addRowError(ValidatorInterface::ERROR_POINTS_IS_EMPTY, $rowNum);
                return false;
            }
            if (!isset($rowData[self::CUSTOMER_EMAIL]) || empty($rowData[self::CUSTOMER_EMAIL])) {
                $this->addRowError(ValidatorInterface::ERROR_EMAIL_IS_EMPTY, $rowNum);
                return false;
            }
        return !$this->getErrorAggregator()->isRowInvalid($rowNum);
    }
    /**
     * Create Advanced price data from raw data.
     *
     * @throws \Exception
     * @return bool Result of operation.
     */
    protected function _importData()
    {
        /*if (\Magento\ImportExport\Model\Import::BEHAVIOR_DELETE == $this->getBehavior()) {
            $this->deleteEntity();
        } elseif (\Magento\ImportExport\Model\Import::BEHAVIOR_REPLACE == $this->getBehavior()) {
            $this->replaceEntity();
        } elseif (\Magento\ImportExport\Model\Import::BEHAVIOR_APPEND == $this->getBehavior()) {
            $this->saveEntity();
        }*/
        $this->saveEntity();
        return true;
    }
    /**
     * Save newsletter subscriber
     *
     * @return $this
     */
    public function saveEntity()
    {
        $this->saveAndReplaceEntity();
        return $this;
    }
    /**
     * Replace newsletter subscriber
     *
     * @return $this
     */
    public function replaceEntity()
    {
        $this->saveAndReplaceEntity();
        return $this;
    }
    /**
     * Deletes newsletter subscriber data from raw data.
     *
     * @return $this
     */
    public function deleteEntity()
    {
        $listTitle = [];
        while ($bunch = $this->_dataSourceModel->getNextBunch()) {
            foreach ($bunch as $rowNum => $rowData) {
                $this->validateRow($rowData, $rowNum);
                if (!$this->getErrorAggregator()->isRowInvalid($rowNum)) {
                    $rowTtile = $rowData[self::POINTS];
                    $listTitle[] = $rowTtile;
                }
                if ($this->getErrorAggregator()->hasToBeTerminated()) {
                    $this->getErrorAggregator()->addRowToSkip($rowNum);
                }
            }
        }
        if ($listTitle) {
            $this->deleteEntityFinish(array_unique($listTitle),self::TABLE_Entity);
        }
        return $this;
    }
 /**
     * Save and replace newsletter subscriber
     *
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function saveAndReplaceEntity()
    {
        $behavior = $this->getBehavior();
        $listTitle = [];
        while ($bunch = $this->_dataSourceModel->getNextBunch()) {
            $entityList = [];
            foreach ($bunch as $rowNum => $rowData) {
                if (!$this->validateRow($rowData, $rowNum)) {
                    //$this->addRowError(ValidatorInterface::ERROR_POINTS_IS_EMPTY, $rowNum);
                    continue;
                }
                if ($this->getErrorAggregator()->hasToBeTerminated()) {
                    $this->getErrorAggregator()->addRowToSkip($rowNum);
                    continue;
                }
                $rowTtile= $rowData[self::POINTS];
                $listTitle[] = $rowTtile;
                
                /** @var Customer $customer */
                $customer = $this->customerFactory->create();
                if (isset($rowData[self::WEBSITE_ID])) {
                    $customer->setWebsiteId($rowData[self::WEBSITE_ID]);
                }
                $customer->loadByEmail($rowData[self::CUSTOMER_EMAIL]);
                
                $storeId = null;
                if(!$customer->getId()){
                    $this->addRowError(ValidatorInterface::ERROR_CUSTOMER_IS_EMPTY, $rowNum);
                    continue;
                } else {
                    $storeId = $customer->getStoreId();
                }
                
                if ($rowData[self::POINTS] > 0){
                    $entityList[$rowTtile][] = [
                        'points_current' => $rowData[self::POINTS],
                        'points_spent' => 0,
                        self::ORDER_ID => $rowData[self::ORDER_ID],
                        'store_id' => $storeId,
                        'customer_id' => $customer->getId(),
                        'date_insertion' => date('Y-m-d'),
                        'period' => date('Y-m-d'),
                    ];
                } else {
                    $entityList[$rowTtile][] = [
                        'points_current' => 0,
                        'points_spent' => abs($rowData[self::POINTS]),
                        self::ORDER_ID => $rowData[self::ORDER_ID],
                        'store_id' => $storeId,
                        'customer_id' => $customer->getId(),
                        'date_insertion' => date('Y-m-d'),
                        'period' => date('Y-m-d'),
                    ];
                }
                
                
            }
            /*if (\Magento\ImportExport\Model\Import::BEHAVIOR_REPLACE == $behavior) {
                if ($listTitle) {
                    if ($this->deleteEntityFinish(array_unique($listTitle), self::TABLE_Entity)) {
                        $this->saveEntityFinish($entityList, self::TABLE_Entity);
                    }
                }
            } elseif (\Magento\ImportExport\Model\Import::BEHAVIOR_APPEND == $behavior) {*/
                $this->saveEntityFinish($entityList, self::TABLE_Entity);
            //}
        }
        return $this;
    }
    /**
     * Save product prices.
     *
     * @param array $priceData
     * @param string $table
     * @return $this
     */
    protected function saveEntityFinish(array $entityData, $table)
    {
        if ($entityData) {
            $tableName = $this->_connection->getTableName($table);
            $entityIn = [];
            foreach ($entityData as $id => $entityRows) {
                    foreach ($entityRows as $row) {
                        $entityIn[] = $row;
                    }
            }
            if ($entityIn) {
                $this->_connection->insertOnDuplicate($tableName, $entityIn,[
                'points_current',
                'points_spent',
                self::ORDER_ID,
                'store_id',
                'customer_id',
                'date_insertion',
                'period'
            ]);
            }
        }
        return $this;
    }
    protected function deleteEntityFinish(array $listTitle, $table)
    {
        if ($table && $listTitle) {
                try {
                    $this->countItemsDeleted += $this->_connection->delete(
                        $this->_connection->getTableName($table),
                        $this->_connection->quoteInto('customer_group_code IN (?)', $listTitle)
                    );
                    return true;
                } catch (\Exception $e) {
                    return false;
                }
      } else {
            return false;
        }
    }
}
