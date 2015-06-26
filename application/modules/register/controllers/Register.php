<?php namespace GoCart\Controller;
/**
 * Register Class
 *
 * @package  GoCart
 * @subpackage Controllers
 * @category Register
 * @author Clear Sky Designs
 * @link http://gocartdv.com
 */

class Register extends Front {

    var $customer;
    
    public function __construct()
    {
        parent::__construct();

        \CI::load()->model(array('Locations'));
        $this->customer = \CI::Login()->customer();
    }

    public function index()
    {
        $redirect  = \CI::Login()->isLoggedIn(false, false);
        //if they are logged in, we send them back to the my_account by default
        if ($redirect)
        {
            redirect('my-account');
        }
        
        \CI::load()->library('form_validation');
        
        //default values are empty if the customer is new
        $data = [
            'company' => '',
            'firstname' => '',
            'lastname' => '',
            'email' => '',
            'phone' => '',
            'address1' => '',
            'address2' => '',
            'city' => '',
            'state' => '',
            'zip' => '',

            'redirect' => \CI::session()->flashdata('redirect')
        ];

        \CI::form_validation()->set_rules('company', 'lang:address_company', 'trim|max_length[128]');
        \CI::form_validation()->set_rules('firstname', 'lang:address_firstname', 'trim|required|max_length[32]');
        \CI::form_validation()->set_rules('lastname', 'lang:address_lastname', 'trim|required|max_length[32]');
        \CI::form_validation()->set_rules('email', 'lang:address_email', ['trim','required','valid_email','max_length[128]', ['check_email_callable', function($str){
            return $this->check_email($str);
        }]]);
        \CI::form_validation()->set_rules('phone', 'lang:address_phone', 'trim|required|max_length[32]');
        \CI::form_validation()->set_rules('email_subscribe', 'lang:account_newsletter_subscribe', 'trim|numeric|max_length[1]');
        \CI::form_validation()->set_rules('password', 'Password', 'required|min_length[6]');
        \CI::form_validation()->set_rules('confirm', 'Confirm Password', 'required|matches[password]');

        
        if (\CI::form_validation()->run() == FALSE)
        {
            //if they have submitted the form already and it has returned with errors, reset the redirect
            if (\CI::input()->post('submitted'))
            {
                $data['redirect'] = \CI::input()->post('redirect');
            }
            
            // load other page content 
            //\CI::load()->model('banner_model');
            \CI::load()->helper('directory');

            $this->view('register', $data);
        }
        else
        {
            $save['id'] = false;
            $save['firstname'] = \CI::input()->post('firstname');
            $save['lastname']  = \CI::input()->post('lastname');
            $save['email'] = \CI::input()->post('email');
            $save['phone'] = \CI::input()->post('phone');
            $save['company'] = \CI::input()->post('company');
            $save['active']  = config_item('new_customer_status');
            $save['email_subscribe'] = intval((bool)\CI::input()->post('email_subscribe'));
            
            $save['password']  = \CI::input()->post('password');
            
            $redirect  = \CI::input()->post('redirect');
            
            //if we don't have a value for redirect
            if ($redirect == '')
            {
                $redirect = 'my-account';
            }
            
            // save the customer info and get their new id
            $id = \CI::Customers()->save($save);

            /* send an email */
            // get the email template
            $row = \CI::db()->where('id', '6')->get('canned_messages')->row_array();
            
            // set replacement values for subject & body
            // {customer_name}
            $row['subject'] = str_replace('{customer_name}', \CI::input()->post('firstname').' '. \CI::input()->post('lastname'), $row['subject']);
            $row['content'] = str_replace('{customer_name}', \CI::input()->post('firstname').' '. \CI::input()->post('lastname'), $row['content']);
            
            // {url}
            $row['subject'] = str_replace('{url}', config_item('base_url'), $row['subject']);
            $row['content'] = str_replace('{url}', config_item('base_url'), $row['content']);
            
            // {site_name}
            $row['subject'] = str_replace('{site_name}', config_item('company_name'), $row['subject']);
            $row['content'] = str_replace('{site_name}', config_item('company_name'), $row['content']);
            
            \CI::load()->library('email');
            
            $config['mailtype'] = 'html';
            
            \CI::email()->initialize($config);
    
            \CI::email()->from(config_item('email'), config_item('company_name'));
            \CI::email()->to($save['email']);
            \CI::email()->bcc(config_item('email'));
            \CI::email()->subject($row['subject']);
            \CI::email()->message(html_entity_decode($row['content']));
            
            \CI::email()->send();
            
            \CI::session()->set_flashdata('message', sprintf( lang('registration_thanks'), \CI::input()->post('firstname') ) );
            
            //lets automatically log them in
            \CI::Login()->loginCustomer($save['email'], \CI::input()->post('confirm'));
            
            //we're just going to make this secure regardless, because we don't know if they are
            //wanting to redirect to an insecure location, if it needs to be secured then we can use the secure redirect in the controller
            //to redirect them, if there is no redirect, the it should redirect to the homepage.
            redirect($redirect);
        }
    }

    public function check_email($str)
    {
        $email = \CI::Customers()->check_email($str);
        
        if ($email)
        {
            \CI::form_validation()->set_message('check_email_callable', lang('error_email'));
            return FALSE;
        }
        else
        {
            return TRUE;
        }
    }
}