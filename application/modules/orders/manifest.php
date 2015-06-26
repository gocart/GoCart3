<?php

$routes[] = ['GET|POST', '/admin/orders/form/[i:id]?', 'GoCart\Controller\AdminOrders#form'];
$routes[] = ['GET|POST', '/admin/orders/export', 'GoCart\Controller\AdminOrders#export'];
$routes[] = ['GET|POST', '/admin/orders/bulk_delete', 'GoCart\Controller\AdminOrders#bulk_delete'];
$routes[] = ['GET|POST', '/admin/orders/order/[:orderNumber]', 'GoCart\Controller\AdminOrders#order'];
$routes[] = ['GET|POST', '/admin/orders/sendNotification/[:orderNumber]', 'GoCart\Controller\AdminOrders#sendNotification'];
$routes[] = ['GET|POST', '/admin/orders/packing_slip/[:orderNumber]', 'GoCart\Controller\AdminOrders#packing_slip'];
$routes[] = ['GET|POST', '/admin/orders/edit_status', 'GoCart\Controller\AdminOrders#edit_status'];
$routes[] = ['GET|POST', '/admin/orders/delete/[i:id]', 'GoCart\Controller\AdminOrders#delete'];
$routes[] = ['GET|POST', '/admin/orders', 'GoCart\Controller\AdminOrders#index'];
$routes[] = ['GET|POST', '/admin/orders/index/[:orderBy]?/[:orderDir]?/[:code]?/[i:page]?', 'GoCart\Controller\AdminOrders#index'];
$routes[] = ['GET|POST', '/digital-products/download/[i:fileId]/[i:orderId]', 'GoCart\Controller\DigitalProducts#download'];