<?php
/**
 * GoCart Class
 *
 * @package     GoCart
 * @subpackage  Library
 * @category    GoCart
 * @author      Clear Sky Designs
 * @link        http://gocartdv.com
 */

class GoCart {

    protected $cart;
    protected $customer;
    protected $items;
    private $groups;

    public function __construct()
    {
        $this->groups = \CI::Customers()->get_groups();
        $this->getCart(true);
    }

    public function saveCart()
    {
        //calculate coupon discounts first
        $this->calculateCouponDiscounts();

        //add up the subtotal (coupon discounts included at line items)
        $this->getSubtotal();
        
        //is the shipping method still valid?
        $this->testShippingMethodValidity();

        //calculate tax based on post-coupon price
        $this->setTaxes();

        //calculate gift card discounts
        $this->calculateGiftCardDiscounts();

        $this->cart->total = $this->getGrandTotal();

        //save the cart information
        CI::Orders()->saveOrder((array)$this->cart);

        //refresh the cart details.
        $this->getCart(true);
    }

    public function addGiftCard($giftCard)
    {
        foreach($this->items as $item)
        {
            if($item->description == $giftCard->code && $item->type == 'gift card')
            {
                return ['success'=>false, 'error'=>lang('gift_card_already_applied')];
            }
        }

        $item = (object)['product_id'=>0, 'shippable'=>0, 'taxable'=>0, 'fixed_quantity'=>1, 'type'=>'gift card', 'name'=>lang('gift_card'), 'price'=>($giftCard->beginning_amount - $giftCard->amount_used), 'description'=>$giftCard->code];
        $this->insertItem(['product'=>$item]);

        return ['success'=>true];
    }

    public function addCoupon($code)
    {
        //get the coupon
        $coupon = \CI::Coupons()->getCouponByCode($code);
        if(!$coupon)
        {
            return json_encode(['error'=>lang('invalid_coupon_code')]);
        }
        //is coupon valid
        if(\CI::Coupons()->isValid($coupon))
        {
            //does the coupon apply to any products?
            if($this->isCouponApplied($coupon))
            {
                return json_encode(['error'=>lang('coupon_already_applied')]);
            }
            else
            {
                $item = (object)['product_id'=>0, 'shippable'=>0, 'track_stock'=>0, 'taxable'=>0, 'fixed_quantity'=>1, 'type'=>'coupon', 'name'=>lang('coupon'), 'price'=>0, 'total_price'=>0, 'description'=>$coupon->code, 'excerpt' => json_encode($coupon)];
                $this->insertItem(['product'=>$item]);
                return json_encode(['message'=>lang('coupon_applied')]);
            }
        }
        else
        {
            return json_encode(['error'=>lang('coupon_invalid')]);
        }
    }
    
    public function isCouponApplied($coupon)
    {
        foreach($this->items as $item)
        {
            if($item->type == 'coupon' && $item->description == $coupon->code)
            {
                return true;
            }
        }
        return false;
    }

    private function getCouponTimesAvailable($coupon)
    {
        $timesAvailable = -1;
        if($coupon->max_uses > 0 && $coupon->max_product_instances > 0)
        {
            $timesAvailable = min( ($coupon->max_uses - $coupon->num_uses),$coupon->max_product_instances ); //maximum times supported per order & max uses in genreral    
        } elseif ($coupon->max_uses == 0 && $coupon->max_product_instances > 0)
        {
            $timesAvailable = $coupon->max_product_instances;
        } elseif($coupon->max_uses > 0 && $coupon->max_product_instances == 0)
        {
            $usesLeft = max($coupon->max_uses - $coupon->num_uses, 0);

            //if there are more than 0 return -1 so the coupon can be used on the whole order
            if($usesLeft == 0)
            {
                $timesAvailable = 0;
            }
        }

        return $timesAvailable;
    }

