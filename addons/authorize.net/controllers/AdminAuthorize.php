<?php namespace GoCart\Controller;
/**
 * AdminAuthorize Class
 *
 * @package     GoCart
 * @subpackage  Controllers
 * @category    AdminAuthorize
 * @author      Clear Sky Designs
 * @link        http://gocartdv.com
 */
class AdminAuthorize extends Admin { 

    public function __construct()
    {       
        parent::__construct();
        
        \CI::auth()->check_access('Admin', true);
        \CI::lang()->load('authorize');
    }

	function install()
	{
		$config['apiLoginId'] = '';
		$config['transactionKey'] = '';
		$config['enabled'] = "0";
		$config['testMode'] =  false;
        $configp['developerMode'] = true;

		\CI::Settings()->save_settings('payment_modules', array('authorize'=>'1'));
		\CI::Settings()->save_settings('authorize', $config);
		redirect('admin\payments');
	}
	
	function uninstall()
	{
        \CI::Settings()->delete_setting('payment_modules', 'authorize');
		\CI::Settings()->delete_settings('authorize');
		redirect('admin\payments');
	}

	public function form()
    {
        //this same function processes the form
        \CI::load()->helper('form');
        \CI::load()->library('form_validation');

        \CI::form_validation()->set_rules('apiLoginId', 'lang:apiLoginId', 'trim|required');
        \CI::form_validation()->set_rules('transactionKey', 'lang:transactionKey', 'trim|required');
        \CI::form_validation()->set_rules('enabled', 'lang:enabled', 'trim|required');	

        
        if (\CI::form_validation()->run() == FALSE)
        {
            $settings = \CI::Settings()->get_settings('authorize');
            $this->view('authorize_admin', $settings);
        }
        else
        {
            \CI::Settings()->save_settings('authorize', \CI::input()->post());
            redirect('admin\payments');
        }
    }


}    