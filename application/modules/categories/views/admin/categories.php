<?php pageHeader(lang('categories')); ?>

<script type="text/javascript">
function areyousure()
{
    return confirm('<?php echo lang('confirm_delete_category');?>');
}
</script>

<div style="text-align:right">
    <a class="btn btn-primary" href="<?php echo site_url('admin/categories/form'); ?>"><i class="icon-plus"></i> <?php echo lang('add_new_category');?></a>
</div>

<table class="table table-striped">
    <thead>
        <tr>
            <th><i class="icon-eye-slash"></i></th>
            <th><?php echo lang('name')?></th>
            <?php foreach($groups as $group):?>
                <th><?php echo $group->name;?></th>
            <?php endforeach; ?>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php echo (count($categories) < 1)?'<tr><td style="text-align:center;" colspan="4">'.lang('no_categories').'</td></tr>':''?>
        <?php
        function list_categories($parent_id, $cats, $groups, $sub='', $hidden=false) {
            
            foreach ($cats[$parent_id] as $cat):?>
            <tr>
                <td><?php echo ($hidden)?'<i class="icon-eye-slash"></i>':'';?></td>
                <td><?php echo $sub.$cat->name; ?></td>
                <?php foreach($groups as $group):?>
                    <td><?php echo ($cat->{'enabled_'.$group->id} == '1') ? lang('enabled') : lang('disabled'); ?></td>
                <?php endforeach;?>
                <td class="text-right">
                    <div class="btn-group">
                        <a class="btn btn-default" href="<?php echo  site_url('admin/categories/form/'.$cat->id);?>"><i class="icon-pencil"></i></a>
                        <a class="btn btn-danger" href="<?php echo  site_url('admin/categories/delete/'.$cat->id);?>" onclick="return areyousure();"><i class="icon-times "></i></a>
                    </div>
                </td>
            </tr>
            <?php
            if (isset($cats[$cat->id]) && sizeof($cats[$cat->id]) > 0)
            {
                $sub2 = str_replace('&rarr;&nbsp;', '&nbsp;', $sub);
                    $sub2 .=  '&nbsp;&nbsp;&nbsp;&rarr;&nbsp;';
                list_categories($cat->id, $cats, $groups, $sub2, $hidden);
            }
            endforeach;
        }
        
        if(isset($categories[-1]))
        {
            list_categories(-1, $categories, $groups, '', true);
        }

        if(isset($categories[0]))
        {
            list_categories(0, $categories, $groups);
        }
        
        ?>
    </tbody>
</table>