    private function calculateCouponDiscounts()
    {
        $coupons = [];
        $discounts = [];
        for($i=0; $i<count($this->items); $i++)
        {
            if($this->items[$i]->type == 'coupon')
            {
                $coupons[] = $this->items[$i];
            }
            elseif($this->items[$i]->type == 'product')
            {
                //remove all discounts
                $this->items[$i]->coupon_code = '';
                $this->items[$i]->coupon_discount = '';
                $this->items[$i]->coupon_discount_quantity = '';
            }
        }

        $couponsByCode = [];
        if(count($coupons) > 0)
        {
            foreach($coupons as $code)
            {
                $coupon = \CI::Coupons()->getCouponByCode($code->description);
                $couponsByCode[$coupon->code] = $coupon;

                $timesAvailable = $this->getCouponTimesAvailable($coupon);
                
                //store the timesAvailable with the coupon to access shortly
                $couponsByCode[$coupon->code]->timesAvailable = $timesAvailable;

                for($i=0; $i < count($this->items); $i++)
                {
                    if($coupon->whole_order_coupon || in_array($this->items[$i]->product_id, $coupon->product_list))
                    {
                        if($this->items[$i]->type == 'product')
                        {
                            if($timesAvailable < 0)
                            {
                                $quantity = $this->items[$i]->quantity;
                            }
                            else
                            {
                                $quantity = min($this->items[$i]->quantity, $timesAvailable);
                            }

                            $key = json_encode(['code'=>$coupon->code, 'itemId'=>$this->items[$i]->id]);

                            if($coupon->reduction_type == 'percent')
                            {
                                $percent = ($coupon->reduction_amount/100);
                                $discount = $this->items[$i]->total_price * $percent;

                                $discounts[$key] = $discount * $quantity;
                            }
                            else //fixed
                            {
                                $discounts[$key] = $coupon->reduction_amount * $quantity;
                            }
                        }
                    }
                }
            }
        }

        //sort descending
        arsort($discounts);

        foreach($discounts as $key => $discount)
        {
            $key = json_decode($key);
            
            $code = $key->code;
            $item = $key->itemId;

            $coupon = $couponsByCode[$code];

            for($i=0; $i<count($this->items); $i++)
            {
                if($this->items[$i]->id == $item)
                {
                    if($coupon->timesAvailable < 0)
                    {
                        $quantity = $this->items[$i]->quantity;
                    }
                    else
                    {
                        $quantity = min($this->items[$i]->quantity, $coupon->timesAvailable);
                    }

                    $discount = 0; // reset discount

                    if($coupon->reduction_type == 'percent')
                    {
                        $percent = ($coupon->reduction_amount/100);
                        $discount = $this->items[$i]->total_price * $percent;
                    }
                    else //fixed
                    {
                        $discount = $coupon->reduction_amount;
                    }

                    if(($this->items[$i]->coupon_discount * $this->items[$i]->coupon_discount_quantity) < ($quantity * $discount))
                    {
                        //consider adding the previous availability back to the other coupon
                        if(!empty($this->items[$i]->coupon_code) && $couponsByCode[$this->items[$i]->coupon_code]->timesAvailable >= 0)
                        {
                            $couponsByCode[$this->items[$i]->coupon_code]->timesAvailable += $this->items[$i]->coupon_discount_quantity;
                        }
                        // else could go here but if it's less than 0 it's an infinite use coupon
                        $this->items[$i]->coupon_code = $code;
                        $this->items[$i]->coupon_discount = $discount;
                        $this->items[$i]->coupon_discount_quantity = $quantity;

                        $couponsByCode[$code]->timesAvailable -= $quantity;
                    }
                }        
            }
        }

        //loop through and resave all items.
        for($i=0; $i<count($this->items); $i++)
        {
            if($this->items[$i]->type == 'product')
            {
                $this->insertItem(['product'=>$this->items[$i], 'quantity'=>$this->items[$i]->quantity]);
            }
        }
    }

