# F1 Shopper Client

* [About](#about)
* [Installation](#installation)
* [ShopperClient Construction](#shopperclient-construction)
* [ShopperClient Methods](#shopperclient-methods)
* [Completion Callbacks](#completion-callbacks)
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

## ShopperClient Construction

All interactions between the shopper web page and the F1 Shopping Cart
service happen via the ShopperClient object. Constructing a ShopperClient
requires an App Id string which can be obtained from F1
Customer Support. For example:
```javascript
var client = new ShopperClient("TestAppId");
```

The F1 Shopping Cart client library loads asynchronously, so the application
needs to wait until the F1 library is fully loaded before constructing the
client and calling methods.

The best way to do this is via the window.f1OnReadyCallback. If this callback
is defined, the client library will call it when it is done loading. The
callback should accept a single response object as an argument. This object
has two properties:
* result: If the library loaded successfully, this property will
contain the string 'F1 client is ready'.
* error: If the library failed to load successfully, this property will
contain an error object explaining the failure.

Only one of these properties will be non-null. The application should
check which property is set and respond accordingly.

Note that the window.f1OnReadyCallback must be defined before the F1 script tag
is loaded. Here is an example of proper loading and client construction using the
window.f1OnReadyCallback:

```
<script type="text/javascript">
    window.f1OnReadyCallback = function(rsp) {
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

## ShopperClient Methods

* [addToCart](#addtocart)
* [removeFromCart](#removefromcart)
* [setCartQuantity](#setcartquantity)
* [emptyCart](#emptycart)
* [getCartState](#getcartstate)
* [getStockState](#getstockstate)
* [bindCartStateEvent](#bindcartstateevent)
* [bindStockStateEvent](#bindstockstateevent)
* [bindCustomEvent](#bindcustomevent)

### addToCart
#### Description
Add an item to the shopper's cart.
#### Parameters
* sku: (integer) The SKU (Stock Keeping Unit) of the item to be added
* qtyRequested: (integer) The number of items requested. Note that fewer
items may actually be added, due to stock availability or purchase limits.
See the return value for details on the quantity in the cart after
the operation completes.
* cb: ([Completion callback](#completion-callbacks))
#### Return Value
This is an async method. The specified
[completion callback](#completion-callbacks) will be called with the
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
* sku: (integer) The SKU (Stock Keeping Unit) of the item to be removed
* qty: (integer) The number of items to be removed
* cb: ([Completion callback](#completion-callbacks))
#### Return Value
This is an async method. The specified
[completion callback](#completion-callbacks) will be called with the
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
* sku: (integer) The SKU (Stock Keeping Unit) of the item
* qty: (integer) The desired quantity. Note that a lower quantity
may actually be set in the cart, due to stock availability or purchase limits.
See the return value for details on the quantity in the cart after the
operation completes.
* cb: ([Completion callback](#completion-callbacks))
#### Return Value
This is an async method. The specified
[completion callback](#completion-callbacks) will be called with the
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
client.setCartQty(sku, desiredQty, function(rsp) {
  if (rsp.error) {
    // Do something with the rsp.error
    console.error("setCartQty failed. Error: " + rsp.error);
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
* cb: ([Completion callback](#completion-callbacks))
#### Return Value
This is an async method. The specified
[completion callback](#completion-callbacks) will be called with the
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

### getStockState
#### Description
Request that a [StockStateEvent](#stockstateevent) be sent
#### Parameters
* None
#### Return Value
This is an async method and does not return a value.
The web application should bind a handler to the
[StockStateEvent](#stockstateevent) to see the event that will be sent
as a result of calling this method. See
[bindStockStateEvent](#bindstockstateevent) for more information.
#### Examples
```javascript
client.getStockState();
```

### bindCartStateEvent
#### Description
Bind a handler for [CartStateEvents](#cartstateevent)
#### Parameters
* handler: ([Event Handler](#event-handlers))
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

### bindStockStateEvent
#### Description
Bind a handler for [StockStateEvents](#stockstateevent)
#### Parameters
* handler: ([Event Handler](#event-handlers))
#### Return Value
This method returns null.
#### Examples
```javascript
client.bindStockStateEvent(function(event) {
  // Do something with the lineItems
  var numItems = event.lineItems.length;
  console.log("Got StockStateEvent");
});
```

### bindCustomEvent
#### Description
Bind a handler for [CustomEvents](#customevent)
#### Parameters
* eventName: (string) The name of the custom event to be bound
* handler: [Event Handler](#event-handlers) Handler for this
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

## Completion Callbacks
Completion callbacks are passed as a parameter to the
[addToCart](#addtocart), [removeFromCart](#removefromcart),
[setCartQuantity](#setcartquantity), and
[emptyCart](#emptycart) methods. Completion callbacks
receive a single [Method Response Object](#method-response-objects)
as a parameter.

### Method Response Objects
The [Method Response Object](#method-response-objects) has two
properties:
* result: The result of the method call if it succeeded. Depending on
the method invoked, the result will one of:
  * [AddToCartResult](#addtocartresult)
  * [RemoveFromCartResult](#removefromcartresult)
  * [SetCartQuantityResult](#setcartquantityresult)
  * [EmptyCartResult](#emptycartresult)
* error: An error object if the method call failed
Only one of these properties will be non-null. The application should
check which property is set and respond accordingly.

## Results
### AddToCartResult
An AddToCartResult is an object with three properties:
* qtyAdded: (integer) The quantity actually added to the cart
* cartQty: (integer) The quantity of the specified SKU currently in the cart.
* why: (string) Explanation of qtyAdded. "ALL" indicates
that all requested items were added to the cart. "STOCK" indicates that
fewer items were added than requested because of insufficient
stock. "LIMIT" indicates that fewer items were added than requested
because of a purchase limit on the requested item.

### RemoveFromCartResult
A RemoveFromCartResult is an object with two properties:
* qtyRemoved: (integer) The quantity removed from the cart
* cartQty: (integer) The quantity of the specified SKU remaining in the cart.

### SetCartQuantityResult
A SetCartQuantityResult is an object with two properties:
* cartQty: (integer) The quantity of the specified SKU currently in the cart.
* why: (string) Explanation of cartQty. "ALL" indicates
that the desired quantity was set. "STOCK" indicates that
the quantity was set to fewer items because of insufficient
stock. "LIMIT" indicates that the quantity was set to fewer items
because of a purchase limit on the requested item.

### EmptyCartResult
An EmptyCartResult is a simple boolean value. It is true if the emptyCart
operation succeeded, and false otherwise.

## Event Handlers
Event handlers are functions that recieve an event as their
only parameter. Depending on the event that was bound, the event will be
one of:
* [CartStateEvent](#cartstateevent)
* [StockStateEvent](#stockstateevent)
* [CustomEvent](#customevent)

## Events
Events are sent from the F1 Shopping Cart service to the shopper's browser.

### CartStateEvent
Sent when the state of the shopper's
cart changes for any reason. This event is an object with one property:
* lineItems: An array of [LineItems](#lineitem) representing the items
in the cart.

### StockStateEvent
Sent approximately once per second if there have been any stock state
changes in the last second. This event is an object with one property:
* lineItems: An array of [LineItems](#lineitem) representing the stock
levels of all SKUs.

### CustomEvent
Sent by [SellerClient::sendEventToShopper](seller.md/#sendeventtoshopper) or
[SellerClient::sendEventToAllShoppers](seller.md/#sendeventtoallshoppers),
CustomEvents are arbitrary strings. Their
semantics are determined by the application.

### LineItem
Each LineItem is an object with two properties:
* sku: (integer) SKU
* qty: (integer) Quantity

## License

Copyright (c) 2017 Deer Creek Labs, LLC

Distributed under the Apache Software License, Version 2.0
http://www.apache.org/licenses/LICENSE-2.0.txt
