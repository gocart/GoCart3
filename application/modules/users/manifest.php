<?php

$routes[] = ['GET|POST', '/admin/users', 'GoCart\Controller\AdminUsers#index'];
$routes[] = ['GET|POST', '/admin/users/form/[i:id]?', 'GoCart\Controller\AdminUsers#form'];
$routes[] = ['GET|POST', '/admin/users/delete/[i:id]', 'GoCart\Controller\AdminUsers#delete'];