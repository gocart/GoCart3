<?php

$routes[] = ['GET|POST', '/admin/cod/form', 'GoCart\Controller\AdminCod#form'];
$routes[] = ['GET|POST', '/admin/cod/install', 'GoCart\Controller\AdminCod#install'];
$routes[] = ['GET|POST', '/admin/cod/uninstall', 'GoCart\Controller\AdminCod#uninstall'];
$routes[] = ['GET|POST', '/cod/process-payment', 'GoCart\Controller\Cod#processPayment'];

$paymentModules[] = ['name'=>'Charge on Delivery', 'key'=>'cod', 'class'=>'Cod'];