<?php namespace GoCart\Controller;
/**
 * AdminCod Class
 *
 * @package     GoCart
 * @subpackage  Controllers
 * @category    AdminCod
 * @author      Clear Sky Designs
 * @link        http://gocartdv.com
 */

class AdminCod extends Admin { 

    public function __construct()
    {       
        parent::__construct();
        
        \CI::auth()->check_access('Admin', true);
        \CI::lang()->load('cod');
    }

    //back end installation functions
    public function install()
    {
        //set a default blank setting for flatrate shipping
        \CI::Settings()->save_settings('payment_modules', array('cod'=>'1'));
        \CI::Settings()->save_settings('cod', array('enabled'=>'1'));

        redirect('admin/payments');
    }

    public function uninstall()
    {
        \CI::Settings()->delete_setting('payment_modules', 'cod');
        \CI::Settings()->delete_settings('cod');
        redirect('admin/payments');
    }

    //admin end form and check functions
    public function form()
    {
        //this same function processes the form
        \CI::load()->helper('form');
        \CI::load()->library('form_validation');

        \CI::form_validation()->set_rules('enabled', 'lang:enabled', 'trim|numeric');

        if (\CI::form_validation()->run() == FALSE)
        {
            $settings = \CI::Settings()->get_settings('cod');
            $enabled = $settings['enabled'];

            $this->view('cod_form', ['enabled'=>$enabled]);
        }
        else
        {
            \CI::Settings()->save_settings('cod', array('enabled'=>$_POST['enabled']));
            redirect('admin/payments');
        }
    }
}