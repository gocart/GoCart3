<?php
$routes[] = ['GET', '/admin/coupons', 'GoCart\Controller\AdminCoupons#index'];
$routes[] = ['GET|POST', '/admin/coupons/form/[i:id]?', 'GoCart\Controller\AdminCoupons#form'];
$routes[] = ['GET|POST', '/admin/coupons/delete/[i:id]', 'GoCart\Controller\AdminCoupons#delete'];

//manifest
$classMap['GoCart\Controller\AdminCoupons'] = 'controllers/AdminCoupons.php';
