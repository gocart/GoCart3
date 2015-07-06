<?php

$routes[] = ['GET', '/admin/categories', 'GoCart\Controller\AdminCategories#index'];
$routes[] = ['GET|POST', '/admin/categories/form/[i:id]?', 'GoCart\Controller\AdminCategories#form'];
$routes[] = ['GET|POST', '/admin/categories/delete/[i:id]', 'GoCart\Controller\AdminCategories#delete'];
$routes[] = ['GET|POST', '/category/[:slug]/[:sort]?/[:dir]?/[:page]?', 'GoCart\Controller\Category#index'];

$themeShortcodes[] = ['shortcode'=>'category', 'method'=>['GoCart\Controller\Category', 'shortcode']];