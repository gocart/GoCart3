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

//styles
$productCell = 'style="border:1px solid #d6d4d4; padding:10px; color:#555454; vertical-align:top;"';
$productCellPrice = 'style="border:1px solid #d6d4d4; font-weight:bold; padding:10px; color:#555454; vertical-align:top; text-align:right;"';
$productCellOptions = 'style="border:1px solid #d6d4d4; padding:10px; color:#555454; vertical-align:top; font-size:12px;"';
$totalsCell = 'style="background:#f8f8f8; border:1px solid #d6d4d4; color:#333; padding:7px; text-align:right; color:#555454; font-weight:bold;"';
$table = 'style="border-collapse:collapse;width:100%;margin-top:10px; font-size:14px; font-family:open-sans, arial verdana, sans-serif;"';
$addressTitle = 'style="margin:3px 0 7px;text-transform:uppercase;font-weight:500;font-size:18px;"';
$addressCell = 'style="border:1px solid #d6d4d4; padding:10px;background-color:#f8f8f8; color:#555454; vertical-align:top;"';
?>

<div style="font-family:Open-sans, Arial, Verdana, sans-serif; color:#555454; text-transform:uppercase; font-size:17px;">
    <?php echo lang('order_number');?>: <?php echo $order->order_number; ?>
</div>

<table <?php echo $table;?>>
    <tr>
        <td <?php echo $addressCell;?>>
            <div <?php echo $addressTitle;?>> <?php echo lang('billing_address');?> </div>

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
        </td>
        <td <?php echo $addressCell;?>>
            <div <?php echo $addressTitle;?>> <?php echo lang('shipping_address');?> </div>

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
        </td>
    <tr>
    <tr>
        <td colspan="2" <?php echo $addressCell;?>>
            <strong><?php echo lang('payment_information');?></strong>
            <?php foreach($order->payments as $payment):?>
            <div><?php echo $payment->description;?></div>
            <?php endforeach;?>
        </td>
    </tr>
</table>


<table <?php echo $table;?>>
    <tbody>
        <?php foreach($charges['products'] as $product):?>
            <tr>
                <td <?php echo $productCell;?>>
                    <strong><?php echo $product->name; ?></strong> <br>
                    <?php echo (!empty($product->sku))?'<small>'.lang('sku').': '.$product->sku.'</small>':''?>
                </td>
                <td <?php echo $productCellOptions;?>>
                    <?php if(isset($order->options[$product->id])):
                        foreach($order->options[$product->id] as $option):?>
                            <div><strong><?php echo ($product->is_giftcard) ? lang('gift_card_'.$option->option_name) : $option->option_name;?></strong> : <?php echo($option->price > 0)?'['.format_currency($option->price).']':'';?> <?php echo $option->value;?></div>
                        <?php endforeach;
                    endif;?>
                    <?php
                    if(isset($order->files[$product->id]))
                    {
                        foreach($order->files[$product->id] as $file)
                        {
                            echo '<div>'.anchor('digital-products/download/'.$file->id.'/'.$file->order_id, $file->title).'</div>';
                        }
                    }
                    ?>
                </td>
                <td <?php echo $productCellPrice;?>>
                    <div style="font-size:11px; color:#bbb;">(<?php echo $product->quantity.'  &times; '.format_currency($product->total_price);?>)</div>
                    <?php if(!empty($product->coupon_code)):?><div style="color:#990000; font-size:11px;"><?php echo lang('coupon');?>: <?php echo '-'.format_currency(($product->coupon_discount * $product->coupon_discount_quantity));?></div><?php endif;?>
                    <?php echo format_currency( ($product->total_price * $product->quantity) - ($product->coupon_discount * $product->coupon_discount_quantity) ); ?>
                </td>
            </tr>
        <?php endforeach; ?>
        <tr>
            <td colspan="2" <?php echo $totalsCell;?>><?php echo lang('subtotal');?></td>
            <td <?php echo $totalsCell;?>><?php echo format_currency($order->subtotal); ?></td>
        </tr>

        <?php foreach($charges['shipping'] as $shipping):?>
            <tr>
                <td colspan="2" <?php echo $totalsCell;?>>
                    <?php echo lang('shipping');?>: <?php echo $shipping->name; ?>
                </td>
                <td colspan="2" <?php echo $totalsCell;?>>
                    <?php echo format_currency($shipping->total_price); ?>
                </td>
            </tr>
        <?php endforeach;?>

        <?php foreach($charges['tax'] as $tax):?>
            <tr>
                <td colspan="2" <?php echo $totalsCell;?>>
                    <?php echo lang('taxes');?>: <?php echo $tax->name; ?>
                </td>
                <td colspan="2" <?php echo $totalsCell;?>>
                    <?php echo format_currency($tax->total_price); ?>
                </td>
            </tr>
        <?php endforeach;?>

        <?php foreach($charges['giftCards'] as $giftCard):?>
            <tr>
                <td colspan="2" <?php echo $totalsCell;?>>
                    <?php echo $giftCard->name; ?><br>
                    <small>
                        <?php echo $giftCard->description; ?><br>
                        <?php echo $giftCard->excerpt; ?>
                    </small>
                </td>

                <td colspan="2" <?php echo $totalsCell;?>>
                    <?php echo format_currency($giftCard->total_price); ?>
                </td>
            </tr>
        <?php endforeach;?>
        <tr>
            <td colspan="2" <?php echo $totalsCell;?>>
                <div style="font-size:17px;"><?php echo lang('grand_total');?></div>
            </td>
            <td colspan="2" <?php echo $totalsCell;?>>
                <div style="font-size:17px;"><?php echo format_currency($order->total); ?></div>
            </td>
        </tr>
    </tbody>
</table>
