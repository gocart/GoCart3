<?php
class Locations extends CI_Model 
{
    //zone areas
    public function save_zone_area($data)
    {
        if(!$data['id']) 
        {
            CI::db()->insert('country_zone_areas', $data);
            return CI::db()->insert_id();
        } 
        else 
        {
            CI::db()->where('id', $data['id']);
            CI::db()->update('country_zone_areas', $data);
            return $data['id'];
        }
    }
    
    public function delete_zone_areas($country_id)
    {
        CI::db()->where('zone_id', $country_id)->delete('country_zone_areas');
    }
    
    public function delete_zone_area($id)
    {
        CI::db()->where('id', $id);
        CI::db()->delete('country_zone_areas');
    }
    
    public function get_zone_areas($country_id) 
    {
        CI::db()->where('zone_id', $country_id);
        return CI::db()->get('country_zone_areas')->result();
    }
    
    public function get_zone_area($id)
    {
        CI::db()->where('id', $id);
        return CI::db()->get('country_zone_areas')->row();
    }
    
    //zones
    public function save_zone($data)
    {
        if(!$data['id']) 
        {
            CI::db()->insert('country_zones', $data);
            return CI::db()->insert_id();
        } 
        else 
        {
            CI::db()->where('id', $data['id']);
            CI::db()->update('country_zones', $data);
            return $data['id'];
        }
    }
    
    public function delete_zones($country_id)
    {
        CI::db()->where('country_id', $country_id)->delete('country_zones');
    }
    
    public function delete_zone($id)
    {
        $this->delete_zone_areas($id);
        
        CI::db()->where('id', $id);
        CI::db()->delete('country_zones');
    }
    
    public function get_zones($country_id) 
    {
        CI::db()->where('country_id', $country_id);
        return CI::db()->get('country_zones')->result();
    }
    
    
    public function get_zone($id)
    {
        CI::db()->where('id', $id);
        return CI::db()->get('country_zones')->row();
    }
    
    
    
    //countries
    public function save_country($data)
    {
        if(!$data['id']) 
        {
            CI::db()->insert('countries', $data);
            return CI::db()->insert_id();
        } 
        else 
        {
            CI::db()->where('id', $data['id']);
            CI::db()->update('countries', $data);
            return $data['id'];
        }
    }
    
    public function organize_countries($countries)
    {
        //now loop through the products we have and add them in
        $sequence = 0;
        foreach ($countries as $country)
        {
            CI::db()->where('id',$country)->update('countries', array('sequence'=>$sequence));
            $sequence++;
        }
    }
    
    public function get_countries()
    {
        return CI::db()->order_by('sequence', 'ASC')->get('countries')->result();
    }
    
    public function get_country_by_zone_id($id)
    {
        $zone   = $this->get_zone($id);
        return $this->get_country($zone->country_id);
    }
    
    public function get_country($id)
    {
        CI::db()->where('id', $id);
        return CI::db()->get('countries')->row();
    }
    
    
    public function delete_country($id)
    {
        CI::db()->where('id', $id);
        CI::db()->delete('countries');
    }
    
    
    public function get_countries_menu()
    {   
        $countries  = CI::db()->order_by('sequence', 'ASC')->where('status', 1)->get('countries')->result();
        $return     = [];
        foreach($countries as $c)
        {
            $return[$c->id] = $c->name;
        }
        return $return;
    }
    
    public function get_zones_menu($country_id)
    {
        $zones  = CI::db()->where(array('status'=>1, 'country_id'=>$country_id))->get('country_zones')->result();
        $return = [];
        foreach($zones as $z)
        {
            $return[$z->id] = $z->name;
        }
        return $return;
    }
    
    public function has_zones($country_id)
    {
        if(!$country_id)
        {
            return false;
        }
        $count = CI::db()->where('country_id', $country_id)->count_all_results('country_zones');
        if($count > 0)
        {
            return true;
        } else {
            return false;
        }
    }
}