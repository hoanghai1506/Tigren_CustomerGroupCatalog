<?php

namespace Tigren\CustomerGroupCatalog\Plugin;

use Magento\Framework\App\ResourceConnection;

class ChangeProductPrice {
    protected $_resource;

    public function __construct( ResourceConnection $resource ) {
        $this->_resource = $resource;
    }

    public function afterGetPrice( \Magento\Catalog\Model\Product $subject, $result ) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $conn          = $this->_resource->getConnection();
        $session       = $objectManager->create( \Magento\Customer\Model\Session::class );
        $storeManager  = $objectManager->get( '\Magento\Store\Model\StoreManagerInterface' );
        $select        = $conn->select()
                              ->from( [ 'so' => $this->_resource->getTableName( 'tigren_customergroupcatalog_rule' ) ] )
                              ->join( [ 'soi' => $this->_resource->getTableName( 'tigren_rule_store' ) ],
                                  'so.rule_id = soi.rule_id' )
                              ->join( [ 'soii' => $this->_resource->getTableName( 'tigren_rule_customer_group' ) ],
                                  'so.rule_id = soii.rule_id' )
                              ->join( [ 'soiii' => $this->_resource->getTableName( 'tigren_rule_products' ) ],
                                  'so.rule_id = soiii.rule_id', )
                              ->where( 'product_id =' . $subject->getId() )
                              ->where( 'store_id = ' . $storeManager->getStore()->getStoreId() )
                              ->where( 'customer_group_id = ' . $session->getCustomer()->getGroupId() )
                              ->where( 'active = 1' );
        $resultSql     = $conn->fetchAll( $select );

        $max           = 0;
        $discount      = 0;
        $per          = 1;
        foreach ( $resultSql as $r ) {
            if ( $r['priority'] > $max ) {
                $max      = $r['priority'];
                $discount = $r['discount_amount'];
            }
        }
        $per = ( 100 - $discount ) / 100;


        return $result * ( $per );
    }
}
