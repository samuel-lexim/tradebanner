<?php
/**
 * Copyright © 2015 J2T Design. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace J2t\Rewardpoints\Model\Resource\Flatpoint;

/**
 * Quotes collection
 *
 * @author      J2T Design Team <contact@j2t-design.net>
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    
    protected $dateTime;
    protected $_customerHelperData;
    
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactory $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        \Magento\Eav\Helper\Data $customerHelperData,
        $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        $this->dateTime = $dateTime;
        $this->_customerHelperData = $customerHelperData;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
    }
    
    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('J2t\Rewardpoints\Model\Flatpoint', 'J2t\Rewardpoints\Model\Resource\Flatpoint');
    }
    
    public function addCustomerId($userId)
    {
        $this->addFieldToFilter('user_id', $userId);
        return $this;
    }
    
    public function addStoreId($storeId)
    {
        $this->addFieldToFilter('store_id', $storeId);
        return $this;
    }
    
    public function addPointsRange($points_min, $points_max)
    {
        $this->addFieldToFilter('points_current', ['gteq' => $points_min]);
        $this->addFieldToFilter('points_current', ['lteq' => $points_max]);
        return $this;
    }
    
    public function addCheckNotificationDate($duration)
    {
        if (is_numeric($duration)){
            //$date_duration = $this->getResource()->formatDate(mktime(0, 0, 0, date("m"), date("d")-$duration, date("Y")));
            //$this->getSelect()->where('(notification_date < ? OR notification_date IS NULL)', $date_duration);
            //TODO: check or operator
            
            /*
             * if (is_numeric($duration)){
            //$this->getResource()->formatDate(time());
                $date_duration = $this->getResource()->formatDate(mktime(0, 0, 0, date("m"), date("d")-$duration, date("Y")));
                $this->getSelect()->where('(notification_date < ? OR notification_date IS NULL)', $date_duration);
            }
             */
            
            $date_duration = $this->dateTime->formatDate(mktime(0, 0, 0, date("m"), date("d")-$duration, date("Y")));
            //$this->addFieldToFilter(['notification_date', ['lt' => $date_duration]], ['notification_date', 'null']);
            $this->getSelect()->where('(notification_date < ? OR notification_date IS NULL)', $date_duration);
        }
        return $this;
    }
    
    public function addClientEntries()
    {
        
        $this->getSelect()->joinLeft(
            ['cust' => $this->getTable('customer_entity')],
            'main_table.user_id = cust.entity_id'
        );
        
        $this->getSelect()->joinLeft(
            ['fl_table' => $this->getTable('rewardpoints_flat_account')],
            'main_table.flat_account_id = fl_table.flat_account_id',
            ['current_store_id' => 'fl_table.store_id', 'current_customer_id' => 'fl_table.user_id']
        );

        return $this;
    }
    
    
    
    public function showCustomerInfo()
    {
        /*$adapter = $this->getConnection();

        $lastNameData = $this->_customerHelperData->getAttributeMetadata(
            \Magento\Customer\Api\CustomerMetadataInterface::ENTITY_TYPE_CUSTOMER,
            'lastname'
        );
        $firstNameData = $this->_customerHelperData->getAttributeMetadata(
            \Magento\Customer\Api\CustomerMetadataInterface::ENTITY_TYPE_CUSTOMER,
            'firstname'
        );*/

		
		
		$this->getSelect()->joinLeft(
            [
                'customer' => $this->getTable('customer_entity')
            ],
            'main_table.user_id',
            [
                'customer_lastname' => 'lastname',
                'customer_firstname' => 'firstname'
            ]
        );
		
        
        /*$this->getSelect()
            ->joinLeft(
                ['customer_lastname_table' => $lastNameData['attribute_table']],
                $adapter->quoteInto(
                    'customer_lastname_table.entity_id=main_table.user_id
                                     AND customer_lastname_table.attribute_id = ?',
                    (int)$lastNameData['attribute_id']
                ),
                ['customer_lastname' => 'value']
            )
            ->joinLeft(
                ['customer_firstname_table' => $firstNameData['attribute_table']],
                $adapter->quoteInto(
                    'customer_firstname_table.entity_id=main_table.user_id
                                     AND customer_firstname_table.attribute_id = ?',
                    (int)$firstNameData['attribute_id']
                ),
                ['customer_firstname' => 'value']
            );*/

        return $this;
    }
    
    
    /*public function setDateOrder($dir = 'DESC')
    {
        $this->setOrder('main_table.date_insertion', $dir);
        return $this;
    }*/
    
}
