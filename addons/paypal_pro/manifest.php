<?php

$routes[] = ['GET|POST', '/admin/paypal_pro/form', 'GoCart\Controller\AdminPayPalPro#form'];
$routes[] = ['GET|POST', '/admin/paypal_pro/install', 'GoCart\Controller\AdminPayPalPro#install'];
$routes[] = ['GET|POST', '/admin/paypal_pro/uninstall', 'GoCart\Controller\AdminPayPalPro#uninstall'];
$routes[] = ['GET|POST', '/paypal_pro/process-payment', 'GoCart\Controller\PayPalPro#processPayment'];

$paymentModules[] = ['name'=>'PayPal Pro', 'key'=>'paypal_pro', 'class'=>'PayPalPro'];