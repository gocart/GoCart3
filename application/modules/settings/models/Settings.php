<?php
Class Settings
{

    public function get_settings($code)
    {
        CI::db()->where('code', $code);
        $result = CI::db()->get('settings');

        $return = [];
        foreach($result->result() as $results)
        {
            $return[$results->setting_key]  = $results->setting;
        }
        return $return; 
    }
    
    /*
    settings should be an array
    array('setting_key'=>'setting')
    $code is the item that is calling it
    ex. any shipping settings have the code "shipping"
    */
    public function save_settings($code, $values)
    {
        //get the settings first, this way, we can know if we need to update or insert settings
        //we're going to create an array of keys for the requested code
        $settings   = $this->get_settings($code);

        //loop through the settings and add each one as a new row
        foreach($values as $key=>$value)
        {
            //if the key currently exists, update the setting
            if(array_key_exists($key, $settings))
            {
                $update = array('setting'=>$value);
                CI::db()->where('code', $code);
                CI::db()->where('setting_key',$key);
                CI::db()->update('settings', $update);
            }
            //if the key does not exist, add it
            else
            {
                $insert = array('code'=>$code, 'setting_key'=>$key, 'setting'=>$value);
                CI::db()->insert('settings', $insert);
            }
        }
    }

    //delete any settings having to do with this particular code
    public function delete_settings($code)
    {
        CI::db()->where('code', $code);
        CI::db()->delete('settings');
    }

    //this deletes a specific setting
    public function delete_setting($code, $setting_key)
    {
        CI::db()->where('code', $code);
        CI::db()->where('setting_key', $setting_key);
        CI::db()->delete('settings');
    }
}