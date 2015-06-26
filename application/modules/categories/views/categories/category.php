<?php if(!empty($category)):?>
    <div class="page-header">
        <h1><?php echo $category->name; ?></h1>
    </div>

    <?php if(!empty($category->description)):?>
    <div class="categoryDescription">
        <?php echo (new content_filter($category->description))->display();?>
    </div>
    <?php endif;?>

    <div class="productsFilter">
        <div class="col-md-2 pull-right">
            <select id="sort" class="form-control">
                <option<?php echo(!empty($_GET['by']) && $_GET['by']=='name/asc')?' selected="selected"':'';?> value="?by=name/asc"><?php echo lang('sort_by_name_asc');?></option>
                <option<?php echo(!empty($_GET['by']) && $_GET['by']=='name/desc')?' selected="selected"':'';?>  value="?by=name/desc"><?php echo lang('sort_by_name_desc');?></option>
                <option<?php echo(!empty($_GET['by']) && $_GET['by']=='price/asc')?' selected="selected"':'';?>  value="?by=price/asc"><?php echo lang('sort_by_price_asc');?></option>
                <option<?php echo(!empty($_GET['by']) && $_GET['by']=='price/desc')?' selected="selected"':'';?>  value="?by=price/desc"><?php echo lang('sort_by_price_desc');?></option>
            </select>
        </div>
        <div class="col-md-1 pull-right">
            <label class="control-label" for="input-limit"><?php echo lang('sort'); ?></label>
        </div>
    </div> 

<?php endif;
    
    include(__DIR__.'/products.php');?>

    <div class="text-center pagination">
        <?php echo CI::pagination()->create_links();?>
    </div>
   
</div>
<script type="text/javascript">
$(function() {
    $("#sort").change(function () {
        window.location = '<?php echo site_url(uri_string()); ?>/'+this.value;
    });
});
</script>