<?php

$routes[] = ['GET|POST', '/admin/table-rate/form', 'GoCart\Controller\AdminTableRate#form'];
$routes[] = ['GET|POST', '/admin/table-rate/install', 'GoCart\Controller\AdminTableRate#install'];
$routes[] = ['GET|POST', '/admin/table-rate/uninstall', 'GoCart\Controller\AdminTableRate#uninstall'];

$shippingModules[] = ['name'=>'Table Rate', 'key'=>'table-rate', 'class'=>'TableRate'];