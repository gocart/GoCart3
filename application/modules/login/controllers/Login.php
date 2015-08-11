<?php namespace GoCart\Controller;
/**
 * Login Class
 *
 * @package     GoCart
 * @subpackage  Controllers
 * @category    Login
 * @author      Clear Sky Designs
 * @link        http://gocartdv.com
 */

class Login extends Front {

    var $customer;

    public function __construct()
    {
        parent::__construct();
        $this->customer = \CI::Login()->customer();
    }

    public function login($redirect= '')
    {
        //find out if they're already logged in
        if (\CI::Login()->isLoggedIn(false, false))
        {

            redirect($redirect);
        }

        \CI::load()->library('form_validation');
        \CI::form_validation()->set_rules('email', 'lang:address_email', ['trim','required','valid_email']);
        \CI::form_validation()->set_rules('password', 'Password', ['required', ['check_login_callable', function($str){
            $email = \CI::input()->post('email');
            $password = \CI::input()->post('password');
            $remember = \CI::input()->post('remember');
            $login = \CI::Login()->loginCustomer($email, sha1($password), $remember);
            if(!$login)
            {
                \CI::form_validation()->set_message('check_login_callable', lang('login_failed'));
                return false;
            }
        }]]);

        if (\CI::form_validation()->run() == FALSE)
        {
            $this->view('login', ['redirect'=>$redirect, 'loginErrors'=>\CI::form_validation()->get_error_array()]);
        }
        else
        {
            redirect($redirect);
        }
    }

    public function logout()
    {
        \CI::Login()->logoutCustomer();
        redirect('login');
    }

    public function forgotPassword()
    {
        $data['page_title'] = lang('forgot_password');

        \CI::form_validation()->set_rules('email', 'lang:address_email', ['trim', 'required', 'valid_email',
            ['email_callable', function($str)
                {
                    $reset = \CI::Customers()->reset_password($str);

                    if(!$reset)
                    {
                        \CI::form_validation()->set_message('email_callable', lang('error_no_account_record'));
                        return FALSE;
                    }
                    else
                    {
                        //user does exist. and the password is reset.
                        return TRUE;
                    }
                }
            ]
        ]);

        if (\CI::form_validation()->run() == FALSE)
        {
            $this->view('forgot_password', $data);
        }
        else
        {
            \CI::session()->set_flashdata('message', lang('message_new_password'));
            redirect('login');
        }
    }

    public function register()
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

        \CI::form_validation()->set_rules('company', 'lang:account_company', 'trim|max_length[128]');
        \CI::form_validation()->set_rules('firstname', 'lang:account_firstname', 'trim|required|max_length[32]');
        \CI::form_validation()->set_rules('lastname', 'lang:account_lastname', 'trim|required|max_length[32]');
        \CI::form_validation()->set_rules('email', 'lang:account_email', ['trim','required','valid_email','max_length[128]', ['check_email_callable', function($str){
            return $this->check_email($str);
        }]]);
        \CI::form_validation()->set_rules('phone', 'lang:account_phone', 'trim|required|max_length[32]');
        \CI::form_validation()->set_rules('email_subscribe', 'lang:email_subscribe', 'trim|numeric|max_length[1]');
        \CI::form_validation()->set_rules('password', 'lang:account_password', 'required|min_length[6]');
        \CI::form_validation()->set_rules('confirm', 'lang:account_confirm', 'required|matches[password]');

        
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

            $data['registrationErrors'] = \CI::form_validation()->get_error_array();

            $this->view('login', $data);
        }
        else
        {
            $save['id'] = false;
            $save['firstname'] = \CI::input()->post('firstname');
            $save['lastname']  = \CI::input()->post('lastname');
            $save['email'] = \CI::input()->post('email');
            $save['phone'] = \CI::input()->post('phone');
            $save['company'] = \CI::input()->post('company');
            $save['active'] = (bool)config_item('new_customer_status');
            $save['email_subscribe'] = intval((bool)\CI::input()->post('email_subscribe'));
            
            $save['password']  = sha1(\CI::input()->post('password'));
            
            $redirect  = \CI::input()->post('redirect');
            
            //if we don't have a value for redirect
            if ($redirect == '')
            {
                $redirect = 'my-account';
            }
            
            // save the customer info and get their new id
            \CI::Customers()->save($save);
            
            //send the registration email
            \GoCart\Emails::registration($save);

            //load twig for this language string
            $loader = new \Twig_Loader_String();
            $twig = new \Twig_Environment($loader);
            
            //if they're automatically activated log them in and send them where they need to go
            if($save['active'])
            {
                \CI::session()->set_flashdata('message', $twig->render( lang('registration_thanks'), $save) );
            
                //lets automatically log them in
                \CI::Login()->loginCustomer($save['email'], $save['password']);

                //to redirect them, if there is no redirect, the it should redirect to the homepage.
                redirect($redirect);
            }
            else
            {
                //redirect to the login page if they need to wait for activation
                \CI::session()->set_flashdata('message', $twig->render( lang('registration_awaiting_activation'), $save) );
                redirect('login');
            }
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
