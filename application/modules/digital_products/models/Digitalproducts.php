<?php

Class DigitalProducts extends CI_Model {

    // Return blank record array
    public function newFile()
    {
        return [
                'id'=>'',
                'filename'=>'',
                'max_downloads'=>'',
                'title'=>'',
                'description'=>'',
                'size'=>''
                ];
    }

    // Get files list
    public function getList()
    {
        $list = CI::db()->get('digital_products')->result();

        foreach($list as &$file)
        {
            // identify if the record is missing it's file content
            $file->verified = $this->verifyContent($file->filename);
        }

        return $list;
    }

    // Get file record
    public function getFileInfo($id)
    {
        return CI::db()->where('id', $id)->get('digital_products')->row();
    }

    // Verify upload path
    public function verifyFilePath()
    {
        return is_writable('uploads/digital_products');
    }

    // Verify file content
    public function verifyContent($filename)
    {
        return file_exists('uploads/digital_products/'.$filename);
    }

    // Verify file content
    public function downloadFile($filename)
    {
        if($this->verifyContent($filename))
        {
            \CI::load()->helper('download');
            force_download($filename, file_get_contents('uploads/digital_products/'.$filename));
        }
        else
        {
            throw_404();
        }
    }

    // Save/Update file record
    public function save($data)
    {
        if(isset($data['id']))
        {
            CI::db()->where('id', $data['id'])->update('digital_products', $data);
            return $data['id'];
        } else {
            CI::db()->insert('digital_products', $data);
            return CI::db()->insert_id();
        }
    }

    // Add product association
    public function associate($file_id, $product_id)
    {
        CI::db()->insert('products_files', ['product_id'=>$product_id, 'file_id'=>$file_id]);
    }

    // Remove product association (all or by product)
    public function disassociate($file_id, $product_id=false)
    {

        if($product_id)
        {
            $data['product_id'] = $product_id;
        }
        if($file_id)
        {
            $data['file_id']    = $file_id;
        }
        CI::db()->where($data)->delete('products_files');
    }

    public function getAssociationsByFile($id)
    {
        return CI::db()->where('file_id', $id)->get('products_files')->result();
    }

    public function getAssociationsByProduct($product_id)
    {
        return CI::db()->where('product_id', $product_id)->get('products_files')->result();
    }

    // Delete file record & content
    public function delete($id)
    {
        CI::load()->model('Products');

        $info = $this->getFileInfo($id);

        if(!$info)
        {
            return false;
        }

        // remove file
        if($this->verifyContent($info->filename))
        {
            unlink('uploads/digital_products/'.$info->filename);
        }

        // Remove db associations
        CI::db()->where('id', $id)->delete('digital_products');
        $this->disassociate($id);

        //remove the item from orders that have a connection to this file
        CI::db()->where('file_id', $id)->delete('order_item_files');
    }


    public function touchDownload($file_id)
    {
        CI::db()->where('id', $file_id)->set('downloads_used','downloads_used+1', false)->update('order_item_files');
    }
}
