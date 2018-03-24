<?php

$routes[] = ['GET|POST', '/admin/twocheckoutapi/form', 'GoCart\Controller\AdminTwocheckoutApi#form'];
$routes[] = ['GET|POST', '/admin/twocheckoutapi/install', 'GoCart\Controller\AdminTwocheckoutApi#install'];
$routes[] = ['GET|POST', '/admin/twocheckoutapi/uninstall', 'GoCart\Controller\AdminTwocheckoutApi#uninstall'];
$routes[] = ['GET|POST', '/twocheckoutapi/process-payment', 'GoCart\Controller\TwocheckoutApi#processPayment'];


$paymentModules[] = ['name'=>'2Checkout API', 'key'=>'twocheckoutapi', 'class'=>'TwocheckoutApi'];