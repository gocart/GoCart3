<?php

$routes[] = ['GET|POST', '/admin/sitemap', 'GoCart\Controller\AdminSitemap#index'];
$routes[] = ['GET|POST', '/admin/sitemap/new-sitemap', 'GoCart\Controller\AdminSitemap#newSitemap'];
$routes[] = ['GET|POST', '/admin/sitemap/generate-products', 'GoCart\Controller\AdminSitemap#generateProducts'];
$routes[] = ['GET|POST', '/admin/sitemap/generate-pages', 'GoCart\Controller\AdminSitemap#generatePages'];
$routes[] = ['GET|POST', '/admin/sitemap/generate-categories', 'GoCart\Controller\AdminSitemap#generateCategories'];
$routes[] = ['GET|POST', '/admin/sitemap/complete-sitemap', 'GoCart\Controller\AdminSitemap#completeSitemap'];