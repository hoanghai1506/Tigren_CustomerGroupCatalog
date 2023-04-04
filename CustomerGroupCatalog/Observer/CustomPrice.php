<?php
/**
 *
 */

namespace Tigren\CustomerGroupCatalog\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\ResourceConnection;
class CustomPrice implements ObserverInterface
{
    protected $_objectManager;
    protected $logger;
    protected $_resource;
    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        \Magento\Sales\Model\Order $order,
        ResourceConnection $resource,
    ) {
        $this->_objectManager = $objectManager;
        $this->order = $order;
        $this->logger = $logger;
        $this->_resource = $resource;
    }
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        $orderId = 2;
        $conn          = $this->_resource->getConnection();
        $table = $this->_resource->getTableName('tigren_group_catalog_history_table');
        $sql = "INSERT INTO " . $table . " (order_id) VALUES " . "('" . $orderId . "')";
        $result = $conn->query($sql);
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/debug.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info( $orderId );

    }

}
