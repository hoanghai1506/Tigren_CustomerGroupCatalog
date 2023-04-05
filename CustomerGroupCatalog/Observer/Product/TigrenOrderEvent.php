<?php
/**
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2023 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Tigren\CustomerGroupCatalog\Observer\Product;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\ResourceConnection;

class TigrenOrderEvent implements ObserverInterface {



    /**
     * Below is the method that will fire whenever the event runs!
     *
     * @param Observer $observer
     */

    protected $_resource;

    public function __construct(
        ResourceConnection $resource,
    ) {
        $this->_resource = $resource;
    }

    public function execute( Observer $observer ) {
        try {
            $conn  = $this->_resource->getConnection();
            $table = $this->_resource->getTableName( 'tigren_group_catalog_history_table' );

            $order       = $observer->getEvent()->getOrder();
            $items       = $order->getAllItems();
            $order_id    = $order->getIncrementId();
            $customer_id = $order->getCustomerId();
            $rule_id     = [];
            foreach ( $items as $item ) {
                $store  = $conn->select()
                               ->from( [ 'so' => $this->_resource->getTableName( 'tigren_rule_products' ) ] )
                               ->where( 'so.product_id = ' . $item->getProductId() );
                $result = $conn->fetchAll( $store );
                array_push( $rule_id, $result[0]['rule_id'] );
            }
            foreach ( $rule_id as $id ) {
                $conn->insert( $table, [
                    'customer_id' => $customer_id,
                    'order_id'    => $order_id,
                    'rule_id'     => $id,
                ] );
            }
        } catch ( \Exception $e ) {
            $writer = new \Zend_Log_Writer_Stream( BP . '/var/log/custom.log' );
            $logger = new \Zend_Log();
            $logger->addWriter( $writer );
            $logger->info( print_r( $e, true ) );

        }

    }
}