    private function calculateGiftCardDiscounts()
    {
        $total = 0;

        $giftCards = [];
        foreach($this->items as $item)
        {
            if($item->type != 'gift card') //gift card
            {
                if(isset($item->coupon_discount))
                {
                    $total += ($item->total_price * $item->quantity) - ($item->coupon_discount * $item->coupon_discount_quantity);
                }
                else
                {
                    $total += ($item->total_price * $item->quantity);
                }
            }
            else
            {
                $giftCards[] = $item;
            }
        }

        $total = round($total, 2);
        foreach($giftCards as $giftCard)
        {
            //find out how much can be applied
            if($total > $giftCard->price)
            {
                $giftCard->total_price = -($giftCard->price);
            }
            else
            {
                $giftCard->total_price = -($total);
            }

            // update the "total" so we don't go negative
            $total = $total + $giftCard->total_price;

            //update the item with the new "total_price" and excerpt
            $giftCard->excerpt = lang('gift_card_amount_applied').' '.format_currency($giftCard->total_price).'<br>'.lang('gift_card_amount_remaining').' '.format_currency($giftCard->price+$giftCard->total_price);

            $this->insertItem(['product'=>$giftCard]);
        }
    }

    public function setAttribute($key, $value)
    {
        $this->cart->$key = $value;
    }

    public function getAttribute($key)
    {
        return $this->cart->$key;
    }

    public function getCustomer()
    {
        return $this->customer;
    }

    public function getCart($refresh = false)
    {
        if($refresh)
        {
            $this->customer = CI::Login()->customer();

            $this->cart = CI::Orders()->getCustomerCart($this->customer->id);
            if(!$this->cart)
            {
                //create a new cart
                CI::Orders()->saveOrder(['status' => 'cart', 'customer_id' => $this->customer->id]);
                $this->cart = CI::Orders()->getCustomerCart($this->customer->id);
            }
            $this->getCartItems(true);
        }

        return $this->cart;
    }

    public function getCartItems($refresh = false)
    {
        if($refresh || empty($this->items))
        {
            $this->items = CI::Orders()->getItems($this->cart->id);
        }

        return $this->items;
    }

    public function getCartItem($id)
    {
        foreach($this->items as $item)
        {
            if($item->id == $id)
            {
                return $item;
            }
        }
    }
    private function cleanProduct($product)
    {
        //add some new fields
        $product->product_id = $product->id;
        $product->type = 'product';
        $product->images = json_encode($product->images); //reencode the images

        //remove the following fields
        $remove = ['id', 'primary_category', 'quantity', 'related_products', 'google_feed', 'seo_title', 'meta'];

        foreach($remove as $r)
        {
            unset($product->$r);
        }

        return $product;
    }

