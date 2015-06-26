<?php if(!empty($breadcrumbs)):?>

<div class="breadcrumbs">
    <a href="<?php echo site_url();?>"><i class="icon-home"></i></a>
    <?php for($i = 0; $i<count($breadcrumbs); $i++):?>
        <?php if($i != count($breadcrumbs)-1):?>
            <a href="<?php echo $breadcrumbs[$i]['link'];?>"><?php echo $breadcrumbs[$i]['name'];?></a>
        <?php else:?>
            <span><?php echo $breadcrumbs[$i]['name'];?></span>
        <?php endif;?>
    <?php endfor;?>
</div>

<?php endif;