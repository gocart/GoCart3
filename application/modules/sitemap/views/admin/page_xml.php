<?php foreach($pages as $page):?>
<?php if($page->parent_id != -1 && !empty($page->url)):?>
    <url>
        <loc><?php echo site_url('page/'.$page->slug);?></loc>
        <lastmod><?php echo date('Y-m-y' , strtotime("now"));?></lastmod>
    </url>
<?php endif;?>
<?php endforeach;?>