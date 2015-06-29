<?php echo pageHeader(lang('product_form')); ?>

<?php $GLOBALS['option_value_count'] = 0;?>
<style type="text/css">
    .sortable { list-style-type: none; margin: 0; padding: 0; width: 100%; }
    .sortable li { margin: 0 3px 3px 3px; padding: 0.4em; padding-left: 1.5em; height: 18px; }
    .sortable li>col-md- { position: absolute; margin-left: -1.3em; margin-top:.4em; }
</style>

<script type="text/javascript">
//<![CDATA[

$(document).ready(function() {
    $(".sortable").sortable();
    $(".sortable > col-md-").disableSelection();
    //if the image already exists (phpcheck) enable the selector

    <?php if($id) : ?>
    //options related
    var ct  = $('#option_list').children().size();
    // set initial count
    option_count = <?php echo count($ProductOptions); ?>;
    <?php endif; ?>

    photos_sortable();
});

function addProduct_image(data)
{
    p   = data.split('.');

    var photo = '<?php add_image("'+p[0]+'", "'+p[0]+'.'+p[1]+'", '', '', '', base_url('uploads/images/thumbnails'));?>';
    $('#gc_photos').append(photo);
    $('#gc_photos').sortable('destroy');
    photos_sortable();
}

function remove_image(img)
{
    if(confirm('<?php echo lang('confirm_remove_image');?>'))
    {
        var id  = img.attr('rel');
        $('#gc_photo_'+id).remove();
    }
}

function photos_sortable()
{
    $('#gc_photos').sortable({
        handle : '.gc_thumbnail',
        items: '.gc_photo',
        axis: 'y',
        scroll: true
    });
}

function remove_option(id)
{
    if(confirm('<?php echo lang('confirm_remove_option');?>'))
    {
        $('#option-'+id).remove();
    }
}

//]]>
</script>


