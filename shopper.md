# F1 Shopper Client

* [About](#about)
* [Installation](#installation)
* [ShopperClient Construction](#shopperclient-construction)
* [ShopperClient Methods](#shopperclient-methods)
* [Events](#events)
* [License](#license)

## About

This is the official Javascript shopper client for the F1 Shopping Cart service.

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
var client = new ShopperClient("TEST_APP_ID");
```

## ShopperClient Methods
These are the methods of the ShopperClient object:

### addToCart
#### Description
Add an item to the shopper's cart.
#### Parameters
* sku: (integer) The SKU (Stock Keeping Unit) of the item to be added
* qtyRequested: (integer) The number of items requested
* cb: ([Completion callback](#completion-callbacks))
#### Return Value
void: This is an async method. The specified
[Completion callback](#completion-callbacks) will be called with the
results of the request. See [AddToCartResult](#addtocartresult) for result
details. The application should also bind a handler to the
[CartStateEvent](#cartstateevent) to see any changes to the shopper's
cart, since cart contents may change due to admin actions, cart expiration,
actions in other browser sessions, etc. See
[bindCartStateEvent](#bindCartStateEvent) for more information.
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
void: This is an async method. The specified
[Completion callback](#completion-callbacks) will be called with the
results of the request. See [RemoveFromCartResult](#removefromcartresult)
for result details. The application should also bind a handler to the
[CartStateEvent](#cartstateevent) to see any changes to the shopper's
cart, since cart contents may change due to admin actions, cart expiration,
actions in other browser sessions, etc. See
[bindCartStateEvent](#bindCartStateEvent) for more information.
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

### emptyCart
#### Description
Empty the shopper's cart
#### Parameters
* cb: ([Completion callback](#completion-callbacks))
#### Return Value
void: This is an async method. The specified
[Completion callback](#completion-callbacks) will be called with the
results of the request, which will be true if the operation succeeded
or false if it failed. The application should also bind a handler to the
[CartStateEvent](#cartstateevent) to see any changes to the shopper's
cart, since cart contents may change due to admin actions, cart expiration,
actions in other browser sessions, etc. See
[bindCartStateEvent](#bindCartStateEvent) for more information.
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
void: This is an async method and does not return a value.
The application should bind a handler to the
[CartStateEvent](#cartstateevent) to see the event that will be sent
as a result of calling this method. See
[bindCartStateEvent](#bindCartStateEvent) for more information.
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
void: This is an async method and does not return a value.
The application should bind a handler to the
[StockStateEvent](#stockstateevent) to see the event that will be sent
as a result of calling this method. See
[bindStockStateEvent](#bindStockStateEvent) for more information.
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
void: This method returns null.
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
void: This method returns null.
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
void: This method returns null.
#### Examples
```javascript
client.bindCustomEvent("SomeCustomEvent", function(event) {
  // Do something with the event, which is a string
  console.log("Got SomeCustomEvent: " + event);
});
```






## Completion Callbacks
TBD

## Event Handlers

## Results

### AddToCartResult

### RemoveFromCartResult

## Events

### CartStateEvent
### StockStateEvent
### CustomEvent

F1 Shopping Cart has two types of events:
* Built-in events
* Custom events

Built-in events are sent automatically by the F1 Shopping Cart service
when certain things happen. The current built-in events are:
* **CartStateEvent** - Sent when the state of a shopper's cart changes
for any reason.
* **StockStateEvent** - Sent approximately once per second if there
have been any stock state changes in the last second.

Custom events are arbitrary strings sent by the seller server to
shoppers. Custom events have a name and a value, which are both strings.
See [sendEventToShopper](#sendeventtoshopper) and
[sendEventToAllShoppers](#sendeventtoallshoppers) for more information.


## License

Copyright (c) 2017 Deer Creek Labs, LLC

Distributed under the Apache Software License, Version 2.0
http://www.apache.org/licenses/LICENSE-2.0.txt
