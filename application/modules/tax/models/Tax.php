<?php

Class Tax extends CI_Model
{
    var $state = '';
    var $state_taxes;
    
    public function __construct()
    {
        parent::__construct();
        
        $this->state_taxes = config_item('state_taxes');

        $order = GC::getCart();

        $taxType = config_item('tax_address');
        
        if($taxType =='ship')
        {
            if((bool)$order->shipping_address_id)
            {
                $this->address = CI::Customers()->get_address($order->shipping_address_id);
            }
            else
            {
                return 0;
            }
        } else {
            if((bool)$order->billing_address_id)
            {
                $this->address = CI::Customers()->get_address($order->billing_address_id);
            }
            else
            {
                return 0;
            }
        }
        if(!$this->address)
        {
            return 0;
        }
    }
    public function getCountryTaxRate()
    {
        $rate = CI::db()->where('id', $this->address['country_id'])->get('countries')->row();

        if($rate)
        {
            $rate = $rate->tax/100;
        }
        else
        {
            $rate = 0;
        }
    
        return $rate;
    }
    
    public function getZoneTaxRate()
    {
        $rate = CI::db()->where('id', $this->address['zone_id'])->get('country_zones')->row();

        if($rate)
        {
            $rate = $rate->tax/100;
        }
        else
        {
            $rate = 0;
        }
    
        return $rate;
    }
    
    public function getAreaTaxRate()
    {
        $rate = CI::db()->where(array('code'=>$this->address['zip'], 'zone_id'=>$this->address['zone_id']))->get('country_zone_areas')->row();

        if($rate)
        {
            $rate = $rate->tax/100;
        }
        else
        {
            $rate = 0;
        }
    
        return $rate;
    }
    
    public function getTaxTotal()
    {
        $taxTotal = 0;
        $taxTotal = $taxTotal + $this->getTaxes();

        return number_format($taxTotal, 2, '.', '');
    }
    
    public function getTaxRate()
    {
        //if there is no address yet return 0
        if(empty($this->address))
        {
            return 0;
        }

        $rate = 0;

        $rate += $this->getCountryTaxRate();
        $rate += $this->getZoneTaxRate();
        $rate += $this->getAreaTaxRate();

        //returns the total rate not affected by price of merchandise.
        return $rate;
    }
    
    public function getTaxes()
    {
        $rate = $this->getTaxRate();
        $orderPrice = GC::getTaxableTotal();

        //send the price of the taxes back
        return round(($orderPrice * $rate), 2);
    }
}