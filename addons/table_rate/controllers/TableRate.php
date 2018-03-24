<?php namespace GoCart\Controller;
/**
 * TableRate Class
 *
 * @package     GoCart
 * @subpackage  Controllers
 * @category    TableRate
 * @author      Clear Sky Designs
 * @link        http://gocartdv.com
 */

class TableRate extends Front { 

    public function __construct()
    {
        parent::__construct();
        \CI::load()->model(array('Locations'));
        $this->customer = \CI::Login()->customer();
    }

    public function rates()
    {
        $addressID = \GC::getAttribute('shipping_address_id');
        $address = \CI::Customers()->get_address($addressID);

        //if there is no address set then return blank
        if(empty($address))
        {
            return [];
        }
        
        $settings = \CI::Settings()->get_settings('TableRate');
        
        if(!(bool)$settings['enabled'])
        {
            return [];
        }
        
        $rates = json_decode($settings['rates'], true);
        
        $orderWeight = $this->getOrderWeight();
        $orderSubtotal = $this->getOrderSubtotal();
        
        $return = [];
        foreach($rates as $rate)
        {
            // Check if customer is in the applicable country
            if(!$this->isCustomerInCountry($address, $rate))
            {
                continue;
            }

            //sort rates highest "From" to lowest
            krsort($rate['rates'], SORT_NUMERIC);

            if ($rate['method'] == 'weight')
            {
                foreach ($rate['rates'] as $key => $val)
                {
                    if($key <= $orderWeight)
                    {
                        $return[$rate['name']] = $val;
                        break;
                    }
                }
            }
            elseif ($rate['method'] == 'price')
            {
                foreach ($rate['rates'] as $key => $val)
                {
                    if($key <= $orderSubtotal)
                    {
                        $return[$rate['name']] = $val;
                        break;
                    }
                }
            }
        }
        
        return $return;
    }

    private function getOrderWeight()
    {
        return \GC::getTotalWeight();
    }
    
    private function getOrderSubtotal()
    {
        return \GC::getSubtotal();
    }

    private function isCustomerInCountry($address, $rate)
    {
        if(isset($rate['country']))
        {
            if(is_array($rate['country']) && in_array($address['country_id'], $rate['country']))
            {
                return true;
            }
            elseif(!is_array($rate['country']) && $rate['country'] == $address['country_id'])
            {
                return true;
            }
            return false;
        }
        else
        {
            return true;
        }
    }
}