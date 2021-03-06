# F1 Seller Client

* [About](#about)
* [Installation](#installation)
* [SellerClient Construction](#sellerclient-construction)
* [SellerClient Methods](#sellerclient-methods)
* [Events](#events)
* [Authentication Integration](#authentication-integration)
* [License](#license)

## About
This is the PHP seller client package for the F1 Shopping Cart service.

This package should be installed on the seller web server and enables seller-side
operations such as getting and setting stock levels, purchase limits, etc.

## Installation
The preferred method of installation is via
[Packagist](https://packagist.org/packages/f1/seller-client) and
[Composer](https://getcomposer.org/).
Run the following command to install the package and add it as a requirement
to your project's `composer.json`:

```bash
composer require f1/seller-client
```

## SellerClient Construction
All interactions between the seller server and the F1 Shopping Cart
service happen via the SellerClient object. Constructing a SellerClient
requires an App Id and an App Secret, which can be obtained from F1
Customer Support. Both the App Id and App Secret are strings.
The App Secret should be kept confidential and stored securely. Here is
an example of constructing a SellerClient using environment variables:

```php
$appId = getenv('F1_APP_ID');
$appSecret = getenv('F1_APP_SECRET');
$client = new SellerClient($appId, $appSecret);
```

## SellerClient Methods
* **Stock Methods**
  * [getStockQuantity](#getstockquantity)
  * [setStockQuantity](#sqetstockquantity)
  * [setStockQuantities](#setstockquantities)
  * [getAllStockQuantities](#getallstockquantities)
* **SKU Info Methods**
  * [getSkuInfo](#getskuinfo)
  * [getAllSkuInfos](#getallskuinfos)
  * [getAggregateSkuInfo](#getaggregateskuinfo)
* **Cart Methods**
  * [getCart](#getcart)
  * [emptyCart](#emptycart)
  * [removeSkuFromAllCarts](#removeskufromallcarts)
  * [markCartAsPurchased](#markcartaspurchased)
  * [markSomeCartSkusAsPurchased](#marksomecartskusaspurchased)
* **Purchase Limit Methods**
  * [getSkuPurchaseLimit](#getskupurchaselimit)
  * [setSkuPurchaseLimit](#setskupurchaselimit)
  * [getAllSkuPurchaseLimits](#getallskupurchaselimits)
  * [setSkuPurchaseLimits](#setskupurchaselimits)
  * [resetPurchaseHistory](#resetpurchasehistory)
  * [resetAllPurchaseHistories](#resetallpurchasehistories)
* **Cart Duration Methods**
  * [getCartDurationSeconds](#getcartdurationseconds)
  * [setCartDurationSeconds](#setcartdurationseconds)
  * [getCartSecondsRemaining](#getcartsecondsremaining)
  * [resetCartStartTime](#resetcartstarttime)
  * [resetAllCartStartTimes](#resetallcartstarttimes)
* **Authentication Methods**
  * [generateAuthToken](#generateauthtoken)
* **Event Methods**
  * [sendEventToShopper](#sendeventtoshopper)
  * [sendEventToAllShoppers](#sendeventtoallshoppers)

### getStockQuantity
#### Description
```php
int getStockQuantity(int $sku)
```
Returns the stock quantity of the given SKU.
#### Parameters
* `sku`: An integer representing the SKU.
#### Return Value
The stock quantity as an integer.
#### Examples
```php
$sku = 81;
$qty = $client->getStockQuantity($sku);
```

### setStockQuantity
#### Description
```php
int setStockQuantity(int $sku, int $qty)
```
Sets the stock quantity of a single SKU. Note that this method should not
be used in a loop for bulk quantity updates, as the
[setStockQuantities](#setstockquantities) is much more efficient for
bulk updates.
#### Parameters
* `sku`: An integer representing the SKU.
* `qty`: An integer representing the stock quantity to be set.
#### Return Value
The stock quantity as an integer.
#### Examples
```php
$sku = 81;
$qty = 500;
$setQty = $client->setStockQuantity($sku, $qty);
```

### setStockQuantities
#### Description
```php
bool setStockQuantities(array $skuToQtyArray)
```
Sets the stock quantity of multiple SKUs.
#### Parameters
* `skuToQtyArray`: An array whose keys are SKUs (ints) and whose values
are quantities (ints).
#### Return Value
`TRUE` if the operation succeeded, `FALSE` otherwise.
#### Examples
```php
$sku1 = 81;
$qty1 = 500;
$sku2 = 1234567;
$qty2 = 200;
$skuToQtyArray = array($sku1 => $qty1,
                       $sku2 => $qty2);
$ret = $client->setStockQuantities($skuToQtyArray);
```

### getAllStockQuantities
#### Description
```php
array getAllStockQuantities()
```
Gets the stock quantity all SKUs. Only SKUs with non-zero stock quantities
are returned.
#### Parameters
* None
#### Return Value
An array whose keys are SKUs (ints) and whose values
are the stock quantities (ints).
#### Examples
```php
$ret = $client->getAllStockQuantities();
```

### getSkuInfo
#### Description
```php
array getSkuInfo(int $sku)
```
Gets quantity information about a SKU.
#### Parameters
* sku: An integer representing the SKU.
#### Return Value
An array with three keys:
* `stock`: An integer representing the quantity of this SKU that are in stock.
* `carts`: An integer representing the quantity of this SKU that are currently
in shopping carts.
* `purchased`: An integer representing the quantity of this SKU that have
been purchased since the last purchase history reset. See
[resetPurchaseHistory](#resetpurchasehistory) and
[resetAllPurchaseHistories](#resetallpurchasehistories) for more information
on purchase histories.
#### Examples
```php
$sku = 81;
$ret = $client->getSkuInfo($sku);
```

### getAllSkuInfos
#### Description
```php
array getAllSkuInfos()
```
Gets quantity information about all SKUs.
#### Parameters
* None
#### Return Value
An array whose keys are integers representing SKUs and whose values
are SKU info arrays, which have three keys:
* `stock`: An integer representing the quantity of this SKU that are currently
in stock.
* `carts`: An integer representing the quantity of this SKU that are currently
in shopping carts.
* `purchased`: An integer representing the quantity of this SKU that have
been purchased since the last purchase history reset. See
[resetPurchaseHistory](#resetpurchasehistory) and
[resetAllPurchaseHistories](#resetallpurchasehistories) for more information
on purchase histories.
#### Examples
```php
$ret = $client->getAllSkuInfos();
```

### getAggregateSkuInfo
#### Description
```php
array getAggregateSkuInfo()
```
Gets aggregate quantity information all active SKUs.
#### Parameters
* None
#### Return Value
An array with three keys:
* `stock`: An integer representing the total quantity of all SKUs that are
currently in stock.
* `carts`: An integer representing the quantity of all SKUs that are currently
in shopping carts.
* `purchased`: An integer representing the quantity of all SKUs that have
been purchased since the last purchase history reset. See
[resetPurchaseHistory](#resetpurchasehistory) and
[resetAllPurchaseHistories](#resetallpurchasehistories) for more information
on purchase histories.
#### Examples
```php
$ret = $client->getAggregateSkuInfo();
```

### getCart
#### Description
```php
array getCart(int $userId)
```
Gets the contents of a shopper's cart.
#### Parameters
* `userId`: An integer representing the shopper's user id.
#### Return Value
An array whose keys are SKUs (integers) and whose values
are the quantity of that SKU in the shopper's cart (integers).
#### Examples
```php
$userId = 2435;
$ret = $client->getCart($userId);
```

### emptyCart
#### Description
```php
bool emptyCart(int $userId)
```
Empties the shopper's cart, without marking any of the items as purchased.
#### Parameters
* `userId`: An integer representing the shopper's user id.
#### Return Value
`TRUE` if the operation succeeded, `FALSE` otherwise.
#### Examples
```php
$userId = 2435;
$ret = $client->emptyCart($userId);
```

### removeSkuFromAllCarts
#### Description
```php
bool removeSkuFromAllCarts(int $sku)
```
Removes all items of the given SKU from all users' carts and returns them
to stock.
#### Parameters
* `sku`: An integer representing the SKU.
#### Return Value
`TRUE` if the operation succeeded, `FALSE` otherwise.
#### Examples
```php
$sku = 81;
$ret = $client->removeSkuFromAllCarts($sku);
```

### markCartAsPurchased
#### Description
```php
bool markCartAsPurchased(int $userId)
```
Marks all the items in a shopper's cart as having been purchased and
removes them from the cart.
#### Parameters
* `userId`: An integer representing the shopper's user id.
#### Return Value
`TRUE` if the operation succeeded, `FALSE` otherwise.
#### Examples
```php
$userId = 2435;
$ret = $client->markCartAsPurchased($userId);
```

### markSomeCartSkusAsPurchased
#### Description
```php
bool markSomeCartSkusAsPurchased(int $userId, array $skus)
```
Marks the specified SKUs in a shopper's cart as having been purchased and
removes them from the cart. Any other SKUs in the cart are unaffected.
#### Parameters
* `userId`: An integer representing the shopper's user id.
* `skus`: A non-empty, one-dimensional of integers representing the SKUs to be
marked as purchased.
#### Return Value
`TRUE` if the operation succeeded, `FALSE` otherwise.
#### Examples
```php
$userId = 2435;
$skus = array(42, 99, 7, 1);
$ret = $client->markSomeCartSkusAsPurchased($userId, $skus);
```

### getSkuPurchaseLimit
#### Description
```php
int getSkuPurchaseLimit(int $sku)
```
Gets the current purchase limit for the given SKU. A shopper may only
have purchased (or have in their cart) this quantity of the given SKU.
Note that a purchase limit of zero means that there is no limit, i.e.
shoppers may purchase an unlimited amount of the given SKU (subject
to stock availability).
To allow shoppers to purchase more of SKUs that are limited, their purchase
history must be reset. See also [resetPurchaseHistory](#resetpurchasehistory) and
[resetAllPurchaseHistories](#resetallpurchasehistories).
#### Parameters
* `sku`: An integer representing the SKU.
#### Return Value
An integer representing the current purchase limit for the given SKU.
#### Examples
```php
$sku = 81;
$limit = $client->getSkuPurchaseLimit($sku);
```

### setSkuPurchaseLimit
#### Description
```php
bool setSkuPurchaseLimit(int $sku, int $qty)
```
Sets the current purchase limit for the given SKU. A shopper may only
have purchased (or have in their cart) this quantity of the given SKU.
Note that a purchase limit of zero means that there is no limit, i.e.
shoppers may purchase an unlimited amount of the given SKU (subject
to stock availability).
To allow shoppers to purchase more units of SKUs that are limited, their purchase
history must be reset. See also [resetPurchaseHistory](#resetpurchasehistory) and
[resetAllPurchaseHistories](#resetallpurchasehistories). If you want
to set the purchase limit for many SKUs at once, use the related
[setSkuPurchaseLimits](#setskupurchaselimits) instead of this method.
#### Parameters
* `sku`: An integer representing the SKU.
* `qty`: An integer representing the quantity of this SKU that may be
purchased.
#### Return Value
`TRUE` if the operation succeeded, `FALSE` otherwise.
#### Examples
```php
$sku = 81;
$limitQty = 5;
$ret = $client->setSkuPurchaseLimit($sku, $limitQty);
```

### getAllSkuPurchaseLimits
#### Description
```php
array getAllSkuPurchaseLimits()
```
Gets the current purchase limits for all SKUs. Only SKUs with non-zero
limits are returned. Note that a purchase limit of zero means that
there is no limit, i.e. shoppers may purchase an unlimited amount of
the given SKU (subject to stock availability).
To allow shoppers to purchase more units of SKUs that are limited, their purchase
history must be reset. See also [resetPurchaseHistory](#resetpurchasehistory) and
[resetAllPurchaseHistories](#resetallpurchasehistories).
#### Parameters
* None
#### Return Value
An array whose keys are SKUs (integers) and whose values
are the purchase limits (integers) for the given SKUs.
#### Examples
```php
$ret = $client->getAllSkuPurchaseLimits();
```

### setSkuPurchaseLimits
#### Description
```php
bool setSkuPurchaseLimits(array $skuToLimitArray)
```
Sets the current purchase limit for multiple SKUs.
Note that a purchase limit of zero means that there is no limit, i.e.
shoppers may purchase an unlimited amount of the given SKU (subject
to stock availability).
To allow shoppers to purchase more units of SKUs that are limited, their purchase
history must be reset. See also [resetPurchaseHistory](#resetpurchasehistory) and
[resetAllPurchaseHistories](#resetallpurchasehistories).
#### Parameters
* `skuToLimitArray`: An array whose keys are SKUs (ints) and whose values
are limit quantities (ints).
#### Return Value
`TRUE` if the operation succeeded, `FALSE` otherwise.
#### Examples
```php
$sku1 = 81;
$limit1 = 500;
$sku2 = 1234567;
$limit2 = 200;
$skuToLimitArray = array($sku1 => $limit1,
                       $sku2 => $limit2);
$ret = $client->setSkuPurchaseLimits($skuToLimitArray);
```

### resetPurchaseHistory
#### Description
```php
bool resetPurchaseHistory(int $userId)
```
Resets the shopper's purchase history for purposes of purchase limits.
After this method is called, the given shopper will be able to purchase up
to the current purchase limit for any SKU, regardless of past purchases.
#### Parameters
* `userId`: An integer representing the shopper's user id.
#### Return Value
`TRUE` if the operation succeeded, `FALSE` otherwise.
#### Examples
```php
$userId = 2435;
$ret = $client->resetPurchaseHistory($userId);
```

### resetAllPurchaseHistories
#### Description
```php
bool resetAllPurchaseHistories()
```
Resets all shoppers' purchase histories for purposes of purchase limits.
After this method is called, all shoppers will be able to purchase up
to the current purchase limit for any SKU, regardless of past purchases.
#### Parameters
* None
#### Return Value
`TRUE` if the operation succeeded, `FALSE` otherwise.
#### Examples
```php
$ret = $client->resetAllPurchaseHistories();
```

### getCartDurationSeconds
#### Description
```php
int getCartDurationSeconds()
```
Returns the current cart duration in seconds. Carts that have existed
for longer than the current cart duration will be automatically emptied.
The cart duration is counted from the time the first item is added to the
cart until the time that the cart is marked as purchased or is otherwise
emptied. See also [resetCartStartTime](#resetcartstarttime) and
[resetAllCartStartTimes](#resetallcartstarttimes).
#### Parameters
* None
#### Return Value
An integer representing the current cart duration in seconds.
#### Examples
```php
$durationSeconds = $client->getCartDurationSeconds();
```

### setCartDurationSeconds
#### Description
```php
bool setCartDurationSeconds(int $seconds)
```
Sets the current cart duration in seconds. Carts that have existed
for longer than the current cart duration will be automatically emptied.
The cart duration is counted from the time the first item is added to the
cart until the time that the cart is marked as purchased or is otherwise
emptied. See also [resetCartStartTime](#resetcartstarttime) and
[resetAllCartStartTimes](#resetallcartstarttimes).
#### Parameters
* `seconds`: An integer representing the desired cart duration in seconds.
#### Return Value
`TRUE` if the operation succeeded, `FALSE` otherwise.
#### Examples
```php
$durationSeconds = 14400 // 4 hours
$ret = $client->setCartDurationSeconds($durationSeconds);
```

### getCartSecondsRemaining
#### Description
```php
int getCartSecondsRemaining(int $userId)
```
Returns the number of seconds remaining before the given shopper's
cart is automatically emptied. See also
[getCartDurationSeconds](#getcartdurationseconds) and
[setCartDurationSeconds](#setcartdurationseconds).
#### Parameters
* `userId`: An integer representing the shopper's user id.
#### Return Value
An integer representing the number of seconds remaining before the
given shopper's cart is automatically emptied.
#### Examples
```php
$userId = 2435;
$secondsRemaining = $client->getCartinutesRemaining($userId);
```

### resetCartStartTime
#### Description
```php
bool resetCartStartTime(int $userId)
```
Resets the start time of the given user's cart. The F1 service automatically
expires (empties) carts that have existed for longer than the current cart
duration (see [setCartDurationSeconds](#setcartdurationseconds)).
Normally, the cart duration is counted from the time the first item is added
to the cart until the time that the cart is marked as purchased or is otherwise
emptied. This method resets the shopper's cart start time, giving that
shopper a new period of time to purchase the items in their cart.
#### Parameters
* `userId`: An integer representing the shopper's user id.
#### Return Value
`TRUE` if the operation succeeded, `FALSE` otherwise.
#### Examples
```php
$userId = 2435;
$ret = $client->resetCartStartTime($userId);
```

### resetAllCartStartTimes
#### Description
```php
bool resetAllCartStartTimes()
```
Resets the start time of all users' carts. The F1 service automatically
expires (empties) carts that have existed for longer than the current cart
duration (see [setCartDurationSeconds](#setcartdurationseconds)).
Normally, the cart duration is counted from the time the first item is added
to the cart until the time that the cart is marked as purchased or is otherwise
emptied. This method resets all shoppers' cart start times, giving all
shoppers a new period of time to purchase the items in their cart.
#### Parameters
* None
#### Return Value
`TRUE` if the operation succeeded, `FALSE` otherwise.
#### Examples
```php
$ret = $client->resetAllCartStartTimes();
```

### generateAuthToken
#### Description
```php
string generateAuthToken(int $userId, $tokenDurationMinutes)
```
Returns an authentication token for the given shopper. The token will
be valid for the number of minutes specified by $tokenDurationMinutes.
This method should be called by the authentication handler. See the
[Authentication Integration](#authentication-integration) section for
more information about authentication.
#### Parameters
* `userId`: An integer representing the shopper's user id.
* `tokenDurationMinutes`: An integer representing the number of minutes
for which the token will be valid.
#### Return Value
A string token allowing the shopper to authenticate directly to the
F1 Shopping Cart service.
#### Examples
```php
$userId = Auth::id();
$tokenDurationMins = 240;
$token = $client->generateAuthToken($userId, $tokenDurationMins);
```

### sendEventToShopper
#### Description
```php
bool sendEventToShopper(int $userId, string $eventName, string $eventString)
```
Sends a custom event to a specific shopper's browser connections.
Custom events have an event name and an event value string. The event name and event
value string are arbitrary and can be any string value.
If the shopper has multiple browser windows or tabs connected, all of
them will recieve the event. If the shopper is not currently connected,
the event is dropped. See the [Events](#events) section for more information.
See also [sendEventToAllShoppers](#sendeventtoallshoppers)
#### Parameters
* `userId`: An integer representing the shopper's user id.
* `eventName`: A string representing name of the event
* `eventString`: A string representing the value of the event.
#### Return Value
`TRUE` if the operation succeeded, `FALSE` otherwise.
#### Examples
```php
$userId = 2435;
$eventName = 'direct-message'
$eventString = 'The item you've been waiting for just arrived in stock';
$ret = $client->sendEventToShopper($userId, $eventName, $eventString);
```

### sendEventToAllShoppers
#### Description
```php
bool sendEventToAllShoppers(string $eventName, string $eventString)
```
Sends a custom event to all shoppers' connections. Custom events
have an event name and an event value string. The event name and event
value string are arbitrary and can be any string value. See the
[Events](#events) section for more information. See also
[sendEventToShopper](#sendeventtoshopper)
#### Parameters
* `eventName`: A string representing name of the event
* `eventString`: A string representing the value of the event.
#### Return Value
`TRUE` if the operation succeeded, `FALSE` otherwise.
#### Examples
```php
$eventName = 'start-countdown'
$eventString = '20'
$ret = $client->sendEventToAllShoppers($eventName, $eventString);
```

## Events
Events are sent to the shopper's browser connections. The
[ShopperClient](shopper.md/#shopperclient-construction) handles
events by binding handlers to each event type.
F1 Shopping Cart has two categories of events:
* Built-in events
* Custom events

Built-in events are sent automatically by the F1 Shopping Cart service
when certain things happen. See the
[ShopperClient's eveent documentation](shopper.md/#events)
for the list of built-in events. Custom events are arbitrary strings sent to
shoppers. Custom events have a name and a value, which are both strings.
See [sendEventToShopper](#sendeventtoshopper) and
[sendEventToAllShoppers](#sendeventtoallshoppers) for more information.


## Authentication Integration

The F1 Shopping Cart service cooperates with the seller server to
authenticate shoppers. This is done via an HTTP GET request from
the shopper's browser client.
The seller server must provide a route that accepts the request only if the
shopper is authenticated. It should then call the generateAuthToken method
on the SellerClient an authentication token. Finally, it should return the shopper's
user id and the authentication token to the shopper in a JSON-encoded array.
Here is an example using Laravel 4:

```php
Route::get('get_f1_auth_token', ['before' => 'auth', function()
{
    $userId = Auth::id();  // Replace this line for other auth systems
    $tokenDurationMins = 240;
    $appId = getenv('F1_APP_ID');
    $appSecret = getenv('F1_APP_SECRET');
    $client = new SellerClient($appId, $appSecret);
    $token = $client->generateAuthToken($userId, $tokenDurationMins);
    $ret = array('userId' => $userId,
                 'token' => $token);
    return json_encode($ret);
}]);
```

## License

Copyright (c) 2017-2018 Deer Creek Labs, LLC

Distributed under the Apache Software License, Version 2.0
http://www.apache.org/licenses/LICENSE-2.0.txt
