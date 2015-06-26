<?php

$routes[] = ['GET|POST', '/admin/gift-cards/form', 'GoCart\Controller\AdminGiftCards#form'];
$routes[] = ['GET|POST', '/admin/gift-cards/delete/[i:id]', 'GoCart\Controller\AdminGiftCards#delete'];
$routes[] = ['GET|POST', '/admin/gift-cards/enable', 'GoCart\Controller\AdminGiftCards#enable'];
$routes[] = ['GET|POST', '/admin/gift-cards/disable', 'GoCart\Controller\AdminGiftCards#disable'];
$routes[] = ['GET|POST', '/admin/gift-cards/settings', 'GoCart\Controller\AdminGiftCards#settings'];
$routes[] = ['GET|POST', '/admin/gift-cards', 'GoCart\Controller\AdminGiftCards#index'];

//manifest
$classMap['GoCart\Controller\AdminGiftCards'] = 'controllers/AdminGiftCards.php';