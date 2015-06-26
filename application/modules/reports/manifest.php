<?php

$routes[] = ['GET|POST', '/admin/reports', 'GoCart\Controller\AdminReports#index'];
$routes[] = ['GET|POST', '/admin/reports/best_sellers', 'GoCart\Controller\AdminReports#best_sellers'];
$routes[] = ['GET|POST', '/admin/reports/sales', 'GoCart\Controller\AdminReports#sales'];