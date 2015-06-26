<?php
$routes[] = ['GET|POST', '/checkout', 'GoCart\Controller\Checkout#index'];
$routes[] = ['GET|POST', '/checkout/address-list', 'GoCart\Controller\Checkout#addressList'];
$routes[] = ['GET|POST', '/checkout/submit-order', 'GoCart\Controller\Checkout#submitOrder'];
$routes[] = ['GET|POST', '/order-complete/[:order_id]', 'GoCart\Controller\Checkout#orderComplete'];
$routes[] = ['GET|POST', '/order-complete-email/[:order_id]', 'GoCart\Controller\Checkout#orderCompleteEmail'];
$routes[] = ['GET|POST', '/checkout/address', 'GoCart\Controller\Checkout#address'];
$routes[] = ['GET|POST', '/checkout/payment-methods', 'GoCart\Controller\Checkout#paymentMethods'];
$routes[] = ['GET|POST', '/checkout/shipping-methods', 'GoCart\Controller\Checkout#shippingMethods'];
$routes[] = ['GET|POST', '/checkout/set-shipping-method', 'GoCart\Controller\Checkout#setShippingMethod'];