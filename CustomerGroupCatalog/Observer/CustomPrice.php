<?php
/**
 *
 */

namespace Tigren\CustomerGroupCatalog\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ObjectManager;
    use Magento\Framework\App\ResourceConnection;

class CustomPrice implements ObserverInterface {
    protected $_objectManager;
    protected $logger;
    protected $_resource;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        ResourceConnection $resource,
    ) {
        $this->_objectManager = $objectManager;
        $this->order          = $order;
        $this->logger         = $logger;
        $this->_resource      = $resource;
    }

    public function execute( \Magento\Framework\Event\Observer $observer ) {
        $conn        = $this->_resource->getConnection();
        $table       = $this->_resource->getTableName( 'tigren_group_catalog_history_table' );
        $sql         = "INSERT INTO " . $table . " (customer_id,order_id,rule_id) VALUES ('2','3','4')" ;
        $result      = $conn->query( $sql );
    }

}
