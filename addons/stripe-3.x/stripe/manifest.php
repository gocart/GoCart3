<?php
$routes[] = ['GET|POST', '/admin/stripe/form', 'GoCart\Controller\AdminStripe#form'];
$routes[] = ['GET|POST', '/stripe/process-payment', 'GoCart\Controller\Stripe#processPayment'];
$routes[] = ['GET|POST', '/admin/stripe/install', 'GoCart\Controller\AdminStripe#install'];
$routes[] = ['GET|POST', '/admin/stripe/uninstall', 'GoCart\Controller\AdminStripe#uninstall'];

$paymentModules[] = ['name'=>'Stripe', 'key'=>'stripe', 'class'=>'Stripe'];