<?php echo form_open('admin/products/gift-card-form/'.$id ); ?>
    <div class="row">
        <div class="col-md-9">
            <div class="tabbable">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#product_info" data-toggle="tab"><?php echo lang('details');?></a></li>
                    <li><a href="#product_categories" data-toggle="tab"><?php echo lang('categories');?></a></li>
                    <li><a href="#productValues" data-toggle="tab"><?php echo lang('giftcard_values');?></a></li>
                    <li><a href="#product_photos" data-toggle="tab"><?php echo lang('images');?></a></li>
                </ul>
            </div>
            <div class="tab-content">
                <div class="tab-pane active" id="product_info">

                    <div class="form-group">
                        <?php echo form_input(['placeholder'=>lang('name'), 'name'=>'name', 'value'=>assign_value('name', $name), 'class'=>'form-control']); ?>
                    </div>

                    <div class="form-group">
                        <?php echo form_textarea(['name'=>'description', 'class'=>'redactor', 'value'=>assign_value('description', $description)]); ?>
                    </div>

                    <div class="form-group">
                        <label><?php echo lang('excerpt');?></label>
                        <?php echo form_textarea(['name'=>'excerpt', 'value'=>assign_value('excerpt', $excerpt), 'class'=>'form-control', 'rows'=>5]); ?>
                    </div>

                    <fieldset>
                        <legend><?php echo lang('header_information');?></legend>
                        <div style="padding-top:10px;">
                            
                            <div class="form-group">
                                <label for="slug"><?php echo lang('slug');?> </label>
                                <?php echo form_input(['name'=>'slug', 'value'=>assign_value('slug', $slug), 'class'=>'form-control']); ?>
                            </div>

                            <div class="form-group">
                                <label for="seo_title"><?php echo lang('seo_title');?> </label>
                                <?php echo form_input(['name'=>'seo_title', 'value'=>assign_value('seo_title', $seo_title), 'class'=>'form-control']); ?>
                            </div>

                            <div class="form-group">
                                <label for="meta"><?php echo lang('meta');?></label>
                                <?php echo form_textarea(['name'=>'meta', 'value'=>assign_value('meta', html_entity_decode($meta)), 'class'=>'form-control']);?>
                                <span class="help-block"><?php echo lang('meta_example');?></span>
                            </div>
                        </div>
                    </fieldset>
                </div>

                <div class="tab-pane" id="product_categories">

                    <?php if(isset($categories[0])):?>
                        <label><strong><?php echo lang('select_a_category');?></strong></label>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th><?php echo lang('name')?></th>
                                    <th></th>
                                    <th><?php echo lang('primary'); ?></th>
                                </tr>
                            </thead>
                        <?php
                        function list_categories($parent_id, $cats, $sub='', $product_categories, $primary_category) {

                            foreach ($cats[$parent_id] as $cat):?>
                            <tr>
                                <td><?php echo  $sub.$cat->name; ?></td>
                                <td>
                                    <input type="checkbox" name="categories[]" value="<?php echo $cat->id;?>" <?php echo(in_array($cat->id, $product_categories))?'checked="checked"':'';?>/>
                                </td>
                                <td>
                                    <input type="radio" name="primary_category" value="<?php echo $cat->id;?>" <?php echo ($primary_category == $cat->id)?'checked="checked"':'';?>/>
                                </td>
                            </tr>
                            <?php
                            if (isset($cats[$cat->id]) && sizeof($cats[$cat->id]) > 0)
                            {
                                $sub2 = str_replace('&rarr;&nbsp;', '&nbsp;', $sub);
                                    $sub2 .=  '&nbsp;&nbsp;&nbsp;&rarr;&nbsp;';
                                list_categories($cat->id, $cats, $sub2, $product_categories, $primary_category);
                            }
                            endforeach;
                        }


                        list_categories(0, $categories, '', $product_categories, $primary_category);

                        ?>

                    </table>
                <?php else:?>
                    <div class="alert"><?php echo lang('no_available_categories');?></div>
                <?php endif;?>

                </div>

                <div class="tab-pane" id="productValues">
                    <div class="row">
                        <div class="pull-right" style="padding:0px 0px 10px 0px;">
                            <button type="button" class="btn btn-primary btn-default-default" name="giftcard_values btn btn-default" onclick="add_giftcard_values()">Add </button>
                        </div>

                        <div class="clearfix"></div>

                        <table id="values_container" class="table table-striped">
                            <thead>
                                <tr></th></th></tr>
                            </thead>
                            <tbody>
                                <?php
                                $counter = 0;
                                if(!empty($ProductOptions))
                                
                                {
                                    
                                    foreach($ProductOptions as $po)
                                    {
                                        $po = (object)$po;
                                        if(empty($po->required)){$po->required = false;
                                    }

                                    if($po->type == 'droplist')
                                    { 
                                        if($po->values):
                                            foreach($po->values as $value)
                                            {
                                                $value = (object)$value;
                                                add_option_value($po, $counter++, $value->price);
                                                $GLOBALS['option_value_count']++;
                                            }
                                        endif;
                                    }
                                        $counter++;
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="tab-pane" id="product_photos">
                    
                    <iframe id="iframe_uploader" src="<?php echo site_url('admin/products/product_image_form');?>" style="height:75px; width:100%; border:0px;"></iframe>

                    <div id="gc_photos">

                    <?php
                    foreach($images as $photo_id=>$photo_obj)
                    {
                        if(!empty($photo_obj))
                        {
                            $photo = (array)$photo_obj;
                            add_image($photo_id, $photo['filename'], $photo['alt'], $photo['caption'], isset($photo['primary']));
                        }

                    }
                    ?>
                    </div>

                </div>
            </div>
            <button type="submit" class="btn btn-primary"><?php echo lang('save');?></button>
        </div>
        <div class="col-md-3">

            <div class="form-group">
                <?php echo form_dropdown('taxable', [0 => lang('not_taxable'), 1 => lang('taxable')], assign_value('taxable',$taxable), 'class="form-control"'); ?>
            </div>

            <div class="form-group">
                <label for="sku"><?php echo lang('sku');?></label>
                <?php echo form_input(['name'=>'sku', 'value'=>assign_value('sku', $sku), 'class'=>'form-control']);?>
            </div>

            <?php foreach($groups as $group):?>
                <fieldset>
                    <legend>
                        <?php echo $group->name;?>
                        <div class="checkbox pull-right" style="font-size:16px; margin-top:5px;">
                            <label>
                                <?php echo form_checkbox('enabled_'.$group->id, 1, ${'enabled_'.$group->id}); ?> <?php echo lang('enabled');?>
                            </label>
                        </div>
                    </legend>
                </fieldset>
            <?php endforeach;?>

        </div>
    </div>
</form>

<?php
function add_image($photo_id, $filename, $alt, $caption, $primary=false)
{

    ob_start();
    ?>
    <div class="row gc_photo" id="gc_photo_<?php echo $photo_id;?>" style="background-color:#fff; border-bottom:1px solid #ddd; padding-bottom:20px; margin-bottom:20px;">
        <div class="col-md-2">
            <input type="hidden" name="images[<?php echo $photo_id;?>][filename]" value="<?php echo $filename;?>"/>
            <img class="gc_thumbnail" src="<?php echo base_url('uploads/images/thumbnails/'.$filename);?>" style="padding:5px; border:1px solid #ddd"/>
        </div>
        <div class="col-md-10">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <input name="images[<?php echo $photo_id;?>][alt]" value="<?php echo $alt;?>" class="form-control" placeholder="<?php echo lang('alt_tag');?>"/>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="checkbox">
                        <label>
                            <input type="radio" name="primary_image" value="<?php echo $photo_id;?>" <?php if($primary) echo 'checked="checked"';?>/> <?php echo lang('main_image');?>
                        </label>
                    </div>
                </div>
                <div class="col-md-4">
                    <a onclick="return remove_image($(this));" rel="<?php echo $photo_id;?>" class="btn btn-danger pull-right"><i class="icon-times "></i></a>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <label><?php echo lang('caption');?></label>
                    <textarea name="images[<?php echo $photo_id;?>][caption]" class="form-control" rows="3"><?php echo $caption;?></textarea>
                </div>
            </div>
        </div>
    </div>

    <?php
    $stuff = ob_get_contents();

    ob_end_clean();

    echo replace_newline($stuff);
}

//this makes it easy to use the same code for initial generation of the form as well as javascript additions
function replace_newline($string) {
  return trim((string)str_replace(array("\r", "\r\n", "\n", "\t"), ' ', $string));
}
?>

<style>
.tree > ul > li {
    float: left;
    width: 50%;
}
</style>

<script type="text/javascript">

function photos_sortable()
{
    $('#gc_photos').sortable({
        handle : '.gc_thumbnail',
        items: '.gc_photo',
        axis: 'y',
        scroll: true
    });
}

<?php
function add_option_value($po, $count, $price)
{
    ob_start();
    ?>
    <tr id="giftcard_value_<?php echo $count;?>">
        <td>
            <div class="input-group">
                <input type="text" name="option[giftcard_values][<?php echo $count;?>]" value="<?php echo $price ?>" class="form-control"/>
                <div class="input-group-btn">
                    <button type="button" class="btn btn-danger" onclick="remove_giftcard_value(<?php echo $count;?>);"><i class="icon-times"></i></button>
                </div>
            </div>
        </td>
    </tr>
    <?php
    $stuff = ob_get_contents();

    ob_end_clean();

    echo replace_newline($stuff);
}
?>

var option_count = <?php echo $counter?>;
    
function add_giftcard_values()
{
    option_count ++;
    $('#values_container tbody').append('<?php add_option_value('', "'+option_count+'", '');?>')
}

function remove_giftcard_value(id)
{
    if(confirm('<?php echo lang('confirm_remove_giftcard_value');?>'))
    {
        $('#giftcard_value_'+id).remove();
    }
}


function photos_sortable()
{
    $('#gc_photos').sortable({
        handle : '.gc_thumbnail',
        items: '.gc_photo',
        axis: 'y',
        scroll: true
    });
}
//]]>
</script>
<style>
.tree > ul > li {
float: left;
width: 50%;
}
</style>
