<?php


$routes[] = ['GET|POST', '/admin/customers/export', 'GoCart\Controller\AdminCustomers#export'];
$routes[] = ['GET|POST', '/admin/customers/get_subscriber_list', 'GoCart\Controller\AdminCustomers#getSubscriberList'];
$routes[] = ['GET|POST', '/admin/customers/form/[i:id]?', 'GoCart\Controller\AdminCustomers#form'];
$routes[] = ['GET|POST', '/admin/customers/addresses/[i:id]', 'GoCart\Controller\AdminCustomers#addresses'];
$routes[] = ['GET|POST', '/admin/customers/delete/[i:id]?', 'GoCart\Controller\AdminCustomers#delete'];
$routes[] = ['GET|POST', '/admin/customers/groups', 'GoCart\Controller\AdminCustomers#groups'];
$routes[] = ['GET|POST', '/admin/customers/group_form/[i:id]?', 'GoCart\Controller\AdminCustomers#groupForm'];
$routes[] = ['GET|POST', '/admin/customers/delete_group/[i:id]?', 'GoCart\Controller\AdminCustomers#deleteGroup'];
$routes[] = ['GET|POST', '/admin/customers/address_list/[i:id]?', 'GoCart\Controller\AdminCustomers#addressList'];
$routes[] = ['GET|POST', '/admin/customers/address_form/[i:customer_id]/[i:id]?', 'GoCart\Controller\AdminCustomers#addressForm'];
$routes[] = ['GET|POST', '/admin/customers/delete_address/[i:customer_id]/[i:id]', 'GoCart\Controller\AdminCustomers#deleteAddress'];
$routes[] = ['GET|POST', '/admin/customers/[:order_by]?/[:direction]?/[i:page]?', 'GoCart\Controller\AdminCustomers#index'];

//manifest
$classMap['GoCart\Controller\AdminCustomers'] = 'controllers/AdminCustomers.php';