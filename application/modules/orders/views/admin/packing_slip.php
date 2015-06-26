<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

</head>

<body>

<?php
$charges['products'] = [];
$charges['shipping'] = [];

foreach ($order->items as $item)
{
    if($item->type == 'shipping')
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

<div style="font-size:12px; font-family:arial, verdana, sans-serif;">

    <h2><?php echo $order->order_number;?></h2>

    <table style="border:1px solid #000; width:100%; font-size:13px;" cellpadding="5" cellspacing="0">
        <tr>
            <td style="width:40%; vertical-align:top;">
                <strong><?php echo lang('bill_to_address');?></strong><br/>
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
            <td style="width:40%; vertical-align:top;" class="packing">
                <strong><?php echo lang('ship_to_address');?></strong><br/>
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

            <br/>
            </td>
        </tr>

        <tr>
            <td style="border-top:1px solid #000;">
                <strong><?php echo lang('payment_method');?></strong>
                <?php foreach($order->payments as $payment):?>
                    <div><?php echo $payment->description;?></div>
                <?php endforeach;?>
            </td>
            <td style="border-top:1px solid #000;">
                <strong><?php echo lang('shipping');?> </strong>
                <?php foreach($charges['shipping'] as $shipping):?>
                    <div>
                        <?php echo $shipping->name; ?>
                    </div>
                <?php endforeach;?>
            </td>
        </tr>

        <?php if(!empty($order->gift_message)):?>
        <tr>
            <td colspan="3" style="border-top:1px solid #000;">
                <strong><?php echo lang('gift_note');?></strong>
                <?php echo $order->gift_message;?>
            </td>
        </tr>
        <?php endif;?>

        <?php if(!empty($order->shipping_notes)):?>
            <tr>
                <td colspan="3" style="border-top:1px solid #000;">
                    <strong><?php echo lang('shipping_notes');?></strong><br/><?php echo $order->shipping_notes;?>
                </td>
            </tr>
        <?php endif;?>
    </table>

    <table border="1" style="width:100%; margin-top:10px; border-color:#000; font-size:13px; border-collapse:collapse;" cellpadding="5" cellspacing="0">
        <thead>
            <tr>
                <th width="5%" class="packing">
                    <?php echo lang('qty');?>
                </th>
                <th width="20%" class="packing">
                    <?php echo lang('name');?>
                </th>
                <th class="packing" >
                    <?php echo lang('description');?>
                </th>
            </tr>
        </thead>
        <tbody>
        <?php foreach($charges['products'] as $product):?>
            <tr>
                <td style="text-align:center; font-size:20px; font-weight:bold;"><?php echo $product->quantity;?></td>
                <td>
                    <strong><?php echo $product->name; ?></strong> <br>
                    <?php echo (!empty($product->sku))?'<small>'.lang('sku').': '.$product->sku.'</small>':''?>
                </td>
                <td>
                    <?php if(isset($order->options[$product->id])):
                        foreach($order->options[$product->id] as $option):?>
                            <div><strong><?php echo ($product->is_giftcard) ? lang('gift_card_'.$option->option_name) : $option->option_name;?></strong> : <?php echo $option->value;?></div>
                        <?php endforeach;
                    endif;?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
