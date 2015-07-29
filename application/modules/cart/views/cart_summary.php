<div class="cartSummary">
    <div class="cartSummaryTitle"><?php echo lang('your_cart');?></div>

    <div class="alert red" id="summaryErrors" style="display:none;"></div>
    <?php
    $cartItems = GC::getCartItems();
    $options = CI::Orders()->getItemOptions(GC::getCart()->id);

    $charges = [];

    $charges['giftCards'] = [];
    $charges['coupons'] = [];
    $charges['tax'] = [];
    $charges['shipping'] = [];
    $charges['products'] = [];

    foreach ($cartItems as $item)
    {
        if($item->type == 'gift card')
        {
            $charges['giftCards'][] = $item;
            continue;
        }
        elseif($item->type == 'coupon')
        {
            $charges['coupons'][] = $item;
            continue;
        }
        elseif($item->type == 'tax')
        {
            $charges['tax'][] = $item;
            continue;
        }
        elseif($item->type == 'shipping')
        {
            $charges['shipping'][] = $item;
            continue;
        }
        elseif($item->type == 'product')
        {
            $charges['products'][] = $item;
        }
    }

    if(count($charges['products']) == 0)
    {
        echo '<script>location.reload();</script>';
    }

    foreach($charges['products'] as $product):

        $photo = theme_img('no_picture.png', lang('no_image_available'));
        $product->images = array_values(json_decode($product->images, true));

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

            $photo = '<img src="'.base_url('uploads/images/full/'.$primary['filename']).'"/>';
        }

        ?>
        
        <div class="cartItem" id="cartItem-<?php echo $product->id;?>">
            <div class="col-nest">
                <div class="col" data-cols="1">
                    <div class="cartItemName"><?php echo $product->name; ?></div>
                </div>
            </div>

            <?php if(!empty($product->coupon_code)):?>
                <div class="col-nest">
                    <div class="col" data-cols="3/4">
                        <small><?php echo lang('coupon').': '.$product->coupon_code;?></small>
                    </div>
                    <div class="col text-right text-red" data-cols="1/4">
                        <strong><small><?php echo '-'.format_currency(($product->coupon_discount * $product->coupon_discount_quantity));?></small></strong>
                    </div>
                </div>
            <?php endif;?>

            <div class="col-nest">
                <div class="col" data-cols="1/5">
                    <?php echo $photo;?>
                </div>

                <div class="col" data-cols="4/5">
                    <?php echo (!empty($product->sku))?'<div class="cartItemSku">'.lang('sku').': '.$product->sku.'</div>':''?>
                    <?php
                    if(isset($options[$product->id]))
                    {
                        foreach($options[$product->id] as $option):?>
                            <div class="cartItemOption"><?php echo ($product->is_giftcard) ? lang('gift_card_'.$option->option_name) : $option->option_name;?> : <?php echo($option->price > 0)?'['.format_currency($option->price).']':'';?> <?php echo $option->value;?></div>
                        <?php endforeach;
                    }
                    ?>
                </div>
            </div>
            <div class="col-nest">
                <div class="col" data-cols="1/2">
                    <strong><small><?php echo $product->quantity.' &times; '.format_currency($product->total_price)?></small></strong>
                </div>
                <div class="col text-right" data-cols="1/4">
                    <?php if(CI::uri()->segment(1) == 'cart' && !$product->fixed_quantity): ?>
                        <input class="input-sm quantityInput" style="margin:0;" <?php echo($product->fixed_quantity)?'disabled':''?> data-product-id="<?php echo $product->id;?>" data-orig-val="<?php echo $product->quantity ?>" id="qtyInput<?php echo $product->id;?>" value="<?php echo $product->quantity ?>" type="text">
                    <?php else: ?>
                        &times; <?php echo $product->quantity; ?>
                    <?php endif;?>

                    <div class="cartItemRemove">
                        <a class="text-red" onclick="updateItem(<?php echo $product->id;?>, 0);" style="cursor:pointer"><?php echo lang('remove');?></a>
                    </div>
                </div>
                <div class="col text-right" data-cols="1/4">
                    <strong><small><?php echo format_currency($product->total_price * $product->quantity); ?></small></strong>
                </div>
            </div>
        </div>
    <?php endforeach;?>

    <?php if(count($charges['products']) > 0):?>

        <div class="cartSummaryTotals">
            <div class="col-nest">
                <div class="col" data-cols="2/3" data-medium-cols="2/3" data-small-cols="2/3">
                    <div class="cartSummaryTotalsKey"><?php echo lang('subtotal');?>:</div>
                </div>
                <div class="col" data-cols="1/3" data-medium-cols="1/3" data-small-cols="1/3">
                    <div class="cartSummaryTotalsValue"><?php echo format_currency(GC::getSubtotal());?></div>
                </div>
            </div>

        <?php if(count($charges['shipping']) > 0 || count($charges['tax']) > 0 ):?>

                <?php foreach($charges['shipping'] as $shipping):?>
                    <div class="col-nest">
                        <div class="col" data-cols="2/3" data-medium-cols="2/3" data-small-cols="2/3">
                            <div class="cartSummaryTotalsKey"><?php echo lang('shipping');?>: <?php echo $shipping->name; ?></div>
                        </div>
                        <div class="col" data-cols="1/3" data-medium-cols="1/3" data-small-cols="1/3">
                            <div class="cartSummaryTotalsValue">
                                <?php echo format_currency($shipping->total_price); ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach;?>

                <?php foreach($charges['tax'] as $tax):?>
                    <div class="col-nest">
                        <div class="col" data-cols="2/3" data-medium-cols="2/3" data-small-cols="2/3">
                            <div class="cartSummaryTotalsKey"><?php echo lang('taxes');?>: <?php echo $tax->name; ?></div>
                        </div>
                        <div class="col" data-cols="1/3" data-medium-cols="1/3" data-small-cols="1/3">
                            <div class="cartSummaryTotalsValue">
                                <?php echo format_currency($tax->total_price); ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach;?>
        <?php endif;?>

        <?php if(count($charges['giftCards']) > 0):?>
            </div>
            <?php foreach($charges['giftCards'] as $giftCard):?>
                <div class="cartItem">
                    <div class="col-nest">
                        <div class="col" data-cols="3/4">
                            <div class="cartItemName"><?php echo $giftCard->name; ?></div>
                            <small><?php echo $giftCard->description; ?><br>
                            <?php echo $giftCard->excerpt; ?></small>
                        </div>
                        <div class="col text-right" data-cols="1/4" data-medium-cols="1/4" data-small-cols="1/4" style="white-space:nowrap;">
                            <?php echo format_currency($giftCard->total_price); ?>
                            <div class="cartItemRemove">
                                <a class="text-red" onclick="updateItem(<?php echo $giftCard->id;?>, 0);" style="cursor:pointer"><?php echo lang('remove');?></a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach;?>
            <div class="cartSummaryTotals">
        <?php endif;?>

            <div class="col-nest">
                <div class="col" data-cols="2/3" data-medium-cols="2/3" data-small-cols="2/3">
                    <div class="cartSummaryTotalsKey"><?php echo lang('grand_total');?>:</div>
                </div>
                <div class="col" data-cols="1/3" data-medium-cols="1/3" data-small-cols="1/3">
                    <div class="cartSummaryTotalsValue"><?php echo format_currency(GC::getGrandTotal());?></div>
                </div>
            </div>
        </div>
    <?php endif;?>

    <?php foreach($charges['coupons'] as $coupon):?>
        <div class="cartItem">
            <div class="col-nest">
                <div class="col" data-cols="3/4">
                    <div class="cartSummaryTotalsKey"><?php echo lang('coupon');?>: <?php echo $coupon->description; ?></div>
                </div>
                <div class="col text-right" data-cols="1/4" data-medium-cols="1/4" data-small-cols="1/4" style="white-space:nowrap;">
                    <div class="cartItemRemove">
                        <a class="text-red" onclick="updateItem(<?php echo $coupon->id;?>, 0);" style="cursor:pointer"><?php echo lang('remove');?></a>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach;?>

    <div class="cartPromotions">

        <div class="couponMessage"></div>
        <div class="btn-group input-sm">
            <div class="priority"><input type="text" id="coupon" placeholder="<?php echo lang('coupon_label');?>"></div>
            <a class="btn green" type="button" onclick="submitCoupon()"><i class="icon-plus"></i></a>
        </div>

        <div class="giftCardMessage"></div>
        <div class="btn-group input-sm">
            <div class="priority"><input type="text" id="giftCard" placeholder="<?php echo lang('gift_card_label');?>"></div>
            <a class="btn green" type="button" onclick="submitGiftCard()"><i class="icon-plus"></i></a>
        </div>

    </div>