    public function insertItem($data=[])
    {
        $product = false;
        $quantity = 1;
        $postedOptions = false;
        $downloads = false;
        $combine = false; //is this an item from a separate cart being combined?

        extract($data);

        if(is_int($product))
        {
            $product = \CI::Products()->getProduct($product);

            if(!$product)
            {
                return json_encode(['error'=>lang('error_product_not_found')]);
            }

            //Clean up the product for the orderItems database
            $product = $this->cleanProduct($product);

            //get downloadable files
            $downloads = \CI::DigitalProducts()->getAssociationsByProduct($product->product_id);
        }

        $update = false;
        if(empty($product->hash))
        {
            $product->hash = md5(json_encode($product).json_encode($postedOptions));

            //set defaults for new items
            $product->coupon_discount = 0;
            $product->coupon_discount_quantity = 0;
            $product->coupon_code = '';
        }
        else
        {
            if(!$combine)
            {
                //this is an update
                $update = true;
            }
            
        }

        $product->order_id = $this->cart->id;

        //loop through the products in the cart and make sure we don't have this in there already. If we do get those quantities as well
        $qty_count = $quantity;

        $this->getCartItems(); // refresh the cart items

        foreach($this->items as $item)
        {
            if(intval($item->product_id) == intval($product->product_id))
            {
                if($item->hash != $product->hash) //if the hashes match, skip this step (this is an update)
                {
                    $qty_count = $qty_count + $item->quantity;
                }

            }

            if($item->hash == $product->hash && !$update) //if this is an update skip this step
            {
                //if the item is already in the cart, send back a message
                return json_encode(['message'=>lang('item_already_added')]);
            }
        }

        if(!config_item('allow_os_purchase') && (bool)$product->track_stock)
        {
            $stock = \CI::Products()->getProduct($product->product_id);

            if($stock->quantity < $qty_count)
            {
                return json_encode(['error'=>sprintf(lang('not_enough_stock'), $stock->name, $stock->quantity)]);
            }
        }

        if (!$quantity || $quantity <= 0 || $product->fixed_quantity==1)
        {
            $product->quantity = 1;
        }
        else
        {
            $product->quantity = $quantity;
        }

        //create save options array here for use later.
        $saveOptions = [];

        if(!$update && $product->product_id) // if not an update or non-product, try and run the options
        {
            //set the base "total_price"
            if($product->saleprice > 0)
            {
                $product->total_price = $product->saleprice;
            }
            else
            {
                $product->total_price = $product->price;
            }

            //set base "total_weight"
            $product->total_weight = $product->weight;

            $productOptions = \CI::ProductOptions()->getProductOptions($product->product_id);

            //option error vars
            $optionError = false;
            $optionErrorMessage = lang('option_error').'<br/>';

            foreach($productOptions as $productOption)
            {
                // are we missing any required values?
                $optionValue = false;
                if(!empty($postedOptions[$productOption->id]))
                {
                    $optionValue = $postedOptions[$productOption->id];
                }

                if((int)$productOption->required && empty($optionValue))
                {
                    $optionError = true;
                    $optionErrorMessage .= "- ". $productOption->name .'<br/>';
                    continue; // don't bother processing this particular option any further
                }

                if(empty($optionValue))
                {
                    //empty? Move along, nothing to see here.
                    continue;
                }

                //create options to save to the database in case we get past the errors
                if($productOption->type == 'checklist')
                {
                    if(is_array($optionValue))
                    {
                        foreach($optionValue as $ov)
                        {
                            foreach($productOption->values as $productOptionValue)
                            {
                                if($productOptionValue->id == $ov)
                                {
                                    $saveOptions[] = [
                                        'option_name'=>$productOption->name,
                                        'value'=>$productOptionValue->value,
                                        'price'=>$productOptionValue->price,
                                        'weight'=>$productOptionValue->weight
                                    ];
                                    $product->total_weight += $productOptionValue->weight;
                                    $product->total_price += $productOptionValue->price;
                                }
                            }
                        }
                    }
                }
                else //every other form type we support
                {
                    $saveOption = [];
                    if($productOption->type == 'textfield' || $productOption->type == 'textarea')
                    {
                        $productOptionValue = $productOption->values[0];
                        $productOptionValue->value = $optionValue;
                    }
                    else //radios and checkboxes
                    {
                        foreach($productOption->values as $ov)
                        {
                            if($ov->id == $optionValue)
                            {
                                $productOptionValue = $ov;
                                break;
                            }
                        }
                        $saveOption['value'] = $optionValue;
                    }
                    if(isset($productOptionValue))
                    {
                        $saveOption['option_name'] = $productOption->name;
                        $saveOption['price'] = $productOptionValue->price;
                        $saveOption['weight'] = $productOptionValue->weight;
                        $saveOption['value'] = $productOptionValue->value;

                        //add it to the array;
                        $saveOptions[] = $saveOption;

                        //update the total weight and price
                        $product->total_weight += $productOptionValue->weight;
                        $product->total_price += $productOptionValue->price;
                    }
                }
            }

            if($optionError)
            {
                return json_encode(['error'=>$optionErrorMessage]);
            }
        }

        //save the product
        $product_id = \CI::Orders()->saveItem((array)$product);

        //save the options if we have them
        foreach($saveOptions as $saveOption)
        {
            $saveOption['order_item_id'] = $product_id;
            $saveOption['order_id'] = $this->cart->id;
            \CI::Orders()->saveItemOption($saveOption);
        }
        if($update)
        {
            foreach($this->items as $key => $item)
            {
                if($item->id == $product_id)
                {
                    $this->items[$key] = $product;
                }
            }
        }
        else
        {
            $product->id = $product_id;
            $this->items[] = $product;


            //update file downloads
            if($downloads)
            {
                foreach($downloads as $file)
                {
                    \CI::Orders()->saveOrderItemFile(['order_id'=>$this->cart->id, 'order_item_id'=>$product->id, 'file_id'=>$file->file_id]);
                }
            }
        }

        //get current item count
        $itemCount = $this->totalItems();

        if($update)
        {
            return json_encode(['message'=>lang('cart_updated'), 'itemCount'=>$itemCount]);
        }
        else
        {
            return json_encode(['message'=>lang('item_added_to_cart'), 'itemCount'=>$itemCount]);
        }
    }

