<?php
/**
 * Banner Class
 *
 * @package     GoCart
 * @subpackage  Models
 * @category    Banners
 * @author      Clear Sky Designs
 * @link        http://gocartdv.com
 */

Class Banners extends CI_Model
{

    public function banner_collections()
    {
        return CI::db()->order_by('name', 'ASC')->get('banner_collections')->result();
    }
    
    public function banner_collection($banner_collection_id)
    {
        return CI::db()->where('banner_collection_id', $banner_collection_id)->get('banner_collections')->row();
    }
    
    public function banner_collection_banners($banner_collection_id, $only_active=false, $limit=5)
    {
        CI::db()->where('banner_collection_id', $banner_collection_id);
        $banners    = CI::db()->order_by('sequence', 'ASC')->get('banners')->result();
        
        if($only_active)
        {
            $return = [];
            foreach ($banners as $banner)
            {
                if ($banner->enable_date == '0000-00-00')
                {
                    $enable_test    = false;
                    $enable         = '';
                }
                else
                {
                    $eo             = explode('-', $banner->enable_date);
                    $enable_test    = $eo[0].$eo[1].$eo[2];
                    $enable         = $eo[1].'-'.$eo[2].'-'.$eo[0];
                }

                if ($banner->disable_date == '0000-00-00')
                {
                    $disable_test   = false;
                    $disable        = '';
                }
                else
                {
                    $do             = explode('-', $banner->disable_date);
                    $disable_test   = $do[0].$do[1].$do[2];
                    $disable        = $do[1].'-'.$do[2].'-'.$do[0];
                }

                $curDate        = date('Ymd');

                if ( (!$enable_test || $curDate >= $enable_test) && (!$disable_test || $curDate < $disable_test))
                {
                    $return[]   = $banner;
                }

                if(count($return) == $limit)
                {
                    break;
                }
            }
            
            return $return;
        }
        else
        {
            return $banners;
        }
    }
    
    public function banner($banner_id)
    {
        CI::db()->where('banner_id', $banner_id);
        $result = CI::db()->get('banners');
        $result = $result->row();
        
        if ($result)
        {
            if ($result->enable_date == '0000-00-00')
            {
                $result->enable_date = '';
            }
            
            if ($result->disable_date == '0000-00-00')
            {
                $result->disable_date = '';
            }
        
            return $result;
        }
        else
        { 
            return [];
        }
    }
    
    public function save_banner($data)
    {
        if(isset($data['banner_id']))
        {
            CI::db()->where('banner_id', $data['banner_id']);
            CI::db()->update('banners', $data);
        }
        else
        {
            $data['sequence'] = $this->getNextSequence($data['banner_collection_id']);
            CI::db()->insert('banners', $data);
        }
    }
    
    public function save_banner_collection($data)
    {
        if(isset($data['banner_collection_id']) && (bool)$data['banner_collection_id'])
        {
            CI::db()->where('banner_collection_id', $data['banner_collection_id']);
            CI::db()->update('banner_collections', $data);
        }
        else
        {
            CI::db()->insert('banner_collections', $data);
        }
    }
    
    public function get_homepage_banners($limit = false)
    {
        $banners    = CI::db()->order_by('sequence ASC')->get('banners')->result();
        $count  = 1;
        foreach ($banners as &$banner)
        {
            if ($banner->enable_date == '0000-00-00')
            {
                $enable_test    = false;
                $enable         = '';
            }
            else
            {
                $eo             = explode('-', $banner->enable_date);
                $enable_test    = $eo[0].$eo[1].$eo[2];
                $enable         = $eo[1].'-'.$eo[2].'-'.$eo[0];
            }

            if ($banner->disable_date == '0000-00-00')
            {
                $disable_test   = false;
                $disable        = '';
            }
            else
            {
                $do             = explode('-', $banner->disable_date);
                $disable_test   = $do[0].$do[1].$do[2];
                $disable        = $do[1].'-'.$do[2].'-'.$do[0];
            }

            $curDate        = date('Ymd');

            if (($enable_test && $enable_test > $curDate) || ($disable_test && $disable_test <= $curDate))
            {
                unset($banner);
            }
            else
            {
                $count++;
            }
            
            if($limit)
            {
                if($count > $limit)
                {
                    continue;
                }               
            }
        }
        return $banners;
    }
    
    public function delete_banner($banner_id)
    {
        CI::db()->where('banner_id', $banner_id);
        CI::db()->delete('banners');
    }
    
    public function delete_banner_collection($banner_collection_id)
    {
        CI::db()->where('banner_collection_id', $banner_collection_id);
        CI::db()->delete('banners');
        
        CI::db()->where('banner_collection_id', $banner_collection_id);
        CI::db()->delete('banner_collections');
    }
    
    public function getNextSequence($banner_collection_id)
    {
        CI::db()->where('banner_collection_id', $banner_collection_id);
        CI::db()->select('sequence');
        CI::db()->order_by('sequence DESC');
        CI::db()->limit(1);
        $result = CI::db()->get('banners');
        $result = $result->row();
        if ($result)
        {
            return $result->sequence + 1;
        }
        else
        {
            return 0;
        }
    }

    public function organize($banners)
    {
        foreach ($banners as $sequence => $id)
        {
            $data = array('sequence' => $sequence);
            CI::db()->where('banner_id', $id);
            CI::db()->update('banners', $data);
        }
    }

    public function show_collection($banner_collection_id, $quantity=5, $theme='default')
    {
        $data['id'] = $banner_collection_id;
        $data['banners'] = $this->banner_collection_banners($banner_collection_id, true, $quantity);
        

        echo \GoCart\Libraries\View::getInstance()->get('banners/'.$theme, $data);
    }
}