</div>

<script>

var inventoryCheck = <?php echo json_encode($inventoryCheck);?>

function setInventoryErrors(checks)
{
    //remove pre-existing errors
    $('.errorAlert').removeClass('errorAlert');
    $('.summaryStockAlert').remove();

    //reprocess
    $.each(checks, function(key, val) {
        $('#cartItem-'+key).addClass('errorAlert').prepend('<div class="summaryStockAlert">'+val+'</div>');
    });
}

setInventoryErrors(inventoryCheck);

updateItemCount(<?php echo GC::totalItems();?>);

var newGrandTotalTest = <?php echo (GC::getGrandTotal() > 0)?1:0;?>;
if(newGrandTotalTest != grandTotalTest)
{
    getPaymentMethods();
    grandTotalTest = newGrandTotalTest; //reset grand total test.
}

$('.quantityInput').bind('blur keyup', function(e){
    if(e.type == 'blur' || e.which == 13)
    {
        updateItem($(this).attr('data-product-id'), $(this).val(), $(this).attr('data-orig-val'));
    }
}).bind('focus', function(e){
    lastInput = $(this);
    lastValue = lastInput.val();
});
function updateItem(id, newQuantity, oldQuantity)
{
    $('#summaryErrors').html('').hide();

    if(newQuantity != oldQuantity)
    {
        var active = $(document.activeElement).attr('id');

        if(newQuantity == 0)
        {
            if(!confirm('<?php echo lang('remove_item');?>')){
                return false;
            }
            else
            {
                if(oldQuantity != undefined) //if the "remove" button is used we don't need to bother with this.
                {
                    $('#qtyInput'+id).val(oldQuantity);
                }
            }
        }
        $('#cartSummary').spin();
        $.post('<?php echo site_url('cart/update-cart');?>', {'product_id':id, 'quantity':newQuantity}, function(data){

            if(data.error != undefined)
            {
                $('#summaryErrors').text(data.error).show();
                //there was an error. reset it.
                lastInput.val(lastValue).focus();
            }
            else
            {
                var elem = $(document.activeElement).attr('id');
                getCartSummary(function(){
                    $('#'+elem).focus();
                });
            }
            
        }, 'json');
    }
}

