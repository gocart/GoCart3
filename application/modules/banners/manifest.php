<?php

$routes[] = ['GET', '/admin/banners', 'GoCart\Controller\AdminBanners#index'];
$routes[] = ['GET|POST', '/admin/banners/banner_collection_form/[i:id]?', 'GoCart\Controller\AdminBanners#banner_collection_form'];
$routes[] = ['GET|POST', '/admin/banners/delete_banner_collection/[i:id]', 'GoCart\Controller\AdminBanners#delete_banner_collection'];
$routes[] = ['GET|POST', '/admin/banners/banner_collection/[i:id]', 'GoCart\Controller\AdminBanners#banner_collection'];
$routes[] = ['GET|POST', '/admin/banners/banner_form/[i:banner_collection_id]/[i:id]?', 'GoCart\Controller\AdminBanners#banner_form'];
$routes[] = ['GET|POST', '/admin/banners/delete_banner/[i:id]', 'GoCart\Controller\AdminBanners#delete_banner'];
$routes[] = ['GET|POST', '/admin/banners/organize', 'GoCart\Controller\AdminBanners#organize'];

$themeShortcodes[] = ['shortcode'=>'banner', 'method'=>['Banners', 'show_collection']];