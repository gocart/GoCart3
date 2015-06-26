<?php foreach($products as $product):?>

    <url>
        <loc><?php echo site_url('product/'.$product->slug);?></loc>
        <lastmod><?php echo date('Y-m-y' , strtotime("now"));?></lastmod>
    </url>

<?php endforeach;?>