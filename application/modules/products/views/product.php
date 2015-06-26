<div class="page-header">
    <h1><?php echo $product->name;?></h1>
</div>

<div class="col-nest">
    <div class="col" data-cols="2/5" data-medium-cols="2/5">
        <div class="productImg"><?php
        $photo = theme_img('no_picture.png', lang('no_image_available'));

        if(!empty($product->images[0]))
        {
            foreach($product->images as $photo)
            {
                if(isset($photo['primary']))
                {
                    $primary = $photo;
                }
            }
            if(!isset($primary))
            {
                $tmp = $product->images; //duplicate the array so we don't lose it.
                $primary = array_shift($tmp);
            }

            $photo = '<img src="'.base_url('uploads/images/full/'.$primary['filename']).'" alt="'.$product->seo_title.'" data-caption="'.htmlentities(nl2br($primary['caption'])).'"/>';
        }
        echo $photo
        ?></div>
        <?php if(!empty($primary['caption'])):?>
        <div class="productCaption">
            <?php echo $primary['caption'];?>
        </div>
        <?php endif;?>

        <?php if(count($product->images) > 1):?>
            <div class="col-nest productImages">

                <?php foreach($product->images as $image):?>
                    <div class="col productThumbnail" data-cols="1/3" data-medium-cols="1/3" data-small-cols="1/3" style="margin:15px 0px;">
                        <img src="<?php echo base_url('uploads/images/full/'.$image['filename']);?>" data-caption="<?php echo htmlentities(nl2br($image['caption']));?>"/>
                    </div>
                <?php endforeach;?>

            </div>
        <?php endif;?>
    </div>


    <div class="col pull-right" data-cols="3/5" data-medium-cols="3/5">
        <div id="productAlerts"></div>
        <?php if(!$product->is_giftcard):?>
            <div class="productPrice">
            <?php if($product->saleprice > 0):?>
                <small class="sale"><?php echo lang('on_sale');?></small>
                <?php echo format_currency($product->saleprice);?>
            <?php else:?>
                <?php echo format_currency($product->price);?>
            <?php endif;?>
            </div>
        <?php endif;?>

        <br class="clear">

        <div class="productDetails">

            <div class="productExcerpt">
                <?php echo (new content_filter($product->excerpt))->display();?>
            </div>

            <?php echo form_open('cart/add-to-cart', 'id="add-to-cart"');?>
            <input type="hidden" name="cartkey" value="<?php echo CI::session()->flashdata('cartkey');?>" />
            <input type="hidden" name="id" value="<?php echo $product->id?>"/>

            <?php if(count($options) > 0): ?>
                <?php foreach($options as $option):
                    $required = '';
                    if($option->required)
                    {
                        $required = ' class="required"';
                    }
                    ?>
                        <div class="col-nest">
                            <div class="col" data-cols="1/3">
                                <label<?php echo $required;?>><?php echo ($product->is_giftcard) ? lang('gift_card_'.$option->name) : $option->name;?></label>
                            </div>
                            <div class="col" data-cols="2/3">
                        <?php
                        if($option->type == 'checklist')
                        {
                            $value  = [];
                            if($posted_options && isset($posted_options[$option->id]))
                            {
                                $value  = $posted_options[$option->id];
                            }
                        }
                        else
                        {
                            if(isset($option->values[0]))
                            {
                                $value  = $option->values[0]->value;
                                if($posted_options && isset($posted_options[$option->id]))
                                {
                                    $value  = $posted_options[$option->id];
                                }
                            }
                            else
                            {
                                $value = false;
                            }
                        }

                        if($option->type == 'textfield'):?>
                            <input type="text" name="option[<?php echo $option->id;?>]" value="<?php echo $value;?>"/>
                        <?php elseif($option->type == 'textarea'):?>
                            <textarea name="option[<?php echo $option->id;?>]"><?php echo $value;?></textarea>
                        <?php elseif($option->type == 'droplist'):?>
                            <select name="option[<?php echo $option->id;?>]">
                                <option value=""><?php echo lang('choose_option');?></option>

                            <?php foreach ($option->values as $values):
                                $selected   = '';
                                if($value == $values->id)
                                {
                                    $selected   = ' selected="selected"';
                                }?>

                                <option<?php echo $selected;?> value="<?php echo $values->id;?>">
                                    <?php if($product->is_giftcard):?>
                                        <?php echo($values->price != 0)?' (+'.format_currency($values->price).') ':''; echo lang($values->name);?>
                                    <?php else:?>
                                        <?php echo($values->price != 0)?' (+'.format_currency($values->price).') ':''; echo $values->name;?>
                                    <?php endif;?>
                                    
                                </option>

                            <?php endforeach;?>
                            </select>
                        <?php elseif($option->type == 'radiolist'):?>
                            <label class="radiolist">
                            <?php foreach ($option->values as $values):

                                $checked = '';
                                if($value == $values->id)
                                {
                                    $checked = ' checked="checked"';
                                }?>
                                <div>
                                    <input<?php echo $checked;?> type="radio" name="option[<?php echo $option->id;?>]" value="<?php echo $values->id;?>"/>
                                    <?php echo($values->price != 0)?'(+'.format_currency($values->price).') ':''; echo $values->name;?>
                                </div>
                            <?php endforeach;?>
                            </label>
                        <?php elseif($option->type == 'checklist'):?>
                            <label class="checklist"<?php echo $required;?>>
                            <?php foreach ($option->values as $values):

                                $checked = '';
                                if(in_array($values->id, $value))
                                {
                                    $checked = ' checked="checked"';
                                }?>
                                <div><input<?php echo $checked;?> type="checkbox" name="option[<?php echo $option->id;?>][]" value="<?php echo $values->id;?>"/>
                                <?php echo($values->price != 0 && $option->name != 'Buy a Sample')?'('.format_currency($values->price).') ':''; echo $values->name;?></div>
                            <?php endforeach; ?>
                            </label>
                        <?php endif;?>
                        </div>
                    </div>
                <?php endforeach;?>
            <?php endif;?>

            <div class="text-right">
            <?php if(!config_item('inventory_enabled') || config_item('allow_os_purchase') || !(bool)$product->track_stock || $product->quantity > 0) : ?>

                <?php if(!$product->fixed_quantity) : ?>

                        <strong>Quantity&nbsp;</strong>
                        <input type="text" name="quantity" value="1" style="width:50px; display:inline"/>&nbsp;
                        <button class="blue" type="button" value="submit" onclick="addToCart($(this));"><i class="icon-cart"></i> <?php echo lang('form_add_to_cart');?></button>
                <?php else: ?>
                        <button class="blue" type="button" value="submit" onclick="addToCart($(this));"><i class="icon-cart"></i> <?php echo lang('form_add_to_cart');?></button>
                <?php endif;?>

            <?php endif;?>
                </div>
            </form>

            <div class="productDescription">
                <?php echo (new content_filter($product->description))->display();?>
            </div>

        </div>

    </div>
