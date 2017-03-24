<?php
/**
 * Copyright © 2015 J2T Design. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace J2t\Rewardpoints\Model\Resource\Point;

/**
 * Quotes collection
 *
 * @author      J2T Design Team <contact@j2t-design.net>
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    
    protected $dateTime;
    protected $_customerHelperData, $_rewardHelperData;
    protected $_scopeConfig;
    
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactory $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        \Magento\Eav\Helper\Data $customerHelperData,
        \J2t\Rewardpoints\Helper\Data $rewardHelperData,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,    
        $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        $this->dateTime = $dateTime;
        $this->_customerHelperData = $customerHelperData;
        $this->_rewardHelperData = $rewardHelperData;
        $this->_scopeConfig = $scopeConfig;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
    }
    
    
    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        //$this->_init('Magento\Quote\Model\Quote', 'Magento\Quote\Model\Resource\Quote');
        $this->_init('J2t\Rewardpoints\Model\Point', 'J2t\Rewardpoints\Model\Resource\Point');
    }
    
    public function setUserFilter($userId)
    {
        $this->addFieldToFilter('customer_id', $userId);
        return $this;
    }
    
    public function setStoreFilter($storeId)
    {
        $this->addFieldToFilter('store_id', $storeId);
        return $this;
    }
    
    public function setDateOrder($dir = 'DESC')
    {
        $this->setOrder('main_table.date_insertion', $dir);
        return $this;
    }
    
	public function addFinishFilter($days)
    {
        $this->getSelect()->where('( DATEDIFF(main_table.date_end, NOW()) = ? AND main_table.date_end IS NOT NULL)', $days);
        return $this;
    }
    
    public function addClientEntries()
    {
        
        $this->getSelect()->joinLeft(
            ['cust' => $this->getTable('customer_entity')],
            'main_table.customer_id = cust.entity_id'
        );
        
        $this->getSelect()->joinLeft(
            ['fl_table' => $this->getTable('rewardpoints_account')],
            'main_table.rewardpoints_account_id = fl_table.rewardpoints_account_id',
            ['current_store_id' => 'fl_table.store_id', 'current_customer_id' => 'fl_table.customer_id']
        );

        return $this;
    }
    
    public function showCustomerInfo()
    {
        //$adapter = $this->getConnection();
		
		$this->getSelect()->joinLeft(
            [
                'customer' => $this->getTable('customer_entity')
            ],
            'main_table.customer_id',
            [
                'customer_lastname' => 'lastname',
                'customer_firstname' => 'firstname'
            ]
        );
        
        return $this;
    }
    
    public function addValidPoints($storeId, $unset_date_limits = false, $no_sum = false, $orderIds = [])
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $statuses = $this->_rewardHelperData->getValidStatuses($storeId);
        $statuses_used = $this->_rewardHelperData->getValidUsedStatuses($storeId);
        $status_field = $this->_rewardHelperData->getStatusField($storeId);
        
        $order_states = explode(",", $statuses);
        $order_states_used = explode(",", $statuses_used);
        
        if (!$no_sum){
            $cols['points_current'] = 'SUM(main_table.points_current) as nb_credit';
            $cols['points_spent'] = 'SUM(main_table.points_spent) as nb_credit_spent';
            $cols['points_available'] = '(SUM(main_table.points_current) - SUM(main_table.points_spent)) as nb_credit_available';
            $this->getSelect()->from($this->getTable('rewardpoints_account').' as child_table', $cols);
        }
        
        // checking if module rewardshare is available
        $sql_share = "";
        $sql_required = "";
        //J2T magento 1.3.x fix
        /*TODO /// if (class_exists('J2t_Rewardshare_Model_Stats', false)){
            $sql_share = "main_table.order_id = '".J2t_Rewardshare_Model_Stats::TYPE_POINTS_SHARE."' or";
        }
        if (Mage::getConfig()->getModuleConfig('J2t_Rewardproductvalue')->is('active', 'true')){
            if (version_compare(Mage::getVersion(), '1.4.0', '>=')){
                $sql_required = "(  
                                    main_table.order_id = '".Rewardpoints_Model_Stats::TYPE_POINTS_REQUIRED ."'
                                    AND main_table.rewardpoints_$status_field in (?,'new')    
                                 ) or ";
                
            }
        }*/
        
        $model = $objectManager->get('J2t\Rewardpoints\Model\Point');
        
        if ($orderIds == []) {
            $points_type = $model->constructSqlPointsType("main_table");
            $this->getSelect()->where(" (".$points_type."
                OR ( main_table.rewardpoints_$status_field = 'new' AND main_table.points_spent > 0)
                OR ( main_table.rewardpoints_$status_field in (?) AND main_table.points_current > 0 )
                OR ( main_table.rewardpoints_$status_field in (?) AND main_table.points_spent > 0 )    
                )", $order_states, $order_states_used);
        } else {
            $points_type = $model->constructSqlPointsType("main_table", $orderIds);
            $this->getSelect()->where(" ( $points_type )");
        }
        
        if (!$no_sum){
            $this->getSelect()->where('main_table.rewardpoints_account_id = child_table.rewardpoints_account_id');
        }

        if ($this->_rewardHelperData->isApplyStoreScope($storeId)){
            $this->getSelect()->where('find_in_set(?, main_table.store_id)', $storeId);
        }

        //v.2.0.0
        $delay = $this->_rewardHelperData->getPointsDelay($storeId);
        if ($delay && !$unset_date_limits){
            $this->getSelect()->where('( NOW() >= main_table.date_start OR main_table.date_start IS NULL)');
        }
        
        $duration = $this->_rewardHelperData->getPointsDuration($storeId);
        if ($duration && !$unset_date_limits){
            $this->getSelect()->where('( main_table.date_end >= NOW() or main_table.date_end IS NULL)');
        }
        
        if (!$no_sum){
            $this->getSelect()->group('main_table.customer_id');
        } else if ($orderIds != []){
            $this->getSelect()->group('main_table.order_id');
        }
        
        /*echo $this->getSelect()->__toString();
        die;*/
        
        return $this;
    }
    
    
    public function loadallpointsbydate($storeId, $customerId, $date_end = null){
        $this->getSelect()->where("main_table.customer_id = ?", $customerId);
        if ($date_end){
            $this->getSelect()->where("( ? <= main_table.date_end )", $date_end);
        }
        $this->getSelect()->where("( main_table.date_start IS NOT NULL OR main_table.date_end IS NOT NULL )");
        $this->addValidPoints($storeId, true, true);
        $this->setOrder('date_start ',  'ASC');
        $this->setOrder('points_current ',  'DESC');
        $this->setOrder('date_end ',  'ASC');
        /*echo $collection->getSelect()->__toString();
        die;*/
        return $this;
    }
    
    
    public function addUsedpointsbydate($storeId, $customerId){
        $cols['points_spent'] = 'SUM(main_table.points_spent) as nb_credit_spent';
        $cols['date_order'] = 'DATE_FORMAT(main_table.date_insertion, "%Y-%m-%d") as date_order';
        
        //selection de tous les points utilisés à x date
        $this->getSelect()->from($this->getTable('rewardpoints_account').' as child_table', $cols);
        
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $statuses = $this->_rewardHelperData->getValidStatuses($storeId);
        $statuses_used = $this->_rewardHelperData->getValidUsedStatuses($storeId);
        $status_field = 'rewardpoints_'.$this->_rewardHelperData->getStatusField($storeId);
        
        $model = $objectManager->get('J2t\Rewardpoints\Model\Point');
        $points_type = $model->getPointsDefaultTypeToArray();
        
        $order_states = explode(",", $statuses);
        $order_states_used = explode(",", $statuses_used);
        
        foreach ($points_type as $key => $value){
            //remove admin point
            if ($key != \J2t\Rewardpoints\Model\Point::TYPE_POINTS_ADMIN){
                $only_valid_types[] = $key;
            }
        }
        $this->getSelect()->where("(main_table.rewardpoints_state in (?,'new') AND main_table.points_current > 0) OR (main_table.$status_field in (?,'new') AND main_table.points_spent > 0) OR main_table.$status_field IS NULL", $order_states, $order_states_used);
        $this->getSelect()->where("main_table.customer_id = ?", $customerId);
        $this->getSelect()->where("main_table.points_spent > 0");
        $this->getSelect()->where("main_table.order_id NOT IN (?)", $only_valid_types);
        
        $this->getSelect()->where('main_table.rewardpoints_account_id = child_table.rewardpoints_account_id');

        if ($this->_rewardHelperData->isApplyStoreScope($storeId)){
            $this->getSelect()->where('find_in_set(?, main_table.store_id)', $storeId);
        }

        $this->getSelect()->group('main_table.date_order');
        
        /*echo $this->getSelect()->__toString();
        die;*/
        
        return $this;
        
    }
    
    
    public function getDateRange($range, $customStart, $customEnd, $returnObjects = false)
    {
        $dateEnd = new \DateTime();
        $dateStart = new \DateTime();

        // go to the end of a day
        $dateEnd->setTime(23, 59, 59);

        $dateStart->setTime(0, 0, 0);

        switch ($range)
        {
            case '24h':
                $dateEnd = new \DateTime();
                $dateEnd->modify('+1 hour');
                $dateStart = clone $dateEnd;
                $dateStart->modify('-1 day');
                break;

            case '7d':
                // substract 6 days we need to include
                // only today and not hte last one from range
                $dateStart->modify('-6 days');
                break;

            case '1m':
                $dateStart->setDate(
                    $dateStart->format('Y'),
                    $dateStart->format('m'),
                    $this->_scopeConfig->getValue(
                        'reports/dashboard/mtd_start',
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                    )
                );
                break;

            case 'custom':
                $dateStart = $customStart ? $customStart : $dateEnd;
                $dateEnd = $customEnd ? $customEnd : $dateEnd;
                break;

            case '1y':
            case '2y':
                /*$startMonthDay = explode(',', Mage::getStoreConfig('reports/dashboard/ytd_start'));
                $startMonth = isset($startMonthDay[0]) ? (int)$startMonthDay[0] : 1;
                $startDay = isset($startMonthDay[1]) ? (int)$startMonthDay[1] : 1;
                $dateStart->setMonth($startMonth);
                $dateStart->setDay($startDay);
                if ($range == '2y') {
                    $dateStart->subYear(1);
                }*/
                $startMonthDay = explode(
                    ',',
                    $this->_scopeConfig->getValue(
                        'reports/dashboard/ytd_start',
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                    )
                );
                $startMonth = isset($startMonthDay[0]) ? (int)$startMonthDay[0] : 1;
                $startDay = isset($startMonthDay[1]) ? (int)$startMonthDay[1] : 1;
                $dateStart->setDate($dateStart->format('Y'), $startMonth, $startDay);
                if ($range == '2y') {
                    $dateStart->modify('-1 year');
                }
                
                break;
        }

        if ($returnObjects) {
            return [$dateStart, $dateEnd];
        } else {
            return ['from' => $dateStart, 'to' => $dateEnd, 'datetime' => true];
        }
    }
    
    public function prepareSummary($range, $customStart, $customEnd, $isFilter = 0)
    {
        $this->_prepareSummaryAggregated($range, $customStart, $customEnd, $isFilter);
        return $this;
    }
    
    protected function getConcatSql(array $data, $separator = null)
    {
        $format = empty($separator) ? 'CONCAT(%s)' : "CONCAT_WS('{$separator}', %s)";
        return new \Zend_Db_Expr(sprintf($format, implode(', ', $data)));
    }
    
    protected function getDateFormatSql($date, $format)
    {
        $expr = sprintf("DATE_FORMAT(%s, '%s')", $date, $format);
        return new \Zend_Db_Expr($expr);
    } 
    
    protected function _getRangeExpression($range)
    {
        switch ($range)
        {
            case '24h':
                /*$expression = $this->getConnection()->getConcatSql(array(
                    $this->getConnection()->getDateFormatSql('{{attribute}}', '%Y-%m-%d %H:'),
                    $this->getConnection()->quote('00')
                ));*/
		$expression = $this->getConcatSql(array(
                    $this->getDateFormatSql('{{attribute}}', '%Y-%m-%d %H:'),
                    $this->getConnection()->quote('00')
                ));
                break;
            case '7d':
            case '1m':
                //$expression = $this->getConnection()->getDateFormatSql('{{attribute}}', '%Y-%m-%d');
                $expression = $this->getDateFormatSql('{{attribute}}', '%Y-%m-%d');
		break;
            case '1y':
            case '2y':
            case 'custom':
            default:
                //$expression = $this->getConnection()->getDateFormatSql('{{attribute}}', '%Y-%m');
                $expression = $this->getDateFormatSql('{{attribute}}', '%Y-%m');
		break;
        }

        return $expression;
    }
    
    protected function _getRangeExpressionForAttribute($range, $attribute)
    {
        $expression = $this->_getRangeExpression($range);
        return str_replace('{{attribute}}', $this->getConnection()->quoteIdentifier($attribute), $expression);
    }
    
    protected function _prepareSummaryAggregated($range, $customStart, $customEnd)
    {
        $this->setMainTable('rewardpoints_account');
        /**
         * Reset all columns, because result will group only by 'date_insertion' or 'period' field
         */
        $this->getSelect()->reset(\Magento\Framework\DB\Select::COLUMNS);
        //$rangePeriod = $this->_getRangeExpressionForAttribute($range, 'main_table.date_insertion');
        
        /********/
        //$this->getSelect()->reset(Zend_Db_Select::COLUMNS);
        
        if ($range == '24h'){
            $rangePeriod = $this->_getRangeExpressionForAttribute($range, 'main_table.date_insertion');
            $tableName = $this->getConnection()->quoteIdentifier('main_table.date_insertion');
        } else {
            $rangePeriod = $this->_getRangeExpressionForAttribute($range, 'main_table.period');
            $tableName = $this->getConnection()->quoteIdentifier('main_table.period');
        }

        $rangePeriod2 = str_replace($tableName, "MIN($tableName)", $rangePeriod);
        $this->getSelect()->columns(array(
            'points_current'  => 'SUM(main_table.points_current)',
            'points_spent' => 'SUM(main_table.points_spent)',
            'range' => $rangePeriod2,
        ))
        ->order('range')
        ->group($rangePeriod);

        if ($range == '24h'){
            $this->getSelect()->where(
                $this->_getConditionSql('main_table.date_insertion', $this->getDateRange($range, $customStart, $customEnd))
            );
        } else {
            $this->getSelect()->where(
                $this->_getConditionSql('main_table.period', $this->getDateRange($range, $customStart, $customEnd))
            );
        }
        
        //$statuses = $this->_rewardHelperData->getValidStatuses();
        //$statuses_used = $this->_rewardHelperData->getValidUsedStatuses();
        
        //$statuses = Mage::getStoreConfig('rewardpoints/default/valid_statuses');
        //$statuses_used = Mage::getStoreConfig('rewardpoints/default/valid_used_statuses');
        //$this->joinValidPointsOrder(false, false, explode(",", $statuses), explode(",", $statuses_used), false, true, true, [], true);

        //TODO: check remove start from original script
        $this->addValidPoints(null, true, true);
        
        
        
        //echo $this->getSelect()->__toString();
        //die;

        return $this;
    }
    
    public function calculateTotals($onlyNonOrders = false)
    {
        //$this->setMainTable('rewardpoints/stats');
        $this->setMainTable('rewardpoints_account');
        $this->removeAllFieldsFromSelect();
        $adapter = $this->getConnection();

        if (!$onlyNonOrders){
            $this->getSelect()->columns(
                array(
                    'all_points_gathered'   => new \Zend_Db_Expr('SUM(main_table.points_current)'),
                    'all_points_spent'      => new \Zend_Db_Expr('SUM(main_table.points_spent)')
                )
            );
            //$this->joinValidPointsOrder(false, false, explode(",", $statuses), explode(",", $statuses_used), false, true, true, array(), true);
            $this->addValidPoints(null, true, true);
        } else {
            $this->getSelect()->columns(
                array(
                    'all_points_gathered'   => new \Zend_Db_Expr('SUM(main_table.points_current)'),
                    'all_points_spent'      => new \Zend_Db_Expr('SUM(main_table.points_spent)'),
                    'order_id'              => 'main_table.order_id'
                )
            );
            
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $model = $objectManager->get('J2t\Rewardpoints\Model\Point');
            //$this->joinValidPointsOrder(false, false, explode(",", $statuses), explode(",", $statuses_used), false, true, true, Mage::getModel("rewardpoints/stats")->getOnlyPointsTypesArray(), true);
            $this->addValidPoints(null, true, true, $model->getOnlyPointsTypesArray());
        }
        
        //echo $this->getSelect()->__toString();
        //die;

        return $this;
    }
    
    public function addCreateAtPeriodFilter($period)
    {
        list($from, $to) = $this->getDateRange($period, 0, 0, true);

        $fieldToFilter = 'main_table.date_insertion';

        $this->addFieldToFilter(
            $fieldToFilter,
            [
                'from' => $from->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT),
                'to' => $to->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT)
            ]
        );

        return $this;
    }
    
}
