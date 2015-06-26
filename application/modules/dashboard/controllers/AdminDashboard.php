<?php namespace GoCart\Controller;
/**
 * AdminDashboard Class
 *
 * @package     GoCart
 * @subpackage  Controllers
 * @category    AdminDashboard
 * @author      Clear Sky Designs
 * @link        http://gocartdv.com
 */

class AdminDashboard extends Admin {

    public function __construct()
    {
        parent::__construct();

        if(\CI::auth()->check_access('Orders'))
        {
            redirect(config_item('admin_folder').'/orders');
        }

        \CI::load()->model('Orders');
        \CI::load()->model('Customers');
        \CI::load()->helper('date');
        \CI::lang()->load('dashboard');
    }

    public function index()
    {
        //check to see if shipping and payment modules are installed
        $data['payment_module_installed'] = (bool)count(\CI::Settings()->get_settings('payment_modules'));
        $data['shipping_module_installed'] = (bool)count(\CI::Settings()->get_settings('shipping_modules'));

        $data['page_title'] =  lang('dashboard');

        // get 5 latest orders
        $data['orders'] = \CI::Orders()->getOrders(false, 'ordered_on' , 'DESC', 5);

        // get 5 latest customers
        $data['customers'] = \CI::Customers()->get_customers(5);

        $this->view('dashboard', $data);
    }

}
