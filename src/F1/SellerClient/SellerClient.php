<?php namespace F1\SellerClient;

function check_int_array($acc, $item)
{
    return $acc and is_int($item);
}

class SellerClient
{
    private $appId;
    private $appSecret;

    public function __construct($appId, $appSecret)
    {
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
        if (!array_reduce($skus, "check_int_array", true))
        {
            throw new \Exception("All keys in skuToQtyArray must be integers.");
        }
        $qtys = array_values($skuToQtyArray);
        if (!array_reduce($qtys, "check_int_array", true))
        {
            throw new \Exception("All values in skuToQtyArray must be integers.");
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

    public function markCartAsPurchased($userId)
    {
        if (!is_int($userId))
        {
            throw new \Exception("userId must be an integer.");
        }
        return $this->sendRPC('mark-cart-as-purchased', $userId);
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
        $arg = array('sku' => $sku,
                     'qty' => $qty);
        return $this->sendRPC('set-sku-purchase-limit', $arg);
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

    public function getCartDurationMinutes()
    {
        return $this->sendRPC('get-cart-duration-mins', NULL);
    }

    public function setCartDurationMinutes($minutes)
    {
        if (!is_int($minutes))
        {
            throw new \Exception("minutes must be an integer.");
        }
        return $this->sendRPC('set-cart-duration-mins', $minutes);
    }

    public function getCartMinutesRemaining($userId)
    {
        if (!is_int($userId))
        {
            throw new \Exception("userId must be an integer.");
        }
        return $this->sendRPC('get-cart-mins-remaining', $userId);
    }

    public function resetCartStartTime($userId)
    {
        if (!is_int($userId))
        {
            throw new \Exception("userId must be an integer.");
        }
        return $this->sendRPC('reset-cart-start-time', $userId);
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

    public function getAuthTokenUrl()
    {
        return $this->sendRPC('get-auth-token-url', NULL);
    }

    public function setAuthTokenUrl($url)
    {
        if (!is_string($url))
        {
            throw new \Exception("url must be a string.");
        }
        return $this->sendRPC('set-auth-token-url', $url);
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
        $getGwsUrl = 'https://gws.f1shoppingcart.com/' . $this->appId;
        $gws = json_decode(file_get_contents($getGwsUrl));
        $gw = $gws[array_rand($gws)];
        $proxyUrl = preg_replace('/^ws/', 'http', $gw) . "/seller-proxy";
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
        $result = file_get_contents($proxyUrl, false, $context);
        if ($result === FALSE)
        {
           throw new \Exception("RPC call to $url failed.");
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
    }

    private function translateSkusAndQtysArray($skusAndQtysArray)
    {
        $skus = $skusAndQtysArray['skus'];
        $qtys = $skusAndQtysArray['qtys'];
        return array_combine($skus, $qtys);
    }
}
