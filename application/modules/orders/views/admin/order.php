<div class="page-header">
    <h1><?php echo lang('order');?>: <?php echo $order->order_number;?></h1>
</div>
<div class="row">
    <div class="col-md-6">
        <a class="btn btn-primary" href="<?php echo site_url('admin/orders/packing_slip/'.$order->order_number);?>" target="_blank"><i class="icon-file"></i> <?php echo lang('packing_slip');?></a>
    </div>
    <div class="col-md-6 text-right">
        <a class="btn btn-danger" onclick="if(!confirm('<?php echo lang('confirm_delete_order');?>')) { return false; }" href="<?php echo site_url('admin/orders/delete/'.$order->id);?>" target="_blank"><i class="icon-cancel"></i> <?php echo lang('delete');?></a>
    </div>
</div>

<div style="margin:10px 0px;">
    <div class="row">

        <div class="col-md-3">
            <h3><?php echo lang('shipping_address');?></h3>
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
        <div class="col-md-3">
            <h3><?php echo lang('billing_address');?></h3>
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
        <div class="col-md-3">
            <h3><?php echo lang('payment_method');?></h3>
            <?php foreach($order->payments as $payment):?>
                <div><?php echo $payment->description;?></div>
            <?php endforeach;?>
        </div>
        <div class="col-md-3">
            <?php echo form_open('admin/orders/order/'.$order->order_number);?>

                <div class="form-group">
                    <label><?php echo lang('admin_notes');?></label>
                    <?php echo form_textarea(['name'=>'notes', 'class'=>'form-control', 'rows'=>2, 'value'=>set_value('notes', $order->notes)]);?>
                </div>
                <div class="form-group">
                    <label><?php echo lang('status');?></label>
                    <div class="input-group">
        				<?php echo form_input(['id'=>'status_form_'.$order->id, 'name'=>'status', 'class'=>'form-control', 'value'=>set_value('status',$order->status)]);?>
        				<div class="input-group-btn">
        					<button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
        						<span class="caret"></span>
        						<span class="sr-only">Toggle Dropdown</span>
        					</button>
        					<ul class="dropdown-menu dropdown-menu-right" role="menu">
        						<?php foreach(config_item('order_statuses') as $os):?>
        							<li><a onclick="$('#status_form_<?php echo $order->id;?>').val('<?php echo $os;?>'); return false;"><?php echo $os;?></a></li>
        						<?php endforeach;?>
        					</ul>
        				</div>
        			</div>
                </div>

                <input type="submit" class="btn btn-primary" value="<?php echo lang('update_order');?>"/>

            </form>
        </div>
    </div>
</div>

<h3><?php echo lang('order_items');?></h3>
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

<table class="table">
    <tbody class="orderItems">
        <?php foreach($charges['products'] as $product):?>
            <tr>
                <td>
                    <strong><?php echo $product->name; ?></strong> <br>
                    <?php echo (!empty($product->sku))?'<small>'.lang('sku').': '.$product->sku.'</small>':''?>
                </td>
                <td>
                    <?php if(isset($order->options[$product->id])):
                        foreach($order->options[$product->id] as $option):?>
                            <div><strong><?php echo ($product->is_giftcard) ? lang('gift_card_'.$option->option_name) : $option->option_name;?></strong> : <?php echo($option->price > 0)?'['.format_currency($option->price).']':'';?> <?php echo $option->value;?></div>
                        <?php endforeach;
                    endif;?>
                </td>
                <td>
                    <div style="font-size:11px; color:#bbb;">(<?php echo $product->quantity.'  &times; '.format_currency($product->total_price);?>)</div>
                    <?php if(!empty($product->coupon_code)):?><div style="color:#990000; font-size:11px;"><?php echo lang('coupon');?>: <?php echo '-'.format_currency(($product->coupon_discount * $product->coupon_discount_quantity));?></div><?php endif;?>
                    <?php echo format_currency( ($product->total_price * $product->quantity) - ($product->coupon_discount * $product->coupon_discount_quantity) ); ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
    <tbody class="orderTotals">
        <tr>
            <td colspan="2"><?php echo lang('subtotal');?></td>
            <td><?php echo format_currency($order->subtotal); ?></td>
        </tr>

        <?php foreach($charges['shipping'] as $shipping):?>
            <tr>
                <td colspan="2">
                    <?php echo lang('shipping');?>: <?php echo $shipping->name; ?>
                </td>
                <td colspan="2">
                    <?php echo format_currency($shipping->total_price); ?>
                </td>
            </tr>
        <?php endforeach;?>

        <?php foreach($charges['tax'] as $tax):?>
            <tr>
                <td colspan="2">
                    <?php echo $tax->name; ?>
                </td>
                <td colspan="2">
                    <?php echo format_currency($tax->total_price); ?>
                </td>
            </tr>
        <?php endforeach;?>

        <?php foreach($charges['giftCards'] as $giftCard):?>
            <tr>
                <td colspan="2">
                    <?php echo $giftCard->name; ?><br>
                    <small>
                        <?php echo $giftCard->description; ?><br>
                        <?php echo $giftCard->excerpt; ?>
                    </small>
                </td>

                <td colspan="2">
                    <?php echo format_currency($giftCard->total_price); ?>
                </td>
            </tr>
        <?php endforeach;?>
        <tr>
            <td colspan="2">
                <div style="font-size:17px;"><?php echo lang('total');?></div>
            </td>
            <td colspan="2">
                <div style="font-size:17px;"><?php echo format_currency($order->total); ?></div>
            </td>
        </tr>
    </tbody>
</table>