    //check inventory on all products
    public function checkInventory()
    {
        $errors = [];

        //if we do not allow overstock sale, then check stock otherwise return an empty array
        if(!config_item('allow_os_purchase'))
        {
            foreach($this->items as $item)
            {
                if($item->type == 'product')
                {
                    $stock = \CI::Products()->getProduct($item->product_id);
                    if((bool)$stock->track_stock && $stock->quantity < $item->quantity)
                    {
                        if ($stock->quantity < 1)
                        {
                            $errors[$item->id] = lang('this_item_is_out_of_stock'); //completely out of stock.
                        }
                        else
                        {
                            $errors[$item->id] = str_replace('{quantity}', $stock->quantity, lang('not_enough_stock_quantity'));
                        }
                    }
                }
            }
        }
        return $errors;
    }

    function checkCoupons()
    {
        $errors = [];
        $coupons = [];
        for($i=0; $i<count($this->items); $i++)
        {
            if($this->items[$i]->type == 'coupon')
            {
                $coupons[] = $this->items[$i];
            }
        }


        foreach($coupons as $code)
        {
            $coupon = \CI::Coupons()->getCouponByCode($code->description);

            if(!\CI::Coupons()->isValid($coupon))
            {
                //coupon is no longer valid
                $errors[] = json_encode(['error'=>str_replace('{coupon_code}', $code->description,lang('coupon_code_no_longer_valid'))]);
                //remove the coupon
                $this->removeItem($code->id);
            }
        }

        return $errors;
    }

    // double check the order before saving
    public function checkOrder() {
        //start tracking errors
        $errors = [];

        $cart = new stdClass();
        $addresses = \CI::Customers()->get_address_list($this->customer->id);
        foreach($addresses as $address)
        {
            if($address['id'] == $this->cart->shipping_address_id)
            {
                $cart->shippingAddress = (object)$address;
            }
            if($address['id'] == $this->cart->billing_address_id)
            {
                $cart->billingAddress = (object)$address;
            }
        }

        //check shipping
        if($this->orderRequiresShipping())
        {
            if(!$this->getShippingMethod())
            {
                $errors['shipping'] = lang('error_choose_shipping');
            }

            if(empty($cart->shippingAddress))
            {
                $errors['shippingAddress'] = lang('error_shipping_address');
            }
        }

        if(empty($cart->billingAddress))
        {
            $errors['billingAddress'] = lang('error_billing_address');
        }

        //check coupons
        $checkCoupons = $this->checkCoupons();
        if(!empty($checkCoupons))
        {
            $errors['coupons'] = $checkCoupons;
        }

        //check the inventory of our products
        $inventory = $this->checkInventory();
        if(!empty($inventory))
        {
            $errors['inventory'] = $inventory;
        }

        //if we have errors, return them
        if(!empty($errors))
        {
            return $errors;
        }
    }

