<?php namespace F1\SellerClient;

function checkIntArray($acc, $item)
{
    return $acc and is_int($item);
}

function checkNonNegIntArray($acc, $item)
{
    return $acc and is_int($item) and ($item > -1);
}

class SellerClient
{
    private $appId;
    private $appSecret;

    public function __construct($appId, $appSecret)
    {
        if (!is_string($appId) or empty($appId))
        {
            throw new \Exception('appId must be a non-empty string.');
        }
        if (!is_string($appSecret) or empty($appSecret))
        {
            throw new \Exception('appSecret must be a non-empty string.');
        }
        $this->appId = $appId;
        $this->appSecret = $appSecret;
    }

    public function getStockQuantity($sku)
    {
        if (!is_int($sku))
        {
            throw new \Exception('sku must be an integer.');
        }
        return $this->sendRPC('get-stock-quantity', $sku);
    }

    public function setStockQuantity($sku, $qty)
    {
        if (!is_int($sku))
        {
            throw new \Exception("sku must be an integer.");
        }
        if (!is_int($qty))
        {
            throw new \Exception("qty must be an integer.");
        }
        if ($qty < 0)
        {
            throw new \Exception("qty must not be negative.");
        }
        $arg = array('sku' => $sku,
                     'qty' => $qty);
        return $this->sendRPC('set-stock-quantity', $arg);
    }

    public function setStockQuantities($skuToQtyArray)
    {
        if (!is_array($skuToQtyArray))
        {
            throw new \Exception("skuToQtyArray must be an array.");
        }
        $skus = array_keys($skuToQtyArray);
        if (!array_reduce($skus, 'F1\SellerClient\\checkIntArray', true))
        {
            throw new \Exception("All keys in skuToQtyArray must be integers.");
        }
        $qtys = array_values($skuToQtyArray);
        if (!array_reduce($qtys, 'F1\SellerClient\\checkNonNegIntArray', true))
        {
            throw new \Exception(
                "All values in skuToQtyArray must be non-negative integers.");
        }
        $arg = array('skus' => $skus,
                     'qtys' => $qtys);
        return $this->sendRPC('set-stock-quantities', $arg);
    }

    public function getAllStockQuantities()
    {
        $ret = $this->sendRPC('get-all-stock-quantities', NULL);
        return $this->translateSkusAndQtysArray($ret);
    }

    public function getSkuInfo($sku)
    {
        if (!is_int($sku))
        {
            throw new \Exception('sku must be an integer.');
        }
        return $this->sendRPC('get-sku-info', $sku);
    }

    public function getAllSkuInfos()
    {
        $ret = $this->sendRPC('get-all-sku-infos', NULL);
        $skus = $ret['skus'];
        $infos = $ret['infos'];
        return array_combine($skus, $infos);
    }

    public function getAggregateSkuInfo()
    {
        return $this->sendRPC('get-aggregate-sku-info', NULL);
    }

    public function getCart($userId)
    {
        if (!is_int($userId))
        {
            throw new \Exception("userId must be an integer.");
        }
        $ret = $this->sendRPC('get-cart', $userId);
        return $this->translateSkusAndQtysArray($ret);
    }

    public function emptyCart($userId)
    {
        if (!is_int($userId))
        {
            throw new \Exception("userId must be an integer.");
        }
        return $this->sendRPC('empty-cart', $userId);
    }

    public function removeSkuFromAllCarts($sku)
    {
        if (!is_int($sku))
        {
            throw new \Exception('sku must be an integer.');
        }
        return $this->sendRPC('remove-sku-from-all-carts', $sku);
    }

    public function markCartAsPurchased($userId)
    {
        if (!is_int($userId))
        {
            throw new \Exception("userId must be an integer.");
        }
        $arg = array('user-id' => $userId,
                     'skus' => array());
        return $this->sendRPC('mark-cart-as-purchased', $arg);
    }

    public function markSomeCartSkusAsPurchased($userId, $skus)
    {
        if (!is_int($userId))
        {
            throw new \Exception("userId must be an integer.");
        }
        if (!is_array($skus) or empty($skus) or !is_int($skus[0]))
        {
            throw new \Exception(
                "skus must be a non-empty one-dimensional array of integers.");
        }
        $arg = array('user-id' => $userId,
                     'skus' => $skus);
        return $this->sendRPC('mark-cart-as-purchased', $arg);
    }

    public function getSkuPurchaseLimit($sku)
    {
        if (!is_int($sku))
        {
            throw new \Exception("sku must be an integer.");
        }
        return $this->sendRPC('get-sku-purchase-limit', $sku);
    }

    public function setSkuPurchaseLimit($sku, $qty)
    {
        if (!is_int($sku))
        {
            throw new \Exception("sku must be an integer.");
        }
        if (!is_int($qty))
        {
            throw new \Exception("qty must be an integer.");
        }
        if ($qty < 0)
        {
            throw new \Exception("qty must not be negative.");
        }
        $arg = array('sku' => $sku,
                     'qty' => $qty);
        return $this->sendRPC('set-sku-purchase-limit', $arg);
    }

    public function getAllSkuPurchaseLimits()
    {
        $ret = $this->sendRPC('get-all-sku-purchase-limits', NULL);
        return $this->translateSkusAndQtysArray($ret);
    }

