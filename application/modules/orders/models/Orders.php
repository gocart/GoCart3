<?php
Class Orders extends CI_Model
{

    private function arrangeSalesFigures($values)
    {
        $return = [];
        foreach($values as $val)
        {
            $return[$val->month] = $val->total;
        }
        return $return;
    }
    public function getGrossMonthlySales($year)
    {
        $reports = [];
        $products = CI::db()->select('MONTH(ordered_on) as month, sum('.\CI::db()->dbprefix('order_items').'.total_price * '.\CI::db()->dbprefix('order_items').'.quantity - '.\CI::db()->dbprefix('order_items').'.coupon_discount * '.\CI::db()->dbprefix('order_items').'.coupon_discount_quantity) as total')
        ->join('order_items', 'order_items.order_id = orders.id')
        ->where('status !=', 'cart')
        ->where('order_items.type', 'product')
        ->where('YEAR(ordered_on)', $year)
        ->group_by(['MONTH(ordered_on)'])
        ->order_by("ordered_on", "desc")
        ->get('orders')->result();
        $reports['products'] = $this->arrangeSalesFigures($products);

        $couponDiscounts = CI::db()->select('MONTH(ordered_on) as month, sum('.\CI::db()->dbprefix('order_items').'.coupon_discount * '.\CI::db()->dbprefix('order_items').'.coupon_discount_quantity) as total')
        ->join('order_items', 'order_items.order_id = orders.id')
        ->where('status !=', 'cart')
        ->where('order_items.type', 'product')
        ->where('YEAR(ordered_on)', $year)
        ->group_by(['MONTH(ordered_on)'])
        ->order_by("ordered_on", "desc")
        ->get('orders')->result();

        $reports['couponDiscounts'] = $this->arrangeSalesFigures($couponDiscounts);

        $giftCardDiscounts = CI::db()->select('MONTH(ordered_on) as month, sum('.\CI::db()->dbprefix('order_items').'.total_price) as total')
        ->join('order_items', 'order_items.order_id = orders.id')
        ->where('status !=', 'cart')
        ->where('order_items.type', 'gift card')
        ->where('YEAR(ordered_on)', $year)
        ->group_by(['MONTH(ordered_on)'])
        ->order_by("ordered_on", "desc")
        ->get('orders')->result();

        $reports['giftCardDiscounts'] = $this->arrangeSalesFigures($giftCardDiscounts);

        $shipping = CI::db()->select('MONTH(ordered_on) as month, sum('.\CI::db()->dbprefix('order_items').'.total_price) as total')
        ->join('order_items', 'order_items.order_id = orders.id')
        ->where('status !=', 'cart')
        ->where('order_items.type', 'shipping')
        ->where('YEAR(ordered_on)', $year)
        ->group_by(['MONTH(ordered_on)'])
        ->order_by("ordered_on", "desc")
        ->get('orders')->result();
        $reports['shipping'] = $this->arrangeSalesFigures($shipping);

        $tax = CI::db()->select('MONTH(ordered_on) as month, sum('.\CI::db()->dbprefix('order_items').'.total_price) as total')
        ->join('order_items', 'order_items.order_id = orders.id')
        ->where('status !=', 'cart')
        ->where('order_items.type', 'shipping')
        ->where('YEAR(ordered_on)', $year)
        ->group_by(['MONTH(ordered_on)'])
        ->order_by("ordered_on", "desc")
        ->get('orders')->result();
        $reports['tax'] = $this->arrangeSalesFigures($tax);

        return $reports;
    }

    public function getSalesYears()
    {
        CI::db()->where('status !=', 'cart');
        CI::db()->order_by("ordered_on", "desc");
        CI::db()->select('YEAR(ordered_on) as year');
        CI::db()->group_by('YEAR(ordered_on)');
        $records = CI::db()->get('orders')->result();
        $years = [];
        foreach($records as $r)
        {
            $years[] = $r->year;
        }
        return $years;
    }

    private function getAddressSelect()
    {
        $fields = \CI::db()->list_fields('customers_address_bank');
        $select = '';
        foreach($fields as $field)
        {
            $select .= ', shipping.'.$field.' as shipping_'.$field.', billing.'.$field.' as billing_'.$field.' ';
        }

        return $select;
    }

    private function getOrderSearchLike($str)
    {
        //support multiple words
        $term = explode(' ', $str);

        foreach($term as $t)
        {
            $not = '';
            $operator = 'OR';
            if(substr($t,0,1) == '-')
            {
                $not = 'NOT ';
                $operator = 'AND';
                //trim the - sign off
                $t = substr($t,1,strlen($t));
            }

            $like = '';
            $like .= "( `order_number` ".$not."LIKE '%".CI::db()->escape_like_str($t)."%' " ;
            $like .= $operator." `billing`.`firstname` ".$not."LIKE '%".CI::db()->escape_like_str($t)."%'  ";
            $like .= $operator." `billing`.`lastname` ".$not."LIKE '%".CI::db()->escape_like_str($t)."%'  ";
            $like .= $operator." `shipping`.`firstname` ".$not."LIKE '%".CI::db()->escape_like_str($t)."%'  ";
            $like .= $operator." `shipping`.`lastname` ".$not."LIKE '%".CI::db()->escape_like_str($t)."%'  ";
            $like .= $operator." `status` ".$not."LIKE '%".CI::db()->escape_like_str($t)."%' ";
            $like .= $operator." `notes` ".$not."LIKE '%".CI::db()->escape_like_str($t)."%' )";

            CI::db()->where($like);
        }
    }

    public function getOrders($search=false, $sort_by='', $sort_order='DESC', $limit=0, $offset=0)
    {
        $select = 'orders.*'.$this->getAddressSelect();

        //\CI::db()->select($select)->join('customers_address_bank as shipping', 'shipping.id = orders.shipping_address_id', 'left')->join('customers_address_bank as billing', 'billing.id = orders.billing_address_id', 'left');

        \CI::db()->select($select)->join('customers_address_bank as shipping', 'shipping.id = orders.shipping_address_id', 'left');
        \CI::db()->join('customers_address_bank as billing', 'billing.id = orders.billing_address_id', 'left');

        if ($search)
        {
            if(!empty($search->term))
            {
                $this->getOrderSearchLike($search->term);
            }
            if(!empty($search->start_date))
            {
                CI::db()->where('ordered_on >=',$search->start_date);
            }
            if(!empty($search->end_date))
            {
                //increase by 1 day to make this include the final day
                //I tried <= but it did not public function. Any ideas why?
                $search->end_date = date('Y-m-d', strtotime($search->end_date)+86400);
                CI::db()->where('ordered_on <',$search->end_date);
            }
        }



        if($limit>0)
        {
            CI::db()->limit($limit, $offset);
        }
        if(!empty($sort_by))
        {
            CI::db()->order_by($sort_by, $sort_order);
        }

        CI::db()->where('status !=', 'cart');

        return CI::db()->get('orders')->result();
    }

    public function getOrderCount($search=false)
    {

        \CI::db()->join('customers_address_bank as shipping', 'shipping.id = orders.shipping_address_id', 'left');
        \CI::db()->join('customers_address_bank as billing', 'billing.id = orders.billing_address_id', 'left');

        if ($search)
        {
            if(!empty($search->term))
            {
                $this->getOrderSearchLike($search->term);
            }
            if(!empty($search->start_date))
            {
                CI::db()->where('ordered_on >=',$search->start_date);
            }
            if(!empty($search->end_date))
            {
                CI::db()->where('ordered_on <',$search->end_date);
            }
        }

        return CI::db()->where('status !=', 'cart')->count_all_results('orders');
    }

    //get an individual customers orders
    public function getCustomerOrders($id, $offset=0)
    {
        CI::db()->order_by('ordered_on', 'DESC');
        CI::db()->where(['customer_id' => $id, 'status !=' => 'cart']);

        return CI::db()->get('orders')->result();
    }

    public function getCustomerCart($customerID)
    {
        CI::db()->where('status', 'cart');
        CI::db()->where('customer_id', $customerID);
        return CI::db()->get('orders')->row();
    }

    public function countCustomerOrders($id)
    {
        CI::db()->where(['customer_id' => $id, 'status !=' => 'cart']);
        return CI::db()->count_all_results('orders');
    }

    public function getOrder($orderNumber)
    {
        $fields = \CI::db()->list_fields('customers_address_bank');
        $select = 'orders.*, customers.*, orders.id as id ';
        foreach($fields as $field)
        {
            $select .= ', shipping.'.$field.' as shipping_'.$field.', billing.'.$field.' as billing_'.$field.' ';
        }
        \CI::db()->select($select)->join('customers', 'customers.id = orders.customer_id', 'left')->join('customers_address_bank as shipping', 'shipping.id = orders.shipping_address_id', 'left')->join('customers_address_bank as billing', 'billing.id = orders.billing_address_id', 'left');
        \CI::db()->where('order_number', $orderNumber);

        $result = \CI::db()->get('orders');
        $order = $result->row();

        if(!$order)
        {
            return false;
        }
        $order->items = $this->getItems($order->id);
        $order->options = $this->getItemOptions($order->id);
        $order->files = $this->getItemFiles($order->id);
        $order->payments = $this->getPaymentInfo($order->id);

        return $order;
    }

    public function getItems($id)
    {
        CI::db()->where('order_id', $id)->order_by('type', 'ASC')->order_by('id', 'ASC');
        $items = CI::db()->get('order_items')->result();

        return $items;
    }

    public function getItemFiles($id)
    {
        $files = CI::db()->select('*, order_item_files.id as id')->where('order_id', $id)->join('digital_products', 'digital_products.id = order_item_files.file_id')->get('order_item_files')->result();

        $return = [];
        foreach($files as $file)
        {   
            if(!isset($return[$file->order_item_id]))
            {
                $return[$file->order_item_id] = [];
            }
            
            $return[$file->order_item_id][] = $file;
        }

        return $return;
    }

    public function getItemOptions($order_id)
    {
        $optionValues = CI::db()->where('order_id', $order_id)->get('order_item_options')->result();

        $return =[];

        foreach($optionValues as $optionValue)
        {
            if(!isset($return[$optionValue->order_item_id]))
            {
                $return[$optionValue->order_item_id] = [];
            }
            $return[$optionValue->order_item_id][] = $optionValue;
        }

        return $return;
    }

    public function removeItem($order_id, $id)
    {
        CI::db()->where('order_id', $order_id)->where('id', $id)->delete('order_items');
        CI::db()->where('order_item_id', $id)->delete('order_item_files');
        CI::db()->where('order_item_id', $id)->delete('order_item_options');
    }

    function saveOrderItemFile($file)
    {
        if(!empty($file['id']))
        {
            CI::db()->where('id', $file['id']);
            CI::db()->update('order_item_files', $file);
        }
        else
        {
            CI::db()->insert('order_item_files', $file);
        }
    }

    function getOrderItemFile($orderId)
    {
        return CI::db()->where('order_id', $orderId)->get('order_item_files')->result();
    }

    public function delete($id)
    {
        CI::db()->where('id', $id);
        CI::db()->delete('orders');

        //now delete the order items
        CI::db()->where('order_id', $id);
        CI::db()->delete('order_items');

        CI::db()->where('order_id', $id);
        CI::db()->delete('order_item_options');

        CI::db()->where('order_id', $id);
        CI::db()->delete('order_item_files');
    }

    public function saveItem($data)
    {
        if (isset($data['id']))
        {
            CI::db()->where('id', $data['id']);
            CI::db()->update('order_items', $data);
            return $data['id'];
        }
        else
        {
            CI::db()->insert('order_items', $data);
            return CI::db()->insert_id();
        }
    }

    public function saveItemOption($data)
    {
        if (isset($data['id']))
        {
            CI::db()->where('id', $data['id']);
            CI::db()->update('order_item_options', $data);
            return $data['id'];
        }
        else
        {
            CI::db()->insert('order_item_options', $data);
            return CI::db()->insert_id();
        }
    }

    public function moveOrderItems($oldId, $newId)
    {
        //move order items
        CI::db()->where('order_id', $oldId)->set('order_id',$newId)->update('order_items');

        //move order item options
        CI::db()->where('order_id', $oldId)->set('order_id',$newId)->update('order_item_options');
    }

    public function savePaymentInfo($info)
    {
        CI::db()->insert('payments', $info);
    }

    public function getPaymentInfo($order_id)
    {
        return CI::db()->where('order_id', $order_id)->where('status !=', 'failed')->get('payments')->result();
    }

    public function saveOrder($data, $contents = false)
    {
        if (isset($data['id']))
        {
            CI::db()->where('id', $data['id']);
            CI::db()->update('orders', $data);
            $id = $data['id'];
        }
        else
        {
            CI::db()->insert('orders', $data);
            $id = CI::db()->insert_id();
        }

        //if there are items being submitted with this order add them now
        if($contents)
        {
            // clear existing order items
            CI::db()->where('order_id', $id)->delete('order_items');
            // update order items
            foreach($contents as $item)
            {
                $save = [];
                $save['contents'] = $item;

                $item = unserialize($item);
                $save['product_id'] = $item['id'];
                $save['quantity'] = $item['quantity'];
                $save['order_id'] = $id;
                CI::db()->insert('order_items', $save);
            }
        }
        return $id;
    }

    public function getBestSellers($start, $end)
    {
        if(!empty($start))
        {
            CI::db()->where('ordered_on >=', $start);
        }
        if(!empty($end))
        {
            CI::db()->where('ordered_on <',  $end);
        }

        // just fetch a list of order id's
        $orders = CI::db()->select('sum(quantity) as quantity_sold, order_items.name as name, sku')->group_by('product_id')->order_by('quantity_sold', 'DESC')->where('status !=','cart')->where('order_items.type', 'product')->join('order_items', 'order_items.order_id = orders.id')->get('orders')->result();

        return $orders;
    }

}
