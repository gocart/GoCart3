<?php pageHeader(lang('countries')) ?>

<script type="text/javascript">
//<![CDATA[
$(document).ready(function(){
    create_sortable();  
});
// Return a helper with preserved width of cells
var fixHelper = function(e, ui) {
    ui.children().each(function() {
        $(this).width($(this).width());
    });
    return ui;
};
function create_sortable()
{
    $('#countries').sortable({
        scroll: true,
        helper: fixHelper,
        axis: 'y',
        handle:'.handle',
        update: function(){
            save_sortable();
        }
    }); 
    $('#countries').sortable('enable');
}

function save_sortable()
{
    serial=$('#countries').sortable('serialize');
            
    $.ajax({
        url:'<?php echo site_url('admin/locations/organize_countries');?>',
        type:'POST',
        data:serial
    });
}
function areyousure()
{
    return confirm('<?php echo lang('confirm_delete_country');?>');
}
//]]>
</script>

<div class="text-right">
    <a class="btn btn-primary" href="<?php echo site_url('admin/locations/country_form'); ?>"><i class="icon-plus"></i> <?php echo lang('add_new_country');?></a>
    <a class="btn btn-primary" href="<?php echo site_url('admin/locations/zone_form'); ?>"><i class="icon-plus"></i> <?php echo lang('add_new_zone');?></a>
</div>

<table class="table table-striped" cellspacing="0" cellpadding="0">
    <thead>
        <tr>
            <th><?php echo lang('sort');?></th>
            <th><?php echo lang('name');?></th>
            <th><?php echo lang('iso_code_2');?></th>
            <th><?php echo lang('iso_code_3');?></th>
            <th><?php echo lang('tax');?></th>
            <th><?php echo lang('status');?></th>
            <th/>
        </tr>
    </thead>
    <tbody id="countries">
<?php foreach ($locations as $location):?>
        <tr id="country-<?php echo $location->id;?>">
            <td class="handle"><a class="btn btn-primary"><span class="icon-sort"></span></a></td>
            <td><?php echo  $location->name; ?></td>
            <td><?php echo $location->iso_code_2;?></td>
            <td><?php echo $location->iso_code_3;?></td>
            <td><?php echo $location->tax+0;?>%</td>
            <td><?php echo ((bool)$location->status)?'enabled':'disabled';?></td>
            <td class="text-right">
                <div class="btn-group">
                    <a class="btn btn-default" href="<?php echo site_url('admin/locations/country_form/'.$location->id); ?>"><i class="icon-pencil"></i></a>
                    <a class="btn btn-default" href="<?php echo site_url('admin/locations/zones/'.$location->id); ?>"><i class="icon-map-marker"></i></a>
                    <a class="btn btn-danger" href="<?php echo site_url('admin/locations/delete_country/'.$location->id); ?>" onclick="return areyousure<();"><i class="icon-times "></i></a>
                </div>
            </td>
      </tr>
<?php endforeach; ?>
    </tbody>
</table>