</div>


<script>

    function addToCart(btn)
    {
        $('.productDetails').spin();
        btn.attr('disabled', true);
        var cart = $('#add-to-cart');
        $.post(cart.attr('action'), cart.serialize(), function(data){
            if(data.message != undefined)
            {
                $('#productAlerts').html('<div class="alert green">'+data.message+' <a href="<?php echo site_url('checkout');?>"> <?php echo lang('view_cart');?></a> <i class="close"></i></div>');
                updateItemCount(data.itemCount);
                cart[0].reset();
            }
            else if(data.error != undefined)
            {
                $('#productAlerts').html('<div class="alert red">'+data.error+' <i class="close"></i></div>');
            }

            $('.productDetails').spin(false);
            btn.attr('disabled', false);
        }, 'json');
    }

    var banners = false;
    $(document).ready(function(){
        banners = $('#banners').html();
    })

    $('.productImages img').click(function(){
        if(banners)
        {
            $.gumboTray(banners);
            $('.banners').gumboBanner($('.productImages img').index(this));
        }
    });

    $('.tabs').gumboTabs();
</script>

<?php if(count($product->images) > 1):?>
<script id="banners" type="text/template">
    <div class="banners">
        <?php
        foreach($product->images as $image):?>
                <div class="banner" style="text-align:center;">
                    <img src="<?php echo base_url('uploads/images/full/'.$image['filename']);?>" style="max-height:600px; margin:auto;"/>
                    <?php if(!empty($image['caption'])):?>
                        <div class="caption">
                            <?php echo $image['caption'];?>
                        </div>
                    <?php endif; ?>
                </div>
        <?php endforeach;?>
        <a class="controls" data-direction="back"><i class="icon-chevron-left"></i></a>
        <a class="controls" data-direction="forward"><i class="icon-chevron-right"></i></a>
        <div class="banner-timer"></div>
    </div>
</script>
<?php endif;?>


<?php if(!empty($product->related_products)):?>
    <div class="page-header" style="margin-top:30px;">
        <h3><?php echo lang('related_products_title');?></h3>
    </div>
    <?php
    $relatedProducts = [];
    foreach($product->related_products as $related)
    {
        $related->images    = json_decode($related->images, true);
        $relatedProducts[] = $related;
    }
    \GoCart\Libraries\View::getInstance()->show('categories/products', ['products'=>$relatedProducts]); ?>

<?php endif;?>