<?php pageHeader(lang('banner_collections')) ?>
<a class="btn btn-primary pull-right" href="<?php echo site_url('admin/banners/banner_collection_form'); ?>"><i class="icon-plus"></i> <?php echo lang('add_new_banner_collection');?></a>

<table class="table table-striped">
    <thead>
        <tr>
            <th><?php echo lang('name');?></th>
            <th></th>
        </tr>
    </thead>
    <?php echo (count($banner_collections) < 1)?'<tr><td style="text-align:center;" colspan="5">'.lang('no_banner_collections').'</td></tr>':''?>
    <?php if ($banner_collections): ?>
    <tbody>
    <?php

    foreach ($banner_collections as $banner_collection):?>
        <tr>
            <td><?php echo $banner_collection->name;?></td>
            <td class="text-right">
                <div class="btn-group">
                    <a class="btn btn-default" href="<?php echo base_url('admin/banners/banner_collection/'.$banner_collection->banner_collection_id);?>"><i class="icon-image"></i></a>
                    
                    <a class="btn btn-default" href="<?php echo site_url('admin/banners/banner_collection_form/'.$banner_collection->banner_collection_id);?>"><i class="icon-pencil"></i></a>
                    
                    <a class="btn btn-danger" href="<?php echo site_url('admin/banners/delete_banner_collection/'.$banner_collection->banner_collection_id);?>" onclick="return areyousure();"><i class="icon-times "></i></a>
                </div>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
    <?php endif;?>
</table>

<script type="text/javascript">
function areyousure(){
    return confirm('<?php echo lang('confirm_delete_banner_collection');?>');
}
</script>