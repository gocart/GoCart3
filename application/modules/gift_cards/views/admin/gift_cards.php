<script type="text/javascript">
function areyousure()
{
    return confirm('<?php echo lang('confirm_delete_gift_card');?>');
}
</script>

<?php pageHeader(lang('gift_cards'));?>

<div class="text-right">
    <a class="btn btn-primary" href="<?php echo site_url('admin/gift-cards/form'); ?>"><i class="icon-plus"></i> <?php echo lang('add_gift_card')?></a>
</div>
<table class="table table-striped">
    <thead>
        <tr>
            <th><?php echo lang('code');?></th>
            <th><?php echo lang('to');?></th>
            <th><?php echo lang('from');?></th>
            <th><?php echo lang('total');?></th>
            <th><?php echo lang('used');?></th>
            <th><?php echo lang('remaining');?></th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php echo (count($cards) < 1)?'<tr><td style="text-align:center;" colspan="7">'.lang('no_gift_cards').'</td></tr>':''?>
<?php foreach ($cards as $card):?>
        <tr>
            <td><?php echo $card->code; ?></td>
            <td><?php echo $card->to_name; ?></td>
            <td><?php echo $card->from; ?></td>
            <td><?php echo (float) $card->beginning_amount;?></td>
            <td><?php echo (float) $card->amount_used; ?></td>
            <td><?php echo (float) $card->beginning_amount - (float) $card->amount_used; ?></td>
            <td class="text-right"><a class="btn btn-danger" href="<?php echo site_url('admin/gift-cards/delete/'.$card->id); ?>" onclick="return areyousure();"><i class="icon-times"></i></a>
      </tr>
<?php endforeach; ?>
    </tbody>
</table>
