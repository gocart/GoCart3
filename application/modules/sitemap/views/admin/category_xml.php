<?php foreach($categories as $category):?>

    <url>
        <loc><?php echo site_url('category/'.$category->slug);?></loc>
        <lastmod><?php echo date('Y-m-y' , strtotime("now"));?></lastmod>
    </url>

<?php endforeach;?>