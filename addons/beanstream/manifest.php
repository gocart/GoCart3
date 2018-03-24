<?php

$routes[] = ['GET|POST', '/admin/beanstream/form', 'GoCart\Controller\AdminBeanstream#form'];
$routes[] = ['GET|POST', '/admin/beanstream/install', 'GoCart\Controller\AdminBeanstream#install'];
$routes[] = ['GET|POST', '/admin/beanstream/uninstall', 'GoCart\Controller\AdminBeanstream#uninstall'];
$routes[] = ['GET|POST', '/beanstream/process-payment', 'GoCart\Controller\Beanstream#processPayment'];


$paymentModules[] = ['name'=>'beanstream', 'key'=>'beanstream', 'class'=>'Beanstream'];