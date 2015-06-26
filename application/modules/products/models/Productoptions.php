<?php
/**
 * ProductOptions Class
 *
 * @package     GoCart
 * @subpackage  Models
 * @category    ProductOptions
 * @author      Clear Sky Designs
 * @link        http://gocartdv.com
 */

Class ProductOptions extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        CI::load()->helper('formatting_helper');
    }
    
    /********************************************************************
        Options Management
    ********************************************************************/
    public function getAllOptions($product_id)
    {
        CI::db()->where('product_id', $product_id);
        CI::db()->order_by('id', 'DESC');
        $result = CI::db()->get('options');
        
        $return = [];
        foreach($result->result() as $option)
        {
            $option->values = $this->getOptionValues($option->id);
            $return[] = $option;
        }
        return $return;
    }
    
    public function getOption($id, $as_array = false)
    {
        $result = CI::db()->get_where('options', array('id'=>$id));
        
        $data = $result->row();
        
        if($as_array)
        {
            $data->values = $this->getOptionValues($id, true);
        }
        else
        {
            $data->values = $this->getOptionValues($id);
        }
        
        return $data;
    }
    
    public function saveOption($option, $values)
    {
        if(isset($option['id']))
        {
            CI::db()->where('id', $option['id']);
            CI::db()->update('options', $option);
            $id = $option['id'];
            
            //eliminate existing options
            $this->deleteOptionValues($id);
        }
        else
        {
            CI::db()->insert('options', $option);
            $id = CI::db()->insert_id();
        }
        
        //add options to the database
        $sequence   = 0;
        foreach($values as $value)
        {
            $value['option_id'] = $id;
            $value['sequence'] = $sequence;
            $value['weight'] = floatval($value['weight']);
            $value['price'] = floatval($value['price']);
            $sequence++;
            
            CI::db()->insert('option_values', $value);
        }
        return $id;
    }
    
    // for product level options 
    public function clearOptions($product_id)
    {
        // get the list of options for this product
        $list = CI::db()->where('product_id', $product_id)->get('options')->result();
        
        foreach($list as $opt)
        {
            $this->deleteOption($opt->id);
        }
    }
    
    // also deletes child records in optionValues and product_option
    public function deleteOption($id)
    {
        CI::db()->where('id', $id);
        CI::db()->delete('options');
        
        $this->deleteOptionValues($id);
    }
    


    /********************************************************************
        Option values Management
    ********************************************************************/
    
    public function getOptionValues($option_id)
    {
        CI::db()->where('option_id',$option_id); 
        CI::db()->order_by('sequence', 'ASC');
        return CI::db()->get('option_values')->result();
    }
    
    public function getValue($value_id) 
    {
        CI::db()->where('id', $value_id);
        return CI::db()->get('option_values')->row();
    }
    
    public function deleteOptionValues($id)
    {
        CI::db()->where('option_id', $id);
        CI::db()->delete('option_values');
    }
    

    /********************************************************************
        Product options Management
    ********************************************************************/
    public function getProductOptions($product_id)
    {
        CI::db()->where('product_id',$product_id); 
        CI::db()->order_by('sequence', 'ASC');
        
        $result = CI::db()->get('options');
        
        $return = [];
        foreach($result->result() as $option)
        {
            $option->values = $this->getOptionValues($option->id);
            $return[] = $option;
        }
        return $return;
    }

    
    /***************************************************
        Options Live Use public Functionality
    ****************************************************/
    
    public function validateProductOptions($product, $values)
    {

        if(empty($product->product_id))
        {
            return false;
        }
        
        // set up to catch option errors
        $error = false;
        $msg = lang('option_error').'<br/>';
        
        // Get the list of options for the product 
        //  We will check the submitted options against this to make sure required options were selected    
        $options = $this->getProductOptions($product->product_id);
        
        // Loop through the options from the database
        foreach($options as $option)
        {
            // Use the product option to see if we have matching data from the product form
            if(isset($values[$option->id]))
            {
                $optionValue = $values[$option->id];
            }

            // are we missing any required values?
            if((int) $option->required && empty($optionValue)) 
            {
                // Set our error flag and add to the user message
                //  then continue processing the other options to built a full list of missing requirements
                $error = true;
                $msg .= "- ". $option->name .'<br/>';
                continue; // don't bother processing this particular option any further
            }
            
            // process checklist items specially
            // multi-valued
            if($option->type == 'checklist')
            {

                $opts = [];
                // tally our adjustments
                
                //check to make sure this is an array before looping
                if(is_array($optionValue))
                {
                    
                    foreach($optionValue as $check_value) 
                    {
                        //$val = $this->get_value($check_value);
                        
                        foreach($option->values as $check_match)
                        {
                            if($check_match->id == $check_value)
                            {
                                $val = $check_match;
                            }
                        }
                        
                        $price = '';
                        if($val->price > 0)
                        {
                            $price = ' (+'.format_currency($val->price).')';
                        }

                        array_push($opts, $val->value.$price);
                    }
                }
                
                // If only one option was checked, add it as a single value
                if(count($opts)==1) 
                {
                    $product['options'][$option->name] = $opts[0];
                }
                // otherwise, add it as an array of values
                elseif(!empty($opts)) 
                { 
                    $product['options'][$option->name] = $opts;
                }
            }
            
             // handle text fields
            else if($option->type == 'textfield' || $option->type == 'textarea') 
            {
                //get the value and weight of the textfield/textarea and add it!
                
                if($optionValue)
                {
                    //get the potential price and weight of this field
                    $val = $option->values[0];

                    //add the weight and price to the product
                    $product['price'] = $product['price'] + $val->price;
                    $product['weight'] = $product['weight'] + $val->weight;
                    
                    //if there is additional cost, add it to the item description
                    $price = '';
                    if($val->price > 0)
                    {
                        $price = ' (+'.format_currency($val->price).')';
                    }
                    
                    $product['options'][$option->name] = $optionValue.$price;
                }
            }
             // handle radios and droplists
            else
            {
                //make sure that blank options aren't used
                if ($optionValue)
                {
                    // we only need the one selected
                    //$val = $this->get_value($optionValue);
                    
                    foreach($option->values as $check_match)
                    {
                        if($check_match->id == $optionValue)
                        {
                            $val = $check_match;
                        }
                    }
                    
                    //adjust product price and weight
                    $product['price']   = $product['price'] + $val->price;
                    $product['weight']  = $product['weight'] + $val->weight;
                    
                    $price = '';
                    if($val->price > 0)
                    {
                        $price = ' (+'.format_currency($val->price).')';
                    }
                    //add the option to the options
                    //$product['options'][$option->name] = $val->name.$price.$weight;
                    $product['options'][$option->name] = $val->name.$price;
                }
            }
        }
        
        if($error)
        {
            return(['validated' => false, 'error' => $msg]);
        }
        else
        {
            return(['validated' => true]);
        }
        
    }
}