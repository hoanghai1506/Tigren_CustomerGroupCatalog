<?php

namespace Tigren\CustomerGroupCatalog\Pricing\Render;

use Magento\Catalog\Pricing\Price;
use Magento\Framework\Pricing\Render;
use Magento\Framework\Pricing\Render\PriceBox as BasePriceBox;
use Magento\Msrp\Pricing\Price\MsrpPrice;
use Magento\Framework\App\ResourceConnection;
class FinalPriceBox extends \Magento\Catalog\Pricing\Render\FinalPriceBox
{
    protected $_resource;
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Pricing\SaleableInterface $saleableItem,
        \Magento\Framework\Pricing\Price\PriceInterface $price,
        \Magento\Framework\Pricing\Render\RendererPool $rendererPool,
        ResourceConnection $resource,
        array $data = [],
        \Magento\Catalog\Model\Product\Pricing\Renderer\SalableResolverInterface $salableResolver = null,
        \Magento\Catalog\Pricing\Price\MinimalPriceCalculatorInterface $minimalPriceCalculator = null
    ) {
        parent::__construct($context,
            $saleableItem,
            $price,
            $rendererPool,
            $data,
            $salableResolver,
            $minimalPriceCalculator);
        $this->_resource = $resource;
    }

    protected function wrapResult($html)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $httpContext = $objectManager->get('Magento\Framework\App\Http\Context');
        $isLoggedIn = $httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH);
        $conn = $this->_resource->getConnection();
        $table = $this->_resource->getTableName('tigren_customergroupcatalog_rule');
        $select = $conn->select()
                       ->from($table)
                        ->join(
                            ['soi' => $this->_resource->getTableName('tigren_rule_products')],
                            'tigren_customergroupcatalog_rule.rule_id = soi.rule_id'
                        )
                       ->where('product_id = ?', $this->getSaleableItem()->getId());
        $resultSql = $conn->fetchAll($select);
//        dd($resultSql[0]['discount_amount']);
//        dd($resultSql);
        if ($isLoggedIn) {
            return '<div class="price-box ' . $this->getData('css_classes') . '" ' .
                'data-role="priceBox" ' .
                'data-product-id="' . $this->getSaleableItem()->getId() . '"' .
                ' xmlns="http://www.w3.org/1999/html" xmlns="http://www.w3.org/1999/html">' . $html . '</br>'.'<p style="color: red">Dicount: <?php ?></p>'. $resultSql[0]['discount_amount'].'%'  . '</div>';
        } else {
            $wording = 'Please Login To See Price';
            return '<div class="" ' .
                'data-role="priceBox" ' .
                'data-product-id="' . $this->getSaleableItem()->getId() . '"' .
                '>' . $wording . '</div>';
        }
    }
}
