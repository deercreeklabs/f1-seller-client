# F1 Seller Client Package

## About

This is the official PHP seller client package for F1 Shopping Cart.

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

All interactions between the seller server and the F1 service happen
via the SellerClient object. Constructing a SellerClient requires an
App Id and an App Secret, which can be obtained from F1 Customer Support.
Both the App Id and App Secret are strings.
The App Secret should be kept confidential and stored securely. Here is
an example of constructing a SellerClient using environment variables:

```php
$appId = getenv('F1_APP_ID');
$appSecret = getenv('F1_APP_SECRET');
$client = new SellerClient($appId, $appSecret);
```

## SellerClient Methods
These are the methods of the SellerClient object:

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

### getSkuPurchaseLimit
#### Description
```php
int getSkuPurchaseLimit(int $sku)
```
Gets the current purchase limit for the given SKU. A shopper may only
have purchased (or have in their cart) this quantity of the given SKU.
To allow shoppers to purchase more of the given SKU, their purchase
history must be reset. See also [resetPurchaseHistory](#resetPurchaseHistory) and
[resetAllPurchaseHistories](#resetAllPurchaseHistories).
#### Parameters
* sku: An integer representing the SKU.
#### Return Value
An integer representing the current purchase limit for the given SKU.

### setSkuPurchaseLimit
#### Description
```php
bool setSkuPurchaseLimit(int $sku, int $qty)
```
Sets the current purchase limit for the given SKU. A shopper may only
have purchased (or have in their cart) this quantity of the given SKU.
To allow shoppers to purchase more of the given SKU, their purchase
history must be reset. See also [resetPurchaseHistory](#resetPurchaseHistory) and
[resetAllPurchaseHistories](#resetAllPurchaseHistories).
#### Parameters
* sku: An integer representing the SKU.
* qty: An integer representing the quantity of this SKU that may be
purchased.
#### Return Value
TRUE if the operation succeeded, FALSE otherwise.

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
