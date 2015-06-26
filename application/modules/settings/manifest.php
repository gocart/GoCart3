<?php

$routes[] = ['GET|POST', '/admin/settings', 'GoCart\Controller\AdminSettings#index'];
$routes[] = ['GET|POST', '/admin/settings/canned_messages', 'GoCart\Controller\AdminSettings#canned_messages'];
$routes[] = ['GET|POST', '/admin/settings/canned_message_form/[i:id]?', 'GoCart\Controller\AdminSettings#canned_message_form'];
$routes[] = ['GET|POST', '/admin/settings/delete_message/[i:id]', 'GoCart\Controller\AdminSettings#delete_message'];