    public function setSkuPurchaseLimits($skuToLimitArray)
    {
        if (!is_array($skuToLimitArray))
        {
            throw new \Exception("skuToLimitArray must be an array.");
        }
        $skus = array_keys($skuToLimitArray);
        if (!array_reduce($skus, 'F1\SellerClient\\checkIntArray', true))
        {
            throw new \Exception(
                "All keys in skuToLimitArray must be integers.");
        }
        $limits = array_values($skuToLimitArray);
        if (!array_reduce($limits, 'F1\SellerClient\\checkNonNegIntArray',
                          true))
        {
            throw new \Exception(
                "All values in skuToLimitArray must be non-negative integers.");
        }
        $arg = array('skus' => $skus,
                     'qtys' => $limits);
        return $this->sendRPC('set-sku-purchase-limits', $arg);
    }

    public function resetPurchaseHistory($userId)
    {
        if (!is_int($userId))
        {
            throw new \Exception("userId must be an integer.");
        }
        return $this->sendRPC('reset-purchase-history', $userId);
    }

    public function resetAllPurchaseHistories()
    {
        return $this->sendRPC('reset-all-purchase-histories', NULL);
    }

    public function getCartDurationSeconds()
    {
        return $this->sendRPC('get-cart-duration-seconds', NULL);
    }

    public function setCartDurationSeconds($seconds)
    {
        if (!is_int($seconds))
        {
            throw new \Exception("seconds must be an integer.");
        }
        return $this->sendRPC('set-cart-duration-seconds', $seconds);
    }

    public function getCartSecondsRemaining($userId)
    {
        if (!is_int($userId))
        {
            throw new \Exception("userId must be an integer.");
        }
        return $this->sendRPC('get-cart-seconds-remaining', $userId);
    }

    public function resetCartStartTime($userId)
    {
        if (!is_int($userId))
        {
            throw new \Exception("userId must be an integer.");
        }
        return $this->sendRPC('reset-cart-start-time', $userId);
    }

    public function resetAllCartStartTimes()
    {
        return $this->sendRPC('reset-all-cart-start-times', NULL);
    }

    public function generateAuthToken($userId, $tokenDurationMinutes)
    {
        if (!is_int($userId))
        {
            throw new \Exception("userId must be an integer.");
        }
        if (!is_int($tokenDurationMinutes))
        {
            throw new \Exception("tokenDurationMinutes must be an integer.");
        }
        $arg = array('user-id' => $userId,
                     'token-duration-mins' => $tokenDurationMinutes);
        return $this->sendRPC('generate-auth-token', $arg);
    }

    public function sendEventToShopper($userId, $eventName, $eventString)
    {
        if (!is_int($userId))
        {
            throw new \Exception("userId must be an integer.");
        }
        if (!is_string($eventName))
        {
            throw new \Exception("eventName must be a string.");
        }
        if (!is_string($eventString))
        {
            throw new \Exception("eventString must be a string.");
        }
        $arg = array('user-id' => $userId,
                     'event-name' => $eventName,
                     'event-data' => $eventString);
        return $this->sendRPC('send-event-to-shopper', $arg);
    }

    public function sendEventToAllShoppers($eventName, $eventString)
    {
        if (!is_string($eventName))
        {
            throw new \Exception("eventName must be a string.");
        }
        if (!is_string($eventString))
        {
            throw new \Exception("eventString must be a string.");
        }
        $arg = array('event-name' => $eventName,
                     'event-data' => $eventString);
        return $this->sendRPC('send-event-to-all-shoppers', $arg);
    }

    private function sendRPC($fnName, $fnArg)
    {
        if (!is_string($fnName))
        {
            throw new \Exception("fnName must be a string.");
        }
        $data = array('app-id' => $this->appId,
                      'app-secret' => $this->appSecret,
                      'fn-name' => $fnName,
                      'fn-arg' => $fnArg);
        $options = array(
            'http' => array( // use key 'http' even for https://...
                'header'  => "Content-type: application/json",
                'method'  => 'POST',
                'content' => json_encode($data)
                )
            );
        $context  = stream_context_create($options);
        $getGwsUrl = 'https://gws.f1shoppingcart.com/' . $this->appId;
        $gws = json_decode(file_get_contents($getGwsUrl));
        foreach ($gws as $gw) {
            $proxyUrl = preg_replace('/^ws/', 'http', $gw) . "/seller-proxy";
            try {
                $result = file_get_contents($proxyUrl, false, $context);
                if ($result === FALSE) {
                    throw new \Exception("RPC call failed.");
                } else {
                    $result = json_decode($result, true);
                    if ($result['result'] === NULL)
                    {
                        throw new \Exception($result['error']);
                    } else
                    {
                        return $result['result'];
                    }
                }
            } catch (\Exception $e) {
            }
        }
        throw new \Exception("No F1 Gateways could be reached.");
    }

    private function translateSkusAndQtysArray($skusAndQtysArray)
    {
        $skus = $skusAndQtysArray['skus'];
        $qtys = $skusAndQtysArray['qtys'];
        return array_combine($skus, $qtys);
    }
}
