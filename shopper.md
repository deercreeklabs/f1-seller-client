# F1 Shopper Client

* [About](#about)
* [Installation](#installation)
* [Callbacks](#callbacks)
* [ShopperClient Construction](#shopperclient-construction)
* [ShopperClient Methods](#shopperclient-methods)

* [Results](#results)
* [EventHandlers](#eventhandlers)
* [Events](#events)
* [License](#license)

## About

This is the JavaScript shopper client for the F1 Shopping Cart service.

This client should be included via a <script> tag in the shopper-facing
webpages. It enables shoppers to add items to their carts, remove items
from their carts, etc.

## Installation

Use this tag in the shopper webpage:

```html
<script type="text/javascript" src="https://js.f1shoppingcart.com/v1/shopper.js"></script>
```

## Callbacks
Most methods in this library are asynchronous and use callbacks to convey their
results. All callbacks receive a single object as a parameter. That parameter
has two properties:
* `result`: The result of the method call if it succeeded. Depending on
the method invoked, the result will vary. See the documentation for the
method in question.
* `error`: An error object if the method call failed
Only one of these properties will be non-null. The application should
check which property is set and respond accordingly.


## ShopperClient Construction

All interactions between the shopper web page and the F1 Shopping Cart
service happen via the ShopperClient object. Constructing a ShopperClient
requires an App Id string which can be obtained from F1
Customer Support. For example:
```javascript
var client = new ShopperClient("TestAppId");
```

Optionally, a log level string can be passed as a second parameter to the
constructor. Valid values are (in order of increasing verbosity):
* `"error"`
* `"warn"`
* `"info"`
* `"debug"`

For example:
```javascript
var client = new ShopperClient("TestAppId", "debug");
```
If no log level is passed, the log level defaults to `"info"`.

The F1 Shopping Cart client library loads asynchronously, so the application
needs to wait until the F1 library is fully loaded before constructing the
client and calling methods.

The best way to do this is via the window.f1OnLoadedCallback. If this callback
is defined, the client library will call it when it is done loading. The
callback should accept a single response object as an argument. This object
has two properties:
* `result`: If the library loaded successfully, this property will
contain the string 'F1 library is loaded.'
* `error`: If the library failed to load successfully, this property will
contain an error object explaining the failure.

Only one of these properties will be non-null. The application should
check which property is set and respond accordingly.

Note that the window.f1OnLoadedCallback must be defined before the F1 script tag
is loaded. Here is an example of proper loading and client construction using the
window.f1OnLoadedCallback:

```
<script type="text/javascript">
    window.f1OnLoadedCallback = function(rsp) {
        if (rsp.error) {
            console.error('F1 script failed to load: %s', rsp.error);
        } else {
            console.log(rsp.result);
            var client = new ShopperClient("INTERNAL_TEST_APP_ID");
            // Do something with the client here ...
        }
    };
</script>

<script type="text/javascript" src="https://js.f1shoppingcart.com/v1/shopper.js">
</script>
```

## Authentication
Before the client can do useful work, it must authenticate itself via the
[logIn](#login) method. If there are too many connections open for this user,
authentication may fail. Check the result of the [logIn](#login) method's callback
to ensure that the client authenticated properly.

## ShopperClient Methods

* [logIn](#login)
* [addToCart](#addtocart)
* [removeFromCart](#removefromcart)
* [setCartQuantity](#setcartquantity)
* [emptyCart](#emptycart)
* [getCartSecondsRemaining](#getcartsecondsremaining)
* [getCartState](#getcartstate)
* [getInStockState](#getinstockstate)
* [bindCartStateEvent](#bindcartstateevent)
* [bindInStockStateEvent](#bindinstockstateevent)
* [bindCartExpiredEvent](#bindcartexpiredevent)
* [bindCustomEvent](#bindcustomevent)


### logIn
#### Description
Log in the user to the F1 Shopping Cart service.
#### Parameters
* `cb`: ([Callback](#callbacks))
#### Return Value
This is an async method. The specified [callback](#callbacks) will be called
with the result of the request. If authentication succeeds, the callback
argument's `result` property will be set to string with a success message.
If authentication fails, the callback argument's `error` property will be
set to an Error object explaining the failure. Authentication may fail if
the shopper already has too many connections open to the F1 service.
Thus, it is important to call this method and check its
result before attempting to call other methods.
#### Examples
```javascript
client.logIn(function(rsp) {
    if (rsp.error) {
        // Do something with the rsp.error
        console.error("logIn failed: " + rsp.error);
    } else {
        // Client is ready, call additional methods now
        console.log(rsp.result);
    }
});
```

### addToCart
#### Description
Add an item to the shopper's cart.
#### Parameters
* `sku`: (integer) The SKU (Stock Keeping Unit) of the item to be added
* `qtyRequested`: (integer) The number of items requested. Note that fewer
items may actually be added, due to stock availability or purchase limits.
See the return value for details on the quantity in the cart after
the operation completes.
* `cb`: ([Callback](#callbacks))
#### Return Value
This is an async method. The specified
[callback](#callbacks) will be called with the
results of the request. See [AddToCartResult](#addtocartresult) for result
details. The web application should also bind a handler to the
[CartStateEvent](#cartstateevent) to see any changes to the shopper's
cart, since cart contents may change due to admin actions, cart expiration,
actions in other browser sessions, etc. See
[bindCartStateEvent](#bindcartstateevent) for more information.
#### Examples
```javascript
var sku = 81;
var qtyRequested = 4;
client.addToCart(sku, qtyRequested, function(rsp) {
  if (rsp.error) {
    // Do something with the rsp.error
    console.error("addToCart failed. Error: " + rsp.error);
  } else {
    var result = rsp.result;
    console.log("Quantity requested: " + qtyRequested);
    console.log("Quantity added to cart: " + result.qtyAdded);
    console.log("Quantity of this SKU currently in cart: " + result.cartQty);
    console.log("Why: " + result.why);
  }
});
```

### removeFromCart
#### Description
Remove item(s) from the shopper's cart
#### Parameters
* `sku`: (integer) The SKU (Stock Keeping Unit) of the item to be removed
* `qty`: (integer) The number of items to be removed
* `cb`: ([Callback](#callbacks))
#### Return Value
This is an async method. The specified
[callback](#callbacks) will be called with the
results of the request. See [RemoveFromCartResult](#removefromcartresult)
for result details. The web application should also bind a handler to the
[CartStateEvent](#cartstateevent) to see any changes to the shopper's
cart, since cart contents may change due to admin actions, cart expiration,
actions in other browser sessions, etc. See
[bindCartStateEvent](#bindcartstateevent) for more information.
#### Examples
```javascript
var sku = 81;
var qtyToRemove = 4;
client.removeFromCart(sku, qtyToRemove,  function(rsp) {
  if (rsp.error) {
    // Do something with the rsp.error
    console.error("removeFromCart failed. Error: " + rsp.error);
  } else {
    var result = rsp.result;
    console.log("Quantity to be removed: " + qtyToRemove);
    console.log("Quantity actually removed: " + result.qtyRemoved);
    console.log("Quantity of this SKU remaining in cart: " + result.cartQty);
  }
});
```

### setCartQuantity
#### Description
Set the quantity of a SKU in the shopper's cart.
#### Parameters
* `sku`: (integer) The SKU (Stock Keeping Unit) of the item
* `qty`: (integer) The desired quantity. Note that a lower quantity
may actually be set in the cart, due to stock availability or purchase limits.
See the return value for details on the quantity in the cart after the
operation completes.
* `cb`: ([Callback](#callbacks))
#### Return Value
This is an async method. The specified
[callback](#callbacks) will be called with the
results of the request. See [SetCartQuantityResult](#setcartquantityresult)
for result details. The web application should also bind a handler to the
[CartStateEvent](#cartstateevent) to see any changes to the shopper's
cart, since cart contents may change due to admin actions, cart expiration,
actions in other browser sessions, etc. See
[bindCartStateEvent](#bindcartstateevent) for more information.
#### Examples
```javascript
var sku = 81;
var desiredQty = 4;
client.setCartQuantity(sku, desiredQty, function(rsp) {
  if (rsp.error) {
    // Do something with the rsp.error
    console.error("setCartQuantity failed. Error: " + rsp.error);
  } else {
    var result = rsp.result;
    console.log("Desired quantity: " + desiredQty);
    console.log("Quantity of this SKU currently in cart: " + result.cartQty);
    console.log("Why: " + result.why);
  }
});
```

### emptyCart
#### Description
Empty the shopper's cart
#### Parameters
* `cb`: ([Callback](#callbacks))
#### Return Value
This is an async method. The specified
[callback](#callbacks) will be called with the
results of the request. See [EmptyCartResult](#emptycartresult)
for result details. The web application should also bind a handler to the
[CartStateEvent](#cartstateevent) to see any changes to the shopper's
cart, since cart contents may change due to admin actions, cart expiration,
actions in other browser sessions, etc. See
[bindCartStateEvent](#bindcartstateevent) for more information.
#### Examples
```javascript
client.emptyCart(function(rsp) {
  if (rsp.error) {
    // Do something with the rsp.error
    console.error("emptyCart failed due to an error. Error: " + rsp.error);
  } else {
    if (rsp.result) {
      console.log("Cart was successfully emptied.");
    } else {
      console.log("emptyCart failed.");
    }
  }
});
```

### getCartSecondsRemaining
#### Description
Gets the number of seconds remaining before the shopper's
cart is automatically emptied. See also seller client methods
[SellerClient::getCartDurationSeconds](seller.md/#getcartdurationseconds) and
[SellerClient::setCartDurationSeconds](seller.md#setcartdurationseconds)
for more information.
#### Parameters
* `cb`: ([Callback](#callbacks))
#### Return Value
This is an async method. The specified
[callback](#callbacks) will be called with the
results of the request.
See [GetCartSecondsRemainingResult](#getcartsecondsremainingresult)
for result details.
#### Examples
```javascript
client.getCartSecondsRemaining(function(rsp) {
  if (rsp.error) {
    // Do something with the rsp.error
    console.error("getCartSecondsRemaining failed due to an error. Error: "
	  + rsp.error);
  } else {
    console.log("Cart will expire in " + rsp.result + " seconds.");
  }
});
```

### getCartState
#### Description
Request that a [CartStateEvent](#cartstateevent) be sent
#### Parameters
* None
#### Return Value
This is an async method and does not return a value.
The web application should bind a handler to the
[CartStateEvent](#cartstateevent) to see the event that will be sent
as a result of calling this method. See
[bindCartStateEvent](#bindcartstateevent) for more information.
#### Examples
```javascript
client.getCartState();
```

### getInStockState
#### Description
Request that a [InStockStateEvent](#instockstateevent) be sent
#### Parameters
* None
#### Return Value
This is an async method and does not return a value.
The web application should bind a handler to the
[InStockStateEvent](#instockstateevent) to see the event that will be sent
as a result of calling this method. See
[bindInStockStateEvent](#bindinstockstateevent) for more information.
#### Examples
```javascript
client.getInStockState();
```

### bindCartStateEvent
#### Description
Bind a handler for [CartStateEvents](#cartstateevent)
#### Parameters
* `handler`: ([Event Handler](#event-handlers))
#### Return Value
This method returns null.
#### Examples
```javascript
client.bindCartStateEvent(function(event) {
  // Do something with the lineItems
  var numItems = event.lineItems.length;
  console.log("Got CartStateEvent");
  for (var i = 0; i < numItems; i++) {
    var lineItem = event.lineItems[i];
    console.log("SKU: " + lineItem.sku + " Qty: " + lineItem.qty);
  }
});
```

### bindInStockStateEvent
#### Description
Bind a handler for [InStockStateEvents](#instockstateevent)
#### Parameters
* `handler`: ([Event Handler](#event-handlers))
#### Return Value
This method returns null.
#### Examples
```javascript
client.bindInStockStateEvent(function(event) {
  // Do something with the inStockSkus property
  var numSkusInStock = event.inStockSkus.length;
  console.log("Got InStockStateEvent. # of SKUS in stock: " + numSkusInStock);
});
```

### bindCartExpiredEvent
#### Description
Bind a handler for [CartExpiredEvents](#cartexpiredevent)
#### Parameters
* `handler`: ([Event Handler](#event-handlers))
#### Return Value
This method returns null.
#### Examples
```javascript
client.bindCartExpiredEvent(function(event) {
  console.log("Your cart has expired.");
});
```

### bindCustomEvent
#### Description
Bind a handler for [CustomEvents](#customevent)
#### Parameters
* `eventName`: (string) The name of the custom event to be bound
* `handler`: [Event Handler](#event-handlers) Handler for this
custom event.
#### Return Value
This method returns null.
#### Examples
```javascript
client.bindCustomEvent("SomeCustomEvent", function(event) {
  // Do something with the event, which is a string
  console.log("Got SomeCustomEvent: " + event);
});
```

## Results
### AddToCartResult
An AddToCartResult is an object with three properties:
* `qtyAdded`: (integer) The quantity actually added to the cart
* `cartQty`: (integer) The quantity of the specified SKU currently in the cart.
* `why`: (string) Explanation of qtyAdded. "ALL" indicates
that all requested items were added to the cart. "STOCK" indicates that
fewer items were added than requested because of insufficient
stock. "LIMIT" indicates that fewer items were added than requested
because of a purchase limit on the requested item.

### RemoveFromCartResult
A RemoveFromCartResult is an object with two properties:
* `qtyRemoved`: (integer) The quantity removed from the cart
* `cartQty`: (integer) The quantity of the specified SKU remaining in the cart.

### SetCartQuantityResult
A SetCartQuantityResult is an object with two properties:
* `cartQty`: (integer) The quantity of the specified SKU currently in the cart.
* `why`: (string) Explanation of cartQty. "ALL" indicates
that the desired quantity was set. "STOCK" indicates that
the quantity was set to fewer items because of insufficient
stock. "LIMIT" indicates that the quantity was set to fewer items
because of a purchase limit on the requested item.

### EmptyCartResult
An EmptyCartResult is a simple boolean value. It is true if the emptyCart
operation succeeded, and false otherwise.

### GetCartSecondsRemainingResult
A GetCartSecondsRemainingResult is an integer representing the number of
seconds remaining until the user's cart expires and is automatically
emptied.

## Event Handlers
Event handlers are functions that recieve an event as their
only parameter. Depending on the event that was bound, the event will be
one of:
* [CartStateEvent](#cartstateevent)
* [InStockStateEvent](#instockstateevent)
* [CartExpiredEvent](#cartexpiredevent)
* [CustomEvent](#customevent)

## Events
Events are sent from the F1 Shopping Cart service to the shopper's browser.

### CartStateEvent
Sent when the state of the shopper's
cart changes for any reason. This event is an object with one property:
* `lineItems`: An array of [LineItems](#lineitem) representing the items
in the cart.
See [bindCartStateEvent](#bindcartstateevent) for information on
binding a handler to this event.

### InStockStateEvent
Represents the set of SKUs that are currently in stock (quantity is
greater than 0). This event is sent whenever the set of SKUs that are in stock
changes. It will be sent at most once per second. The event is an object
with one property:
* `inStockSkus`: An array of integers representing the SKUs that are
currently in stock.
See [bindInStockStateEvent](#bindinstockstateevent) for information on
binding a handler to this event.

### CartExpiredEvent
Sent when the user's cart has expired and has been automatically
emptied. This event has no content. See
[bindCartExpiredEvent](#bindcartexpiredevent) for information on
binding a handler to this event.

### CustomEvent
Sent by [SellerClient::sendEventToShopper](seller.md/#sendeventtoshopper) or
[SellerClient::sendEventToAllShoppers](seller.md/#sendeventtoallshoppers),
CustomEvents are arbitrary strings. Their semantics are determined by the
application. See [bindCustomEvent](#bindcustomevent) for information on
binding a handlers to custom events.

### LineItem
Each LineItem is an object with two properties:
* `sku`: (integer) SKU
* `qty`: (integer) Quantity

## License

Copyright (c) 2017 Deer Creek Labs, LLC

Distributed under the Apache Software License, Version 2.0
http://www.apache.org/licenses/LICENSE-2.0.txt
