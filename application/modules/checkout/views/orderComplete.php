<div class="page-header">
    <h1><?php echo lang('order_number');?>: <?php echo $order->order_number;?></h1>
</div>

<div class="col-nest">
    <div class="col" data-cols="2/3">

        <?php
        $charges = [];

        $charges['giftCards'] = [];
        $charges['coupons'] = [];
        $charges['tax'] = [];
        $charges['shipping'] = [];
        $charges['products'] = [];

        foreach ($order->items as $item)
        {
            if($item->type == 'gift card')
            {
                $charges['giftCards'][] = $item;
                continue;
            }
            elseif($item->type == 'coupon')
            {
                $charges['coupons'][] = $item;
                continue;
            }
            elseif($item->type == 'tax')
            {
                $charges['tax'][] = $item;
                continue;
            }
            elseif($item->type == 'shipping')
            {
                $charges['shipping'][] = $item;
                continue;
            }
            elseif($item->type == 'product')
            {
                $charges['products'][] = $item;
            }
        }
        ?>

        <?php foreach($charges['products'] as $product):

            $photo = theme_img('no_picture.png', lang('no_image_available'));
            $product->images = array_values(json_decode($product->images, true));

            if(!empty($product->images[0]))
            {
                foreach($product->images as $photo)
                {
                    if(isset($photo['primary']))
                    {
                        $primary = $photo;
                    }
                }
                if(!isset($primary))
                {
                    $tmp = $product->images; //duplicate the array so we don't lose it.
                    $primary = array_shift($tmp);
                }

                $photo = '<img src="'.base_url('uploads/images/full/'.$primary['filename']).'"/>';
            }
            ?>


            <div class="orderItem">
                <div class="col-nest">
                    <div class="col" data-cols="2/3" data-medium-cols="2/3" data-small-cols="2/3">
                        <div class="orderPhoto">
                            <?php echo $photo;?>
                            <?php echo (!empty($product->sku))?'<div class="orderItemSku">'.lang('sku').': '.$product->sku.'</div>':''?>
                        </div>
                        <div class="orderItemDetails">
                            <div class="orderItemName"><?php echo $product->name; ?></div>
                            <div class="orderItemDescription">
                                <?php
                                if(isset($order->options[$product->id])):

                                    foreach($order->options[$product->id] as $option):?>
                                        <div class="orderItemOption"><strong><?php echo ($product->is_giftcard) ? lang('gift_card_'.$option->option_name) : $option->option_name;?> :</strong> <?php echo($option->price > 0)?'['.format_currency($option->price).']':'';?> <?php echo $option->value;?></div>
                                    <?php endforeach;

                                endif;

                                if(isset($order->files[$product->id]))
                                {
                                    foreach($order->files[$product->id] as $file)
                                    {
                                        if($file->max_downloads == 0 || $file->downloads_used < $file->max_downloads)
                                        {
                                            echo '<div class="orderCompleteFileDownload">'.anchor('digital-products/download/'.$file->id.'/'.$file->order_id, '<i class="icon-chevron-down"></i>', 'class="btn input-xs"');
                                            echo ' '.$file->title.' <small>';
                                            if($file->max_downloads > 0)
                                            {
                                                echo ' '.str_replace('{quantity}', ($file->max_downloads - $file->downloads_used), lang('downloads_remaining'));
                                            }
                                            else
                                            {
                                                echo ' '.str_replace('{quantity}', '&infin;', lang('downloads_remaining'));
                                            }
                                            echo '</small></div>';
                                        }
                                    }
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="col" data-cols="1/3" data-medium-cols="1/3" data-small-cols="1/3">
                        <div class="orderPrice">
                            <div class="orderItemQuantity">(<?php echo $product->quantity.'  &times; '.format_currency($product->total_price);?>)</div>
                            <?php if(!empty($product->coupon_code)):?><div class="orderItemCoupon"><?php echo lang('coupon');?> <span class="nowrap"><?php echo '-'.format_currency(($product->coupon_discount * $product->coupon_discount_quantity));?></span></div><?php endif;?>

                            <?php echo format_currency( ($product->total_price * $product->quantity) - ($product->coupon_discount * $product->coupon_discount_quantity) ); ?>
                        </div>
                    </div>
                </div>
            </div>

        <?php endforeach;?>

            <div class="cartSummaryTotals">
                <div class="col-nest">
                    <div class="col" data-cols="2/3" data-medium-cols="2/3" data-small-cols="2/3">
                        <div class="cartSummaryTotalsKey"><?php echo lang('subtotal');?>:</div>
                    </div>
                    <div class="col" data-cols="1/3" data-medium-cols="1/3" data-small-cols="1/3">
                        <div class="orderTotalsValue"><?php echo format_currency($order->subtotal);?></div>
                    </div>
                </div>

                <?php if(count($charges['shipping']) > 0 || count($charges['tax']) > 0 ):?>
                    <?php foreach($charges['shipping'] as $shipping):?>
                        <div class="col-nest">
                            <div class="col" data-cols="2/3" data-medium-cols="2/3" data-small-cols="2/3">
                                <div class="cartSummaryTotalsKey"><?php echo lang('shipping');?>: <?php echo $shipping->name; ?></div>
                            </div>
                            <div class="col" data-cols="1/3" data-medium-cols="1/3" data-small-cols="1/3">
                                <div class="orderTotalsValue"><?php echo format_currency($shipping->total_price); ?></div>
                            </div>
                        </div>
                    <?php endforeach;?>

                    <?php foreach($charges['tax'] as $tax):?>
                        <div class="col-nest">
                            <div class="col" data-cols="2/3" data-medium-cols="2/3" data-small-cols="2/3">
                                <div class="cartSummaryTotalsKey"><?php echo lang('taxes');?>: <?php echo $tax->name; ?></div>
                            </div>
                            <div class="col" data-cols="1/3" data-medium-cols="1/3" data-small-cols="1/3">
                                <div class="orderTotalsValue"><?php echo format_currency($tax->total_price); ?></div>
                            </div>
                        </div>
                    <?php endforeach;?>
                <?php endif;?>

                <?php if(count($charges['giftCards']) > 0):?>

                    <?php foreach($charges['giftCards'] as $giftCard):?>
                        <div class="col-nest">
                            <div class="col" data-cols="2/3" data-medium-cols="2/3" data-small-cols="2/3">
                                <div class="cartSummaryTotalsKey"><?php echo $giftCard->name; ?> : <?php echo $giftCard->description; ?>
                                </div>
                            </div>
                            <div class="col" data-cols="1/3" data-medium-cols="1/3" data-small-cols="1/3" style="white-space:nowrap;">
                                <div class="orderTotalsValue"><?php echo format_currency($giftCard->total_price); ?></div>
                            </div>
                        </div>
                    <?php endforeach;?>
                <?php endif;?>

                <div class="col-nest">
                    <div class="col" data-cols="2/3" data-medium-cols="2/3" data-small-cols="2/3">
                        <div class="cartSummaryTotalsKey"><?php echo lang('grand_total');?>:</div>
                    </div>
                    <div class="col" data-cols="1/3" data-medium-cols="1/3" data-small-cols="1/3">
                        <div class="orderTotalsValue"><?php echo format_currency($order->total);?></div>
                    </div>
                </div>
            </div>
        </div>

    <div class="col" data-cols="1/3">
        <div class="orderAddresses">
            <div class="orderAddressTitle"><?php echo lang('shipping_address');?></div>
            <div class="orderAddress">
            <?php echo format_address([
                'company'=>$order->shipping_company,
                'firstname'=>$order->shipping_firstname,
                'lastname'=>$order->shipping_lastname,
                'phone'=>$order->shipping_phone,
                'email'=>$order->shipping_email,
                'address1'=>$order->shipping_address1,
                'address2'=>$order->shipping_address2,
                'city'=>$order->shipping_city,
                'zone'=>$order->shipping_zone,
                'zip'=>$order->shipping_zip,
                'country_id'=>$order->shipping_country_id
                ]);?>
            </div>
            <div class="orderAddressTitle"><?php echo lang('billing_address');?></div>
            <div class="orderAddress">
            <?php echo format_address([
                'company'=>$order->billing_company,
                'firstname'=>$order->billing_firstname,
                'lastname'=>$order->billing_lastname,
                'phone'=>$order->billing_phone,
                'email'=>$order->billing_email,
                'address1'=>$order->billing_address1,
                'address2'=>$order->billing_address2,
                'city'=>$order->billing_city,
                'zone'=>$order->billing_zone,
                'zip'=>$order->billing_zip,
                'country_id'=>$order->billing_country_id
                ]);?>
            </div>

            <div class="orderAddressTitle"><?php echo lang('payment_information');?></div>
            <div class="orderAddress">
                <?php foreach($order->payments as $payment):?>
                <div><?php echo $payment->description;?></div>
                <?php endforeach;?>
            </div>

        </div>
    </div>
</div>