$('#coupon').keyup(function(event){
    var code = event.keyCode || event.which;
    if(code == 13) {
        submitCoupon();
    }
});

$('#giftCard').keyup(function(event){
    var code = event.keyCode || event.which;
    if(code == 13) {
        submitGiftCard();
    }
});

function submitGiftCard()
{
    $('#cartSummary').spin();
    $.post('<?php echo site_url('cart/submit-gift-card');?>', {'gift_card':$('#giftCard').val()}, function(data){
        if(data.error != undefined)
        {
            $('.giftCardMessage').html($('<div class="alert red"></div>').text(data.error).prepend('<i class="close"></i>'));
            $('#cartSummary').spin(false);
            $('#giftCard')[0].setSelectionRange(0, $('#giftCard').val().length);
        }
        else
        {
            getCartSummary(function(){
                $('.giftCardMessage').html($('<div class="alert green"></div>').text(data.message).prepend('<i class="close"></i>'))
            })
        }

    }, 'json');
}

function submitCoupon()
{
    $('#cartSummary').spin();
    $.post('<?php echo site_url('cart/submit-coupon');?>', {'coupon':$('#coupon').val()}, function(data){
        if(data.error != undefined)
        {
            $('.couponMessage').html($('<div class="alert red"></div>').text(data.error).prepend('<i class="close"></i>'));
            $('#cartSummary').spin(false);
            $('#coupon')[0].setSelectionRange(0, $('#coupon').val().length);
        }
        else
        {
            getCartSummary(function(){
                $('.couponMessage').html($('<div class="alert green"></div>').text(data.message).prepend('<i class="close"></i>'))
            })
        }
    }, 'json');
}

</script>