    function submitOrder($transaction = false)
    {
        foreach ($this->items as $item)
        {
            if($item->type == 'gift card')
            {
                //touch giftcard
                \CI::GiftCards()->updateAmountUsed($item->description, $item->total_price);
                continue;
            }
            elseif($item->type == 'coupon')
            {
                //touch coupon
                \CI::Coupons()->touchCoupon($item->description);
                continue;
            }
            elseif($item->type == 'product')
            {
                //update inventory
                if($item->track_stock)
                {
                    \CI::Products()->touchInventory($item->product_id, $item->quantity);
                }

                //if this is a giftcard purchase, generate it and send it where it needs to go.
                if($item->is_giftcard)
                {
                    //process giftcard
                    $options = CI::Orders()->getItemOptions(GC::getCart()->id);

                    $giftCard = [];
                    foreach($options[$item->id] as $option)
                    {
                        if($option->option_name == 'gift_card_amount')
                        {
                            $giftCard[$option->option_name] = $option->price;
                        }
                        else
                        {
                            $giftCard[$option->option_name] = $option->value;
                        }
                    }
                    $giftCard['code'] = generate_code();
                    $giftCard['activated'] = 1;

                    //save the card
                    \CI::GiftCards()->saveCard($giftCard);

                    //send the gift card notification
                    \GoCart\Emails::giftCardNotification($giftCard);
                }
            }
        }
        if(!$transaction)
        {
            $transaction = $this->transaction();
        }

        //add transaction info to the order
        $this->cart->order_number = $transaction->order_number;
        $this->cart->transaction_id = $transaction->id;
        $this->cart->status = config_item('order_status');
        $this->cart->ordered_on = date('Y-m-d H:i:s');

        $orderNumber = $this->cart->order_number;

        //save order to the database
        CI::Orders()->saveOrder((array)$this->cart);

        //refresh the cart
        $this->getCart(true);

        //get the order as it would be on the order complete page
        $order = \CI::Orders()->getOrder($orderNumber);

        //send the cart email
        \GoCart\Emails::Order($order);

        //return the order number
        return $orderNumber;
    }
    
    public function transaction($transaction = false)
    {
        //no transaction provided? create a new one and return it.
        if(!$transaction)
        {
            $order_number = str_replace('.', '-', microtime(true)).$this->cart->id;
            $transaction = [
                'order_id' => $this->cart->id,
                'order_number' => $order_number,
                'created_at'=>date('Y-m-d H:i:s')
            ];

            \CI::db()->insert('transactions', $transaction);
            $transaction['id'] = \CI::db()->insert_id();

            return (object)$transaction;    
        }
        else
        {
            //we have a transaction, update it with the response
            \CI::db()->where('id',$transaction->id)->update('transactions', (array)$transaction);
        }
    }
    
    public function getTaxableTotal()
    {
        $total = 0;
        foreach($this->items as $item)
        {
            if($item->taxable)
            {
                if(isset($item->coupon_discount))
                {
                    $total += ($item->total_price * $item->quantity) - ($item->coupon_discount * $item->coupon_discount_quantity);
                }
                else
                {
                    $total += ($item->total_price * $item->quantity);
                }
            }
        }

        return round($total, 2);
    }

    public function getSubtotal()
    {
        $total = 0;
        foreach($this->items as $item)
        {
            if($item->type == 'product')
            {
                $total += ($item->total_price * $item->quantity) - ($item->coupon_discount * $item->coupon_discount_quantity);
            }
        }
        $total = round($total, 2);

        $this->cart->subtotal = $total;
        return $total;
    }

    public function getTotalWeight()
    {
        $total = 0;
        foreach($this->items as $item)
        {
            if($item->type == 'product')
            {
                $total += ($item->total_weight * $item->quantity);
            }
        }

        return $total;
    }

    public function getGrandTotal()
    {
        $total = 0;
        foreach($this->items as $item)
        {
            if(isset($item->coupon_discount))
            {
                $math = ($item->total_price * $item->quantity) - ($item->coupon_discount * $item->coupon_discount_quantity);
            }
            else
            {
                $math = ($item->total_price * $item->quantity);
            }
            $total = $total+$math;
        }
        $total = round($total, 2);
        //$this->cart->grandtotal = $total;
        return $total;
    }

    public function setTaxes()
    {
        $tax = CI::Tax()->getTaxes();

        //remove any existing tax charges
        $this->removeItemsOfType('tax');

        if($tax > 0)
        {
            $item = (object)['product_id'=>0, 'shippable'=>0, 'taxable'=>0, 'track_stock'=>0, 'fixed_quantity'=>1, 'type'=>'tax', 'name'=>lang('taxes'), 'total_price'=>$tax];
            $this->insertItem(['product'=>$item]);
        }
    }

    public function setShippingMethod($key, $rate, $hash)
    {
        //first remove any existing shipping methods
        $this->removeItemsOfType('shipping');

        $shipping = (object)['product_id'=>0, 'shippable'=>0, 'taxable'=>0, 'fixed_quantity'=>1, 'type'=>'shipping', 'name'=>$key, 'total_price'=>$rate, 'description'=>$hash];

        if(config_item('tax_shipping'))
        {
            $shipping->taxable = 1;
        }

        $this->insertItem(['product'=>$shipping, 'recalcuateShipping'=>false]);
    }

