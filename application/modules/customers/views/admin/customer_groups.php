<?php pageHeader(lang('customer_groups'));?>

<script type="text/javascript">
function areyousure()
{
    return confirm('<?php echo lang('confirm_delete_group');?>');
}

</script>

<a class="btn btn-primary" style="float:right;" href="<?php echo site_url('admin/customers/group_form'); ?>"><i class="icon-plus"></i> <?php echo lang('add_new_group');?></a>
    
<table class="table table-striped">
    <thead>
        <tr>
            <th><?php echo lang('group_name');?></th>
            <th></th>
        </tr>
    </thead>
    <tbody>
    
    <?php foreach ($groups as $group):?>
    <tr>
        <td><?php echo $group->name;?></td>
        <td class="text-right">
            <div class="btn-group">
                <a class="btn btn-default" href="<?php echo site_url('admin/customers/group_form/'.$group->id); ?>"><i class="icon-pencil"></i></a>
                <?php if($group->id != 1) : ?>
                <a class="btn btn-danger" href="<?php echo site_url('admin/customers/delete_group/'.$group->id); ?>" onclick="return areyousure();"><i class="icon-times "></i></a>
                <?php endif; ?>
            </div>
        </td>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>