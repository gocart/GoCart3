<?php defined('BASEPATH') OR exit('No direct script access allowed');

function assign_value($field, $default = '')
{
    return set_value($field, $default, false);
}