    public function getShippingMethod()
    {
        foreach($this->items as $item)
        {
            if($item->type == 'shipping')
            {
                return $item;
            }
        }
        return false;
    }

    public function orderRequiresShipping()
    {
        foreach($this->items as $item)
        {
            if((bool)$item->shippable)
            {
                return true;
            }
        }
        return false;
    }

    public function getShippingMethodOptions()
    {
        global $shippingModules;

        $rates = [];
        foreach($shippingModules as $shippingModule)
        {
            $className = '\GoCart\Controller\\'.$shippingModule['class'];
            $rates = $rates+(new $className)->rates();
        }
        return $rates;
    }

    public function testShippingMethodValidity()
    {
        if(!$this->orderRequiresShipping())
        {
            //if shipping is not required, then remove any shipping methods.
            $this->removeItemsOfType('shipping');
        }
        else
        {
            $shippingExists = false;
            $shippingMethod = $this->getShippingMethod();
            if(is_object($shippingMethod))
            {
                $shippingMethods = $this->getShippingMethodOptions();
                foreach($shippingMethods as $key=>$rate)
                {
                    $hash = md5( json_encode(['key'=>$key, 'rate'=>$rate]) );
                    if($hash == $shippingMethod->description)
                    {
                        $shippingExists = true;
                    }
                }
            }
            if(!$shippingExists)
            {
                $this->removeItemsOfType('shipping');
            }
        }
    }

    public function removeItemsOfType($type)
    {
        foreach($this->items as $item)
        {
            if($item->type == $type)
            {
                $this->removeItem($item->id);
            }
        }
    }

    public function removeItem($id)
    {
        CI::Orders()->removeItem($this->cart->id, $id);

        for($i=0; $i < count($this->items); $i++)
        {
            if($this->items[$i]->id == $id)
            {
                unset($this->items[$i]);
            }
        }

        //reset the array keys
        $this->items = array_values($this->items);
    }

    public function totalItems()
    {
        $count = 0;

        foreach($this->items as $item)
        {
            if($item->type == 'product')
            {
                $count += $item->quantity;
            }
        }

        return $count;
    }

    //if a long time has passed, or the customer logs in, reprice items
    public function repriceItems()
    {
        $this->getCart(true); // refresh the cart and items again.

        $options = CI::Orders()->getItemOptions(GC::getCart()->id);

        foreach($this->items as $item)
        {

            if($item->type == 'product')
            {

                //grab the product from the database
                $product = \CI::Products()->getProduct($item->product_id);
                
                if(empty($product))
                {
                    //product can no longer be found. remove it
                    $this->removeItem($item->id);
                    continue;
                }

                //Clean up the product for the orderItems database
                $product = $this->cleanProduct($product);

                $totalPrice = 0;

                if($product->{'saleprice_'.$this->customer->group_id} > 0)
                {
                    //if it's on sale, give it the sale price
                    $totalPrice = $product->{'saleprice_'.$this->customer->group_id};
                }
                else
                {
                    //not on sale give it the normal price
                    $totalPrice = $product->{'price_'.$this->customer->group_id};
                }

                if(isset($options[$item->id]))
                {
                    foreach($options[$item->id] as $option)
                    {
                        $totalPrice += $option->price;
                    }
                }
                
                $product->id = $item->id;
                $product->hash = $item->hash;

                $product->total_price = $totalPrice; //updated price

                \CI::Orders()->saveItem((array)$product);
            }
        }
        $this->getCart(true); // refresh the cart and items just one more time.
    }

    //method used to combine a users cart when they login.
    public function combineCart($customer)
    {
        //current cart / items
        $oldCart = CI::Orders()->getCustomerCart($customer->id);
        
        //get the new cart in place
        $this->getCart(true);

        //move the options to the new order
        CI::Orders()->moveOrderItems($oldCart->id, $this->cart->id);

        //delete oldCart if it exists
        CI::Orders()->delete($oldCart->id);

        $this->repriceItems();
    }
}
