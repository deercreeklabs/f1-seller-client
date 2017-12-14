# F1 Seller Client Package

## About

This is the official PHP seller client package for the F1 Shopping Cart service.

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
service happenvia the SellerClient object. Constructing a SellerClient
requires anApp Id and an App Secret, which can be obtained from F1
Customer Support. Both the App Id and App Secret are strings.
The App Secret should be kept confidential and stored securely. Here is
an example of constructing a SellerClient using environment variables:

```php
$appId = getenv('F1_APP_ID');
$appSecret = getenv('F1_APP_SECRET');
$client = new SellerClient($appId, $appSecret);
```

## SellerClient Methods
These are the methods of the SellerClient object:
* **Stock Methods**
  * [getStockQuantity](#getstockquantity)
  * [setStockQuantity](#sqetstockquantity)
  * [setStockQuantities](#setstockquantities)
  * [getAllStockQuantities](#getallstockquantities)
* **Cart Methods**
  * [getCart](#getcart)
  * [emptyCart](#emptycart)
  * [markCartAsPurchased](#markcartaspurchased)
* **Purchase Limit Methods**
  * [getSkuPurchaseLimit](#getskupurchaselimit)
  * [setSkuPurchaseLimit](#setskupurchaselimit)
  * [resetPurchaseHistory](#resetpurchasehistory)
  * [resetAllPurchaseHistories](#resetallpurchasehistories)
* **Cart Duration Methods**
  * [getCartDurationMinutes](#getcartdurationminutes)
  * [setCartDurationMinutes](#setcartdurationminutes)
  * [getCartMinutesRemaining](#getcartminutesremaining)
  * [resetCartStartTime](#resetcartstarttime)
* **Authentication Methods**
  * [generateAuthToken](#generateauthtoken)
  * [getAuthTokenUrl](#getauthtokenurl)
  * [setAuthTokenUrl](#setauthtokenurl)
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
* sku: An integer representing the SKU.
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
Sets the stock quantity of a single SKU.
#### Parameters
* sku: An integer representing the SKU.
* qty: An integer representing the stock quantity to be set.
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
* skuToQtyArray: An array whose keys are SKUs (ints) and whose values
are quantities (ints).
#### Return Value
TRUE if the operation succeeded, FALSE otherwise.
#### Examples
```php
$sku1 = 81;
$qty1 = 500;
$sku2 = 1234567;
$qty3 = 200;
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

### getCart
#### Description
```php
array getCart(int $userId)
```
Gets the contents of a shopper's cart.
#### Parameters
* userId: An integer representing the shopper's user id.
#### Return Value
An array whose keys are SKUs (ints) and whose values
are the quantity of that SKU in the shopper's cart (ints).
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
* userId: An integer representing the shopper's user id.
#### Return Value
TRUE if the operation succeeded, FALSE otherwise.
#### Examples
```php
$userId = 2435;
$ret = $client->emptyCart($userId);
```

### markCartAsPurchased
#### Description
```php
bool markCartAsPurchased(int $userId)
```
Marks the items in the cart as having been purchased and removes them
from the cart
#### Parameters
* userId: An integer representing the shopper's user id.
#### Return Value
TRUE if the operation succeeded, FALSE otherwise.
#### Examples
```php
$userId = 2435;
$ret = $client->markCartAsPurchased($userId);
```

### getSkuPurchaseLimit
#### Description
```php
int getSkuPurchaseLimit(int $sku)
```
Gets the current purchase limit for the given SKU. A shopper may only
have purchased (or have in their cart) this quantity of the given SKU.
To allow shoppers to purchase more of the given SKU, their purchase
history must be reset. See also [resetPurchaseHistory](#resetpurchasehistory) and
[resetAllPurchaseHistories](#resetallpurchasehistories).
#### Parameters
* sku: An integer representing the SKU.
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
To allow shoppers to purchase more of the given SKU, their purchase
history must be reset. See also [resetPurchaseHistory](#resetpurchasehistory) and
[resetAllPurchaseHistories](#resetallpurchasehistories).
#### Parameters
* sku: An integer representing the SKU.
* qty: An integer representing the quantity of this SKU that may be
purchased.
#### Return Value
TRUE if the operation succeeded, FALSE otherwise.
#### Examples
```php
$sku = 81;
$limitQty = 5;
$ret = $client->setSkuPurchaseLimit($sku, $limitQty);
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
* userId: An integer representing the shopper's user id.
#### Return Value
TRUE if the operation succeeded, FALSE otherwise.
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
TRUE if the operation succeeded, FALSE otherwise.
#### Examples
```php
$ret = $client->resetAllPurchaseHistories();
```

### getCartDurationMinutes
#### Description
```php
int getCartDurationMinutes()
```
Returns the current cart duration in minutes. Carts that have existed
for longer than the current cart duration will be automatically emptied.
The cart duration is counted from the time the first item is added to the
cart until the time that the cart is marked as purchased or is otherwise
emptied. See also [resetCartStartTime](#resetcartstarttime).
#### Parameters
* None
#### Return Value
An integer representing the current cart duration in minutes.
#### Examples
```php
$durationMinutes = $client->getCartDurationMinutes();
```

### setCartDurationMinutes
#### Description
```php
bool setCartDurationMinutes(int $minutes)
```
Sets the current cart duration in minutes. Carts that have existed
for longer than the current cart duration will be automatically emptied.
The cart duration is counted from the time the first item is added to the
cart until the time that the cart is marked as purchased or is otherwise
emptied. See also [resetCartStartTime](#resetcartstarttime).
#### Parameters
* minutes: An integer representing the desired cart duration in minutes.
#### Return Value
TRUE if the operation succeeded, FALSE otherwise.
#### Examples
```php
$durationMinutes = 240 // 4 hours
$ret = $client->setCartDurationMinutes($durationMinutes);
```

### getCartMinutesRemaining
#### Description
```php
int getCartMinutesRemaining(int $userId)
```
Returns the number of minutes remaining before the given shopper's
cart is automatically emptied. See also
[getCartDurationMinutes](#getcartdurationminutes) and
[setCartDurationMinutes](#setcartdurationminutes)
#### Parameters
* userId: An integer representing the shopper's user id.
#### Return Value
An integer representing the number of minutes remaining before the
given shopper's cart is automatically emptied.
#### Examples
```php
$userId = 2435;
$minutesRemaining = $client->getCartinutesRemaining($userId);
```

### resetCartStartTime
#### Description
```php
bool resetCartStartTime(int $userId)
```
Resets the start time of the given user's cart. Carts that have existed
for longer than the current cart duration will be automatically emptied.
The cart duration is counted from the time the first item is added to the
cart until the time that the cart is marked as purchased or is otherwise
emptied. This method resets the shopper's cart start time, giving that
shopper a new period of time to purchase the items in their cart. See also
[setCartDurationMinutes](#setcartdurationminutes).
#### Parameters
* userId: An integer representing the shopper's user id.
#### Return Value
TRUE if the operation succeeded, FALSE otherwise.
#### Examples
```php
$userId = 2435;
$ret = $client->resetCartStartTime($userId);
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
* userId: An integer representing the shopper's user id.
* tokenDurationMinutes: An integer representing the number of minutes
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

### getAuthTokenUrl
#### Description
```php
string getAuthTokenUrl()
```
Returns the current URL for getting authentication tokens. See the
[Authentication Integration](#authentication-integration) section for
more information about authentication.
#### Parameters
* None
#### Return Value
The URL for getting authentication tokens.
#### Examples
```php
$tokenUrl = $client->getAuthTokenUrl();
```

### setAuthTokenUrl
#### Description
```php
bool setAuthTokenUrl(string $url)
```
Sets the current URL for getting authentication tokens. See the
[Authentication Integration](#authentication-integration) section for
more information about authentication.
#### Parameters
* None
#### Return Value
TRUE if the operation succeeded, FALSE otherwise.
#### Examples
```php
$tokenUrl = 'https://www.sirshopsalot.com/get_f1_auth_token';
$ret = $client->setAuthTokenUrl($tokenUrl);
```

### sendEventToShopper
#### Description
```php
bool sendEventToShopper(int $userId, string $eventName, string $eventString)
```
Sends a custom event to a specific shopper's browser connections.
 Custom events
have an event name and an event data string. The event name and event
data string are arbitrary and can be any string value.
If the shopper has multiple browser windows or tabs connected, all of
them will recieve the event. If the shopper is not currently connected,
the event is dropped. See the[Events](#events) section for more information.
See also [sendEventToAllShoppers](#sendeventtoallshoppers)
#### Parameters
* userId: An integer representing the shopper's user id.
* eventName: A string representing name of the event
* eventString: A string representing the data of the event.
#### Return Value
TRUE if the operation succeeded, FALSE otherwise.
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
have an event name and an event data string. The event name and event
data string are arbitrary and can be any string value. See the
[Events](#events) section for more information. See also
[sendEventToShopper](#sendeventtoshopper)
#### Parameters
* eventName: A string representing name of the event
* eventString: A string representing the data of the event.
#### Return Value
TRUE if the operation succeeded, FALSE otherwise.
#### Examples
```php
$eventName = 'start-countdown'
$eventString = '20'
$ret = $client->sendEventToAllShoppers($eventName, $eventString);
```




## Events
TBD


## Authentication Integration

The F1 service cooperates with the seller server to authenticate shoppers.
This is done via an HTTP GET request from the shopper's browser client.
The seller server must provide a route that accepts the request only if the
shopper is authenticated. It should then call the F1 server via the Seller
API to get an authentication token. Finally, it should return the shopper's
user id and the authentication token to the shopper. Here is an example
using Laravel 4's built-in Auth::id function:

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

The seller server must set the authentication route in the F1
service before any shoppers can be authenticated. For example, using
the route above, the seller would execute this code on startup:

```php
$routeUrl = 'https://myshoppingsite.com/get_f1_auth_token';
$appId = getenv('F1_APP_ID');
$appSecret = getenv('F1_APP_SECRET');
$client = new SellerClient($appId, $appSecret);
$ret = $client->setAuthTokenUrl($routeUrl);
```

## License

Copyright (c) 2017 Deer Creek Labs, LLC

Distributed under the Apache Software License, Version 2.0
http://www.apache.org/licenses/LICENSE-2.0.txt
