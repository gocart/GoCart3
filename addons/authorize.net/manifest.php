<?php

$routes[] = ['GET|POST', '/admin/authorize/form', 'GoCart\Controller\AdminAuthorize#form'];
$routes[] = ['GET|POST', '/admin/authorize/install', 'GoCart\Controller\AdminAuthorize#install'];
$routes[] = ['GET|POST', '/admin/authorize/uninstall', 'GoCart\Controller\AdminAuthorize#uninstall'];
$routes[] = ['GET|POST', '/authorize/process-payment', 'GoCart\Controller\Authorize#processPayment'];


$paymentModules[] = ['name'=>'Authorize.Net', 'key'=>'authorize', 'class'=>'Authorize'];