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
        //find out if they're already logged in, if they are redirect them to the my account page
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
            $this->view('login', ['redirect'=>$redirect]);
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

        $submitted = \CI::input()->post('submitted');

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
}
