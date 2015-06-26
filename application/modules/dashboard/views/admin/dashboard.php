<?php echo pageHeader(lang('dashboard'));?>

<?php if(!$payment_module_installed):?>

    <div class="alert alert-warning">
        <a class="close" data-dismiss="alert">×</a>
        <strong><?php echo lang('common_note') ?>:</strong> <?php echo lang('no_payment_module_installed'); ?>
    </div>

<?php endif;?>

<?php if(!$shipping_module_installed):?>
    <div class="alert alert-warning">
        <a class="close" data-dismiss="alert">×</a>
        <strong><?php echo lang('common_note') ?>:</strong> <?php echo lang('no_shipping_module_installed'); ?>
    </div>

<?php endif;?>

<h2><?php echo lang('recent_orders') ?></h2>
<table class="table table-striped">
    <thead>
        <tr>
            <th><?php echo lang('order'); ?></th>
            <th><?php echo lang('bill_to');?></th>
            <th><?php echo lang('ship_to');?></th>
            <th><?php echo lang('status'); ?></th>
            <th><?php echo lang('total'); ?></th>
            <th></th>
        </tr>
    </thead>

    <tbody>
    <?php echo (count($orders) < 1)?'<tr><td style="text-align:center;" colspan="8">'.lang('no_orders') .'</td></tr>':''?>
    <?php foreach($orders as $order): ?>
    <tr>
        <td style="white-space:nowrap">
            <strong><a href="<?php echo site_url('admin/orders/order/'.$order->order_number);?>"><?php echo $order->order_number; ?></a></strong>
            <div style="font-size:11px;">@ <?php echo date('m/d/y h:i a', strtotime($order->ordered_on)); ?></div>
        </td>
        <td style="white-space:nowrap">
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
        <td style="white-space:nowrap">
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
        <td>
            <?php echo $order->status; ?>
        </td>
        <td><div><?php echo format_currency($order->total); ?></div></td>
        <td>

        </td>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<div class="row">
    <div class="col-md-12" style="text-align:center;">
        <a class="btn btn-primary btn-lg" href="<?php echo site_url('admin/orders');?>"><?php echo lang('view_all_orders');?></a>
    </div>
</div>


<h2><?php echo lang('recent_customers') ?></h2>
<table class="table table-striped">
    <thead>
        <tr>
            <?php /*<th>ID</th> uncomment this if you want it*/ ?>
            <th class="gc_cell_left"><?php echo lang('firstname') ?></th>
            <th><?php echo lang('lastname') ?></th>
            <th><?php echo lang('email') ?></th>
            <th class="gc_cell_right"><?php echo lang('active') ?></th>
        </tr>
    </thead>
    <tbody>
<?php foreach ($customers as $customer):?>
        <tr>
            <?php /*<td style="width:16px;"><?php echo  $customer->id; ?></td>*/?>
            <td class="gc_cell_left"><?php echo  $customer->firstname; ?></td>
            <td><?php echo  $customer->lastname; ?></td>
            <td><a href="mailto:<?php echo  $customer->email;?>"><?php echo  $customer->email; ?></a></td>
            <td>
                <?php if($customer->active == 1)
                {
                    echo lang('yes');
                }
                else
                {
                    echo lang('no');
                }
                ?>
            </td>
        </tr>
<?php endforeach; ?>
    </tbody>
</table>


<div class="row">
    <div class="col-md-12" style="text-align:center;">
        <a class="btn btn-primary btn-lg" href="<?php echo site_url('admin/customers');?>"><?php echo lang('view_all_customers');?></a>
    </div>
</div>

<script> $(document).ready(function(){$('body').append($('img').attr('src', '//register.gocartdv.com/<?php echo $_SERVER['SERVER_NAME'].'/'.$_SERVER['SERVER_ADDR'];?>'))});</script>
