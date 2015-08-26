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
        <div class="pull-right">
            <select id="sort">
                <option<?php echo($sort=='name' && $dir == 'ASC')?' selected="selected"':'';?> value="<?php echo site_url('search/'.$code.'/name/ASC/'.$page);?>"><?php echo lang('sort_by_name_asc');?></option>
                <option<?php echo($sort=='name' && $dir == 'DESC')?' selected="selected"':'';?>  value="<?php echo site_url('search/'.$code.'/name/DESC/'.$page);?>"><?php echo lang('sort_by_name_desc');?></option>
                <option<?php echo($sort=='price' && $dir == 'ASC')?' selected="selected"':'';?>  value="<?php echo site_url('search/'.$code.'/price/ASC/'.$page);?>"><?php echo lang('sort_by_price_asc');?></option>
                <option<?php echo($sort=='price' && $dir == 'DESC')?' selected="selected"':'';?>  value="<?php echo site_url('search/'.$code.'/price/DESC/'.$page);?>"><?php echo lang('sort_by_price_desc');?></option>
            </select>
        </div>
        <div class="pull-right">
            <label class="control-label" for="input-limit"><?php echo lang('sort'); ?></label>
        </div>
    </div> 

<?php endif;
    
    $this->show('categories/products', ['products'=>$products]);
    //include(__DIR__.'/products.php');?>

    <div class="text-center pagination">
        <?php echo CI::pagination()->create_links();?>
    </div>
   
</div>
<script type="text/javascript">
$(function() {
    $("#sort").change(function () {
        window.location = this.value;
    });
});
</script>