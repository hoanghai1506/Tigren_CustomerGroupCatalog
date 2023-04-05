<?php

namespace Tigren\CustomerGroupCatalog\Block;

use Magento\Framework\App\ResourceConnection;

class MyOrders extends \Magento\Framework\View\Element\Template {
    protected $_resource;

    public function __construct(
        ResourceConnection $resource,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = [],
    ) {
        parent::__construct( $context );
        $this->_resource = $resource;

    }

    public function getOrders() {
        $objectManager   = \Magento\Framework\App\ObjectManager::getInstance();
        $customerSession = $objectManager->create( 'Magento\Customer\Model\Session' );
        $custonerId      = $customerSession->getCustomerId();
        $connection      = $this->_resource->getConnection();
        $table           = $this->_resource->getTableName( 'tigren_group_catalog_history_table' );
        $select          = $connection->select()
                                      ->from( $table )
                                      ->join(
                                          [ 'soi' => $this->_resource->getTableName( 'tigren_customergroupcatalog_rule' ) ],
                                          'tigren_group_catalog_history_table.rule_id = soi.rule_id' )
                                      ->where( 'customer_id = ?', $custonerId )
                                      ->group( 'order_id' );
        $result          = $connection->fetchAll( $select );


        return $result;

    }
}
