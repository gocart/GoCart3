<?php if(count($products) == 0):?>

    <h2 style="margin:50px 0px; text-align:center; line-height:30px;">
        <?php echo lang('no_products');?>
    </h2>

<?php else:?>

    <div class="col-nest categoryItems element">
    <?php foreach($products as $product):?>
        <div class="col" data-cols="1/4" data-medium-cols="1/2" data-small-cols="1">
            <?php
            $photo  = theme_img('no_picture.png');

            if(!empty($product->images[0]))
            {
                $primary    = $product->images[0];
                foreach($product->images as $photo)
                {
                    if(isset($photo['primary']))
                    {
                        $primary    = $photo;
                    }
                }

                $photo  = base_url('uploads/images/medium/'.$primary['filename']);
            }
            ?>
            <div onclick="window.location = '<?php echo site_url('/product/'.$product->slug); ?>'" class="categoryItem" >
                <?php if((bool)$product->track_stock && $product->quantity < 1 && config_item('inventory_enabled')):?>
                    <div class="categoryItemNote yellow"><?php echo lang('out_of_stock');?></div>
                <?php elseif($product->saleprice > 0):?>
                    <div class="categoryItemNote red"><?php echo lang('on_sale');?></div>
                <?php endif;?>
                
                <div class="previewImg"><img src="<?php echo $photo;?>"></div>

                <div class="categoryItemDetails">
                    <?php echo $product->name;?>
                </div>

                <?php if(!$product->is_giftcard): //do not display this if the product is a giftcard?>
                <div class="categoryItemHover">
                    <div class="look">
                        <?php echo ( $product->saleprice>0?format_currency($product->saleprice):format_currency($product->price) );?>
                    </div>
                </div>
                <?php endif;?>

            </div>
        </div>
    <?php endforeach;?>
    </div>

<?php endif;?>