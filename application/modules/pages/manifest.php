<?php

$routes[] = ['GET', '/admin/pages', 'GoCart\Controller\AdminPages#index'];
$routes[] = ['GET|POST', '/admin/pages/form/[i:id]?', 'GoCart\Controller\AdminPages#form'];
$routes[] = ['GET|POST', '/admin/pages/link_form/[i:id]?', 'GoCart\Controller\AdminPages#link_form'];
$routes[] = ['GET|POST', '/admin/pages/delete/[i:id]', 'GoCart\Controller\AdminPages#delete'];
$routes[] = ['GET|POST', '/page/[:slug]', 'GoCart\Controller\Page#index'];