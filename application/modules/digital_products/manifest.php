<?php

$routes[] = ['GET|POST', '/admin/digital_products/form/[i:id]?', 'GoCart\Controller\AdminDigitalProducts#form'];
$routes[] = ['GET|POST', '/admin/digital_products/delete/[i:id]', 'GoCart\Controller\AdminDigitalProducts#delete'];
$routes[] = ['GET|POST', '/admin/digital_products', 'GoCart\Controller\AdminDigitalProducts#index'];

//manifest
$classMap['GoCart\Controller\AdminDigitalProducts'] = 'controllers/AdminDigitalProducts.php';