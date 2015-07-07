<?php

$routes[] = ['GET|POST', '/admin/login', 'GoCart\Controller\AdminLogin#login'];
$routes[] = ['GET|POST', '/admin/logout', 'GoCart\Controller\AdminLogin#logout'];
$routes[] = ['GET|POST', '/login/[:redirect]?', 'GoCart\Controller\Login#login'];
$routes[] = ['GET|POST', '/logout', 'GoCart\Controller\Login#logout'];
$routes[] = ['GET|POST', '/forgot-password', 'GoCart\Controller\Login#forgotPassword'];
$routes[] = ['GET|POST', '/admin/forgot-password', 'GoCart\Controller\AdminLogin#forgotPassword'];
$routes[] = ['GET|POST', '/register', 'GoCart\Controller\Login#register'];