<?php echo pageHeader(lang('product_form')); ?>

<?php $GLOBALS['optionValueCount'] = 0;?>
<style type="text/css">
    .sortable { list-style-type: none; margin: 0; padding: 0; width: 100%; }
    .sortable li { margin: 0 3px 3px 3px; padding: 0.4em; padding-left: 1.5em; height: 18px; }
    .sortable li>col-md- { position: absolute; margin-left: -1.3em; margin-top:.4em; }
</style>

<?php echo form_open('admin/products/form/'.$id ); ?>
    <div class="row">
        <div class="col-md-9">
            <div class="tabbable">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#product_info" data-toggle="tab"><?php echo lang('details');?></a></li>
                    <?php //if there aren't any files uploaded don't offer the client the tab
                    if (count($file_list) > 0):?>
                    <li><a href="#product_downloads" data-toggle="tab"><?php echo lang('digital_content');?></a></li>
                    <?php endif;?>
                    <li><a href="#product_categories" data-toggle="tab"><?php echo lang('categories');?></a></li>
                    <li><a href="#ProductOptions" data-toggle="tab"><?php echo lang('options');?></a></li>
                    <li><a href="#product_related" data-toggle="tab"><?php echo lang('related_products');?></a></li>
                    <li><a href="#product_photos" data-toggle="tab"><?php echo lang('images');?></a></li>
                </ul>
            </div>
            <div class="tab-content">
                <div class="tab-pane active" id="product_info">

                    <div class="form-group">
                        <label><?php echo lang('name');?></label>
                        <?php echo form_input(['placeholder'=>lang('name'), 'name'=>'name', 'value'=>assign_value('name', $name), 'class'=>'form-control']); ?>
                    </div>

                    <div class="form-group">
                        <label><?php echo lang('description');?></label>
                        <?php echo form_textarea(['name'=>'description', 'class'=>'redactor', 'value'=>assign_value('description', $description)]); ?>
                    </div>

                    <div class="form-group">
                        <label><?php echo lang('excerpt');?></label>
                        <?php echo form_textarea(['name'=>'excerpt', 'value'=>assign_value('excerpt', $excerpt), 'class'=>'redactor']); ?>
                    </div>

                    <fieldset>
                        <legend><?php echo lang('inventory');?></legend>
                        <div class="row" style="padding-top:10px;">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="track_stock"><?php echo lang('track_stock');?> </label>
                                    <?php echo form_dropdown('track_stock', [1 => lang('yes'), 0 => lang('no')], assign_value('track_stock',$track_stock), 'class="form-control"'); ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="fixed_quantity"><?php echo lang('fixed_quantity');?> </label>
                                    <?php echo form_dropdown('fixed_quantity', [1 => lang('yes'), 0 => lang('no')], assign_value('fixed_quantity',$fixed_quantity), 'class="form-control"'); ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="quantity"><?php echo lang('quantity');?> </label>
                                    <?php echo form_input(['name'=>'quantity', 'value'=>assign_value('quantity', $quantity), 'class'=>'form-control']); ?>
                                </div>
                            </div>
                        </div>
                    </fieldset>

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

                <div class="tab-pane" id="product_downloads">
                    <div class="alert alert-info">
                        <?php echo lang('digital_products_desc'); ?>
                    </div>
                    <fieldset>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th><?php echo lang('filename');?></th>
                                    <th><?php echo lang('title');?></th>
                                    <th><?php echo lang('size');?></th>
                                    <th style="width:16px;"></th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php echo (count($file_list) < 1)?'<tr><td style="text-align:center;" colspan="6">'.lang('no_files').'</td></tr>':''?>
                            <?php foreach ($file_list as $file):?>
                                <tr>
                                    <td><?php echo $file->filename ?></td>
                                    <td><?php echo $file->title ?></td>
                                    <td><?php echo $file->size ?></td>
                                    <td><?php echo form_checkbox('downloads[]', $file->id, in_array($file->id, $product_files)); ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </fieldset>
                </div>

                <div class="tab-pane" id="product_categories">
                    <?php if(isset($categories[0])):?>
                        <label><strong><?php echo lang('select_a_category');?></strong></label>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th><i class="icon-eye-slash"></i></th>
                                    <th><?php echo lang('name')?></th>
                                    <?php foreach ($groups as $group):?>
                                        <th><?php echo $group->name;?></th>
                                    <?php endforeach;?>
                                    <th class="text-center"><?php echo lang('in').'/'.lang('main'); ?></th>
                                </tr>
                            </thead>
                        <?php
                        function list_categories($parent_id, $cats, $sub='', $product_categories, $primary_category, $groups, $hidden)
                        {
                            if(isset($cats[$parent_id]))
                            {
                                foreach ($cats[$parent_id] as $cat):?>
                                <tr>
                                    <td><?php echo ($hidden)?'<i class="icon-eye-slash">':'';?></i></td>
                                    <td><?php echo  $sub.$cat->name; ?></td>
                                    <?php foreach ($groups as $group):?>
                                        <td><?php echo ($cat->{'enabled_'.$group->id})?'<i class="icon-check"></i>':'';?></td>
                                    <?php endforeach;?>
                                    <td class="text-center">
                                        <input type="checkbox" name="categories[]" value="<?php echo $cat->id;?>" <?php echo(in_array($cat->id, $product_categories))?'checked="checked"':'';?>/>
                                        &nbsp;&nbsp;
                                        <input type="radio" name="primary_category" value="<?php echo $cat->id;?>" <?php echo ($primary_category == $cat->id)?'checked="checked"':'';?>/>
                                    </td>
                                </tr>
                                <?php
                                if (isset($cats[$cat->id]) && sizeof($cats[$cat->id]) > 0)
                                {
                                    $sub2 = str_replace('&rarr;&nbsp;', '&nbsp;', $sub);
                                        $sub2 .=  '&nbsp;&nbsp;&nbsp;&rarr;&nbsp;';
                                    list_categories($cat->id, $cats, $sub2, $product_categories, $primary_category, $groups, $hidden);
                                }
                                endforeach;
                            }
                        }

                        list_categories(-1, $categories, '', $product_categories, $primary_category, $groups, true);
                        list_categories(0, $categories, '', $product_categories, $primary_category, $groups, false);
                        ?>

                    </table>
                <?php else:?>
                    <div class="alert"><?php echo lang('no_available_categories');?></div>
                <?php endif;?>

                </div>

                <div class="tab-pane" id="ProductOptions">

                    <div class="row" style="margin-bottom:15px;">
                        <div class="col-md-5 col-md-offset-7">
                            <div class="input-group">
                               <select id="optionTypes" class="form-control">
                                    <option value=""><?php echo lang('select_option_type')?></option>
                                    <option value="checklist"><?php echo lang('checklist');?></option>
                                    <option value="radiolist"><?php echo lang('radiolist');?></option>
                                    <option value="droplist"><?php echo lang('droplist');?></option>
                                    <option value="textfield"><?php echo lang('textfield');?></option>
                                    <option value="textarea"><?php echo lang('textarea');?></option>
                                </select>
                                <span class="input-group-btn">
                                    <button id="addOption" class="btn btn-primary" type="button"><?php echo lang('add_option');?></button>
                                </span>
                            </div>
                        </div>
                    </div>

                    <style type="text/css">
                        .option-form {
                            display:none;
                            margin-top:10px;
                        }
                        .optionValuesForm
                        {
                            background-color:#fff;
                            padding:6px 3px 6px 6px;
                            -webkit-border-radius: 3px;
                            -moz-border-radius: 3px;
                            border-radius: 3px;
                            margin-bottom:5px;
                            border:1px solid #ddd;
                        }

                        .optionValuesForm input {
                            margin:0px;
                        }
                        .optionValuesForm a {
                            margin-top:3px;
                        }
                    </style>

                    <table class="table table-striped">
                        <tbody id="optionsContainer">
                        </tbody>
                    </table>

                </div>

                <div class="tab-pane" id="product_related">

                    <label><strong><?php echo lang('select_a_product');?></strong></label>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <input class="form-control" type="text" id="product_search" />
                            </div>
                            <script type="text/javascript">
                            $('#product_search').keyup(function(){
                                $('#product_list').html('');
                                run_product_query();
                            });

                            function run_product_query()
                            {
                                $.post("<?php echo site_url('admin/products/product_autocomplete/');?>", { name: $('#product_search').val(), limit:10},
                                    function(data) {

                                        $('#product_list').html('');

                                        $.each(data, function(index, value){

                                            if($('#related_product_'+index).length == 0)
                                            {
                                                $('#product_list').append('<option id="product_item_'+index+'" value="'+index+'">'+value+'</option>');
                                            }
                                        });

                                }, 'json');
                            }
                            </script>

                            <div class="form-group">
                                <select class="form-control" id="product_list" size="5" style="margin:0px;"></select>
                            </div>
                            <button type="button" onclick="add_related_product();return false;" class="btn btn-primary btn-block" title="Add Related Product"><?php echo lang('add_related_product');?></button>
                        </div>

                        <div class="col-md-8">
                            <table class="table table-striped" style="margin-top:10px;">
                                <tbody id="product_items_container">

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="tab-pane" id="product_photos">
                    <iframe id="iframe_uploader" src="<?php echo site_url('admin/products/product_image_form');?>" style="height:75px; width:100%; border:0px;"></iframe>
                    <div id="gc_photos"></div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary"><?php echo lang('save');?></button>

        </div>
        <div class="col-md-3">
            <div class="form-group">
                <?php echo form_dropdown('shippable', [1 => lang('shippable'), 0 => lang('not_shippable')], assign_value('shippable',$shippable), 'class="form-control"');?>
            </div>

            <div class="form-group">
                <?php echo form_dropdown('taxable', [1 => lang('taxable'), 0 => lang('not_taxable')], assign_value('taxable',$taxable), 'class="form-control"'); ?>
            </div>

            <div class="form-group">
                <label for="sku"><?php echo lang('sku');?></label>
                <?php echo form_input(['name'=>'sku', 'value'=>assign_value('sku', $sku), 'class'=>'form-control']);?>
            </div>

            <div class="form-group">
                <label for="weight"><?php echo lang('weight');?> </label>
                <?php echo form_input(['name'=>'weight', 'value'=>assign_value('weight', $weight), 'class'=>'form-control']);?>
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
                    <div class="row">
                        <div class="col-md-6">
                            <label for="price"><?php echo lang('price');?></label>
                            <?php echo form_input(['name'=>'price_'.$group->id, 'value'=>assign_value('price_'.$group->id, ${'price_'.$group->id}), 'class'=>'form-control']);?>
                        </div>
                        <div class="col-md-6">
                            <label for="saleprice"><?php echo lang('saleprice');?></label>
                            <?php echo form_input(['name'=>'saleprice_'.$group->id, 'value'=>assign_value('saleprice_'.$group->id, ${'saleprice_'.$group->id}), 'class'=>'form-control']);?>
                        </div>
                    </div>
                </fieldset>
            <?php endforeach;?>
        </div>
    </div>
</form>

<script type="text/template" id="relatedItemsTemplate">
    <tr id="related_product_{{id}}">
        <td>
            <input type="hidden" name="related_products[]" value="{{id}}"/>
            {{name}}
        </td>
        <td class="text-right">
            <a class="btn btn-danger" href="#" onclick="remove_related_product({{id}}); return false;"><i class="icon-times"></i></a>
        </td>
    </tr>
</script>

<script type="text/template" id="imageTemplate">
    <div class="row gc_photo" id="gc_photo_{{id}}" style="background-color:#fff; border-bottom:1px solid #ddd; padding-bottom:20px; margin-bottom:20px;">
        <div class="col-md-2">
            <input type="hidden" name="images[{{id}}][filename]" value="{{filename}}"/>
            <img class="gc_thumbnail" src="<?php echo base_url('uploads/images/thumbnails/{{filename}}');?>" style="padding:5px; border:1px solid #ddd"/>
        </div>
        <div class="col-md-10">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <input name="images[{{id}}][alt]" value="{{alt}}" class="form-control" placeholder="<?php echo lang('alt_tag');?>"/>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="checkbox">
                        <label>
                            <input type="radio" name="primary_image" value="{{id}}" {{#primary}}checked="checked"{{/primary}}/> <?php echo lang('main_image');?>
                        </label>
                    </div>
                </div>
                <div class="col-md-4">
                    <a onclick="return remove_image($(this));" rel="{{id}}" class="btn btn-danger pull-right"><i class="icon-times "></i></a>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <label><?php echo lang('caption');?></label>
                    <textarea name="images[{{id}}][caption]" class="form-control" rows="3">{{caption}}</textarea>
                </div>
            </div>
        </div>
    </div>
</script>

<script type="text/template" id="optionTemplate">
    <tr id="option-{{id}}">
        <td>
            <a class="handle1 btn btn-primary btn-sm"><i class="icon-sort"></i></a>
            <strong><a class="optionTitle" href="#option-form-{{id}}">{{type}} : {{name}}</a></strong>
            <button type="button" class="btn btn-danger btn-sm pull-right" onclick="remove_option({{id}});"><i class="icon-times"></i></button>
            <input type="hidden" name="option[{{id}}][type]" value="{{type}}" />

            <div class="option-form" id="option-form-{{id}}">
                <div class="row">
                    <div class="col-md-10">
                        <input type="text" class="form-control" placeholder="<?php echo lang('option_name');?>" name="option[{{id}}][name]" value="{{name}}"/>
                    </div>
                    <div class="col-md-2" style="text-align:right;">
                        <div class="checkbox">
                            <label>
                                <input class="checkbox" type="checkbox" name="option[{{id}}][required]" value="1" {{#required}} checked="checked" {{/required}}/> <?php echo lang('required');?>
                            </label>
                        </div>
                    </div>
                </div>
                
                {{^isText}}
                <a class="btn btn-primary" onclick="addOptionValue({{id}});"><?php echo lang('add_item');?></a>
                {{/isText}}
                
                <div style="margin-top:10px;">
                    <div class="row">
                        {{^isText}}<div class="col-md-1">&nbsp;</div>{{/isText}}
                        <div class="col-md-3"><strong>&nbsp;&nbsp;<?php echo lang('name');?></strong></div>
                        <div class="col-md-2"><strong>&nbsp;<?php echo lang('value');?></strong></div>
                        <div class="col-md-2"><strong>&nbsp;<?php echo lang('weight');?></strong></div>
                        <div class="col-md-2"><strong>&nbsp;<?php echo lang('price');?></strong></div>
                        {{#isText}}<div class="col-md-2"><strong>&nbsp;<?php echo lang('limit');?></strong></div>{{/isText}}
                    </div>

                    <div class="optionItems" id="optionItems-{{id}}"></div>
                </div>
            </div>
        </td>
    </tr>
</script>

<script type="text/template" id="optionValueTemplate">
    <div class="optionValuesForm">
        <div class="row">
            
            {{^isText}}
                <div class="col-md-1"><a class="handle2 btn btn-primary btn-sm" style="float:left;"><i class="icon-sort"></i></a></div>
            {{/isText}}

            <div class="col-md-3"><input type="text" class="form-control input-sm" name="option[{{id}}][values][{{valId}}][name]" value="{{name}}" /></div>
            <div class="col-md-2"><input type="text" class="form-control input-sm" name="option[{{id}}][values][{{valId}}][value]" value="{{value}}" /></div>
            <div class="col-md-2"><input type="text" class="form-control input-sm" name="option[{{id}}][values][{{valId}}][weight]" value="{{weight}}" /></div>
            <div class="col-md-2"><input type="text" class="form-control input-sm" name="option[{{id}}][values][{{valId}}][price]" value="{{price}}" /></div>
            <div class="col-md-2">
                {{#isText}}
                    <input class="form-control" type="text" name="option[{{id}}][values][{{valId}}][limit]" value="{{limit}}" />
                {{/isText}}
                {{^isText}}
                    <a class="deleteOptionValue btn btn-danger btn-sm pull-right"><i class="icon-times"></i></a>
                {{/isText}}
            </div>
        </div>
    </div>
</script>

<script>
var relatedItemsTemplate = $('#relatedItemsTemplate').html();
var relatedItems = <?php echo json_encode($related_products);?>

var imageTemplate = $('#imageTemplate').html();
var images = <?php echo json_encode($images);?>

var optionAddValueButtonTemplate = $('#optionTextButtonTemplate').html();
var optionTemplate = $('#optionTemplate').html();
var optionValueTemplate = $('#optionValueTemplate').html();

var optionCount = 0;
var optionValueCount = 0;
var options = <?php echo json_encode($productOptions);?>

$(document).ready(function() {

    optionsSortable();
    optionValuesSortable();

    $('body').on('click', '.optionTitle', function(){
        $($(this).attr('href')).slideToggle();
        return false;
    }).on('click', '.deleteOptionValue', function(){
        if(confirm('<?php echo lang('confirm_remove_value');?>'))
        {
            $(this).closest('.optionValuesForm').remove();
        }
    });

    $(".sortable").sortable();
    $(".sortable > col-medium-").disableSelection();

    //init the photo sortable
    photos_sortable();

    $.each(relatedItems, function(key,val) {
        add_related_product(val.id, val.name);
    });

    $.each(images, function(key,val) {
        addProductImage(key, val.filename, val.alt, val.primary, val.caption);
    });

    $.each(options, function(key, val) {
        isText = null;
        if(val.type == 'textfield' || val.type == 'textarea')
        {
            isText = true;
        }

        addOption(val.type, val.name, isText, val.required, val.values);
    });

    $( "#addOption" ).click(function(){
        var type = $('#optionTypes').val();

        if(type != '')
        {
            isText = null;

            if(type == 'textfield' || type == 'textarea')
            {
                isText = true;
            }
            addOption($('#optionTypes').val(), '', isText, '', [0]);
        }
    });
});

function addOption(type, name, isText, required, values)
{
    //increase optionCount by 1
    optionCount++;

    var view = {
        id:optionCount,
        type:type,
        name:name,
        isText:isText,
        required:parseInt(required)
    }

    var output = Mustache.render(optionTemplate, view);

    $('#optionsContainer').append(output);

    $.each(values, function(key,val) {
        addOptionValue(optionCount, val.name, val.value, val.weight, val.price, val.limit, isText);
    });
    
    optionsSortable();
}

function addOptionValue(id, name, value, weight, price, limit, isText)
{

    optionValueCount++;
    
    var view = {
        valId:optionValueCount,
        id:id,
        name:name,
        value:value,
        weight:weight,
        price:price,
        limit:limit,
        isText:isText
    }
    
    var output = Mustache.render(optionValueTemplate, view);
    $('#optionItems-'+id).append(output);

    optionValuesSortable();
}


function addProductImage(id, filename, alt, primary, caption)
{
    view = {
        id:id,
        filename:filename,
        alt:alt,
        primary:primary,
        caption:caption
    }

    var output = Mustache.render(imageTemplate, view);

    $('#gc_photos').append(output);
    $('#gc_photos').sortable('refresh');
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

function optionsSortable()
{
    $('#optionsContainer').sortable({
        axis: "y",
        items:'tr',
        handle:'.handle1',
        forceHelperSize: true,
        forcePlaceholderSize: true
    });
}

function optionValuesSortable()
{
    $('.optionItems').sortable({
        axis: "y",
        handle:'.handle2',
        forceHelperSize: true,
        forcePlaceholderSize: true
    });
}

function remove_option(id)
{
    if(confirm('<?php echo lang('confirm_remove_option');?>'))
    {
        $('#option-'+id).remove();
    }
}

function add_related_product(id, name)
{
    var view = null;
    if(id != undefined && name != undefined)
    {
        view = {
            id:id,
            name:name
        }
    }
    else
    {
        if($('#related_product_'+$('#product_list').val()).length == 0 && $('#product_list').val() != null)
        {
            view = {
                id:$('#product_list').val(),
                name: $('#product_item_'+$('#product_list').val()).html()
            }
        }
    }

    if(view != null)
    {
        var output = Mustache.render(relatedItemsTemplate, view);
        $('#product_items_container').append(output);
        run_product_query();
    }
    else
    {
        if($('#product_list').val() == null)
        {
            alert('<?php echo lang('alert_select_product');?>');
        }
        else
        {
            alert('<?php echo lang('alert_product_related');?>');
        }
    }
}

function remove_related_product(id)
{
    if(confirm('<?php echo lang('confirm_remove_related');?>'))
    {
        $('#related_product_'+id).remove();
        run_product_query();
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

</script>
<style>
.tree > ul > li {
    float: left;
    width: 50%;
}
</style>