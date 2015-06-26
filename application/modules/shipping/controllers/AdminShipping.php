<?php namespace GoCart\Controller;
/**
 * AdminShipping Class
 *
 * @package     GoCart
 * @subpackage  Controllers
 * @category    AdminShipping
 * @author      Clear Sky Designs
 * @link        http://gocartdv.com
 */

class AdminShipping extends Admin {

    public function index()
    {
        \CI::auth()->check_access('Admin', true);

        \CI::lang()->load('settings');
        \CI::load()->helper('inflector');

        global $shippingModules;

        $data['shipping_modules'] = $shippingModules;
        $data['enabled_modules'] = \CI::Settings()->get_settings('shipping_modules');

        $data['page_title'] = lang('common_shipping_modules');
        $this->view('shipping_index', $data);
    }
}