<?php
class Login extends CI_Model
{

    public function __construct()
    {
        $customer = CI::session()->userdata('customer');

        if (empty($customer)) {
        //If we don't have a customer, check for a cookie.
            if (isset($_COOKIE['GoCartCustomer'])) {
            //the cookie is there, lets log the customer back in.
                $info = $this->aes256Decrypt($_COOKIE['GoCartCustomer']);
                $cred = json_decode($info);

                if (is_object($cred)) {
                    $this->loginCustomer($cred->email, $cred->password, true);
                    if (! $this->isLoggedIn()) {
                    // cookie data isn't letting us login.
                        $this->logoutCustomer();
                        $this->createGuest();
                    }
                }
            } else {
                //cookie is empty
                $this->logoutCustomer();
                $this->createGuest();
            }
        }
    }

    public function customer()
    {
        return CI::session()->userdata('customer');
    }

    public function logoutCustomer()
    {
        CI::session()->unset_userdata('customer');

        //force expire the cookie
        $this->generateCookie('[]', time()-3600);
    }

    private function generateCookie($data, $expire)
    {
        setcookie('GoCartCustomer', $data, $expire, '/', $_SERVER['HTTP_HOST'], config_item('ssl_support'), true);
    }

    public function loginCustomer($email, $password, $remember = false)
    {
        $customer = CI::db()->where('is_guest', 0)->
                        where('email', $email)->
                        where('active', 1)->
                        limit(1)->
                        get('customers')->row();

        if ($customer && !(bool)$customer->is_guest && password_verify($password, $customer->password) == true) {
        // Set up any group discount
            if ($customer->group_id != 0) {
                $group = CI::Customers()->get_group($customer->group_id);
                if ($group) { // group might not exist
                    $customer->group = $group;
                }
            }

            if ($remember) {
                $loginCred = json_encode(array('email'=>$customer->email, 'password'=>$customer->password));
                $loginCred = $this->aes256Encrypt($loginCred);
                //remember the user for 6 months
                $this->generateCookie($loginCred, strtotime('+6 months'));
            }

            //combine cart items
            if ($this->customer()) {
                $oldCustomer = $this->customer();
                CI::session()->set_userdata('customer', $customer);
                \GC::combineCart($oldCustomer); // send the logged-in customer data
            }
            return true;
        } else {
            return false;
        }
    }

    public function isLoggedIn($redirect = false, $default_redirect = 'login')
    {
        //$redirect allows us to choose where a customer will get redirected to after they login
        //$default_redirect points is to the login page, if you do not want this, you can set it to false and then redirect wherever you wish.

        $customer = CI::session()->userdata('customer');

        if (!$customer) {
            return false;
        }

        if (isset($customer->is_guest) && $customer->is_guest == 1) {
            if ($redirect) {
                redirect($default_redirect);
            } else {
                return false;
            }
        } else {
            return true;
        }
    }

    private function createGuest()
    {
        //create a temp customer
        $customerID = CI::Customers()->createGuest();
        $customer = CI::db()->where('id', $customerID)->get('customers')->row();
        CI::session()->set_userdata('customer', $customer);
    }

    private function aes256Encrypt($data)
    {
        $key = config_item('encryption_key');
        if (32 !== strlen($key)) {
            $key = hash('SHA256', $key, true);
        }
        $padding = 16 - (strlen($data) % 16);
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
        $data .= str_repeat(chr($padding), $padding);
        $encrypted =  openssl_encrypt($data, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
        return base64_encode($encrypted . '::' . $iv);
    }

    private function aes256Decrypt($data)
    {
        $key = config_item('encryption_key');
        if (32 !== strlen($key)) {
            $key = hash('SHA256', $key, true);
        }
        list($encrypted_data, $iv) = explode('::', base64_decode($data), 2);
        return openssl_decrypt($encrypted_data, 'aes-256-cbc', $encryption_key, 0, $iv);
    }
}
