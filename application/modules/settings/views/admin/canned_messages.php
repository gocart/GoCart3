<?php pageHeader(lang('canned_messages')) ?>

<div class="text-right">
    <a class="btn btn-primary" href="<?php echo site_url('admin/settings/canned_message_form/');?>"><i class="icon-plus"></i> <?php echo lang('add_canned_message');?></a>
</div>

<?php if(count($canned_messages) > 0): ?>
<table class="table table-striped">
    <thead>
        <tr>
            <th><?php echo lang('message_name');?></th>
            <th></th>
        </tr>
    </thead>
    <tbody>
    <?php foreach($canned_messages as $message): ?>
        <tr>
            <td><?php echo $message['name']; ?></td>
            <td class="text-right">
                <span class="btn-group">
                    <a class="btn btn-default" href="<?php echo site_url('admin/settings/canned_message_form/'.$message['id']);?>"><i class="icon-pencil"></i></a>
                    <?php if($message['deletable'] == 1) : ?>   
                        <a class="btn btn-danger" href="<?php echo site_url('admin/settings/delete_message/'.$message['id']);?>" onclick="return areyousure();"><i class="icon-times"></i></a>
                    <?php endif; ?>
                </span>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>

<script type="text/javascript">
function areyousure()
{
    return confirm('<?php echo lang('confirm_are_you_sure');?>');
}
</script>