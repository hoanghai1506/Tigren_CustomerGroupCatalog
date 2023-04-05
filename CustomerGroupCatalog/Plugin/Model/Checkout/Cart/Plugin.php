<?php

namespace Tigren\CustomerGroupCatalog\Plugin\Model\Checkout\Cart;

use Magento\Framework\Exception\LocalizedException;

class Plugin
{
    public function beforeAddProduct($subject, $productInfo, $requestInfo = null)
    {

        try {
            //            dd($subject);
        } catch (\Exception $e) {
            throw new LocalizedException(__($e->getMessage()));
        }

        return [$productInfo, $requestInfo];
    }
}
