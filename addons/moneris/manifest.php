<?php

$routes[] = ['GET|POST', '/admin/moneris/form', 'GoCart\Controller\AdminMoneris#form'];
$routes[] = ['GET|POST', '/admin/moneris/install', 'GoCart\Controller\AdminMoneris#install'];
$routes[] = ['GET|POST', '/admin/moneris/uninstall', 'GoCart\Controller\AdminMoneris#uninstall'];
$routes[] = ['GET|POST', '/moneris/process-payment', 'GoCart\Controller\Moneris#processPayment'];


$paymentModules[] = ['name'=>'Moneris', 'key'=>'moneris', 'class'=>'Moneris'];