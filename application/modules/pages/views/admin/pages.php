<?php echo pageHeader(lang('pages')); ?>
<script type="text/javascript">
function areyousure()
{
    return confirm('<?php echo lang('confirm_delete');?>');
}
</script>
<div class="text-right">
    <a class="btn btn-primary" href="<?php echo site_url('admin/pages/form'); ?>"><i class="icon-plus"></i> <?php echo lang('add_new_page');?></a>
    <a class="btn btn-primary" href="<?php echo site_url('admin/pages/link_form'); ?>"><i class="icon-plus"></i> <?php echo lang('add_new_link');?></a>
</div>

<table class="table table-striped">
    <thead>
        <tr>
            <th style="max-width:20px;"></th>
            <th><?php echo lang('title');?></th>
            <th/>
        </tr>
    </thead>
    
    <?php echo (count($pages) < 1)?'<tr><td style="text-align:center;" colspan="2">'.lang('no_pages_or_links').'</td></tr>':''?>
    <?php if($pages):?>
    <tbody>
        
        <?php
        function list_pages($parent_id, $pages, $sub='') {
            
            foreach ($pages[$parent_id] as $page):?>
            <tr>
                <td style="width:20px;"><?php echo ($page->parent_id == -1)?'<i class="icon-eye-slash"></i>':'';?></td>
                <td><?php echo  $sub.$page->title; ?></td>
                <td class="text-right">
                    <div class="btn-group">
                        <?php if(!empty($page->url)): ?>
                            <a class="btn btn-default" href="<?php echo site_url('admin/pages/link_form/'.$page->id); ?>"><i class="icon-pencil"></i></a>
                            <a class="btn btn-default" href="<?php echo $page->url;?>" target="_blank"><i class="icon-link"></i></a>
                        <?php else: ?>
                            <a class="btn btn-default" href="<?php echo site_url('admin/pages/form/'.$page->id); ?>"><i class="icon-pencil"></i></a>
                            <a class="btn btn-default" href="<?php echo site_url('page/'.$page->slug); ?>" target="_blank"><i class="icon-eye"></i></a>
                        <?php endif; ?>
                        <a class="btn btn-danger" href="<?php echo site_url('admin/pages/delete/'.$page->id); ?>" onclick="return areyousure();"><i class="icon-times"></i></a>
                    </div>
                </td>
            </tr>
            <?php
            if (isset($pages[$page->id]) && sizeof($pages[$page->id]) > 0)
            {
                $sub2 = str_replace('&rarr;&nbsp;', '&nbsp;', $sub);
                    $sub2 .=  '&nbsp;&nbsp;&nbsp;&rarr;&nbsp;';
                list_pages($page->id, $pages, $sub2);
            }
            endforeach;
        }
        
        if(isset($pages[-1]))
        {
            list_pages(-1, $pages);
        }

        if(isset($pages[0]))
        {
            list_pages(0, $pages);
        }
        ?>
    </tbody>
    <?php endif;?>
</table>