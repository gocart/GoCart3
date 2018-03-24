<?php namespace GoCart\Controller;
/**
 * AdminTableRate Class
 *
 * @package     GoCart
 * @subpackage  Controllers
 * @category    AdminTableRate
 * @author      Clear Sky Designs
 * @link        http://gocartdv.com
 */

class AdminTableRate extends Admin { 

    public function __construct()
    {       
        parent::__construct();
        \CI::auth()->check_access('Admin', true);
        \CI::lang()->load('table_rate');
    }

    //back end installation functions
    public function install()
    {
        $install = ['enabled'=>false];
        
        $rate = [0=>[
            'name'=>'Example',
            'method'=>'price',
            'coutry'=>[],
            'rates'=>[
                '80'   => '85.00',
                '70'   => '65.00',
                '60'   => '55.00',
                '50'   => '55.00',
                '40'   => '45.00',
                '30'   => '35.00',
                '20'   => '25.00',
                '10'   => '15.00',
                '0'    => '5.00'
            ]
        ]];
        
        $install['rates']   = json_encode($rate);
        
        \CI::Settings()->save_settings('shipping_modules', ['TableRate'=>'1']);
        \CI::Settings()->save_settings('TableRate', $install);

        redirect('admin/shipping');
    }

    public function uninstall()
    {
        \CI::Settings()->delete_settings('TableRate');
        \CI::Settings()->delete_setting('shipping_modules', 'TableRate');

        redirect('admin/shipping');
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
            if(empty($_POST))
            {
                $settings = \CI::Settings()->get_settings('TableRate');
                $settings['rates'] = json_decode($settings['rates'], true);
            }
            else
            {
                $settings['enabled'] = \CI::input()->post('enabled');
                $settings['rates'] = \CI::input()->post('rates');
            }

            $settings['countries'] = \CI::Locations()->get_countries_menu();

            $this->view('table_rate_form', $settings);
        }
        else
        {
            $save = ['enabled' => \CI::input()->post('enabled')];

            $postedRates = \CI::input()->post('rate');
            $rates = [];
            foreach($postedRates as $rate)
            {
                if(isset($rate['rates']))
                {
                    $rate['rates'] = $this->sortRates($rate['rates']);
                }
                else
                {
                    $rate['rates'] = [];
                }
                
                $rates[] = $rate;
            }

            $save['rates'] = json_encode($rates);

            \CI::Settings()->save_settings('TableRate', $save);
            redirect('admin/shipping');
        }
    }

    private function sortRates($rates)
    {
        $new_rates  = [];
        foreach($rates as $r)
        {
            if(is_numeric($r['from']) && is_numeric($r['rate']))
            {
                $new_rates[$r['from']]  = $r['rate'];
            }
            ksort($new_rates, SORT_NUMERIC);
        }
        return $new_rates;
    }
}