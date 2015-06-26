<div class="page-header">
    <h3><?php echo lang('payment_methods');?></h3>
</div>

<?php if(count($modules) == 0):?>
    <div class="alert">
        <?php echo lang('error_no_payment_method');?>
    </div>
<?php elseif(GC::getGrandTotal() == 0):?>
    <div class="alert">
        <?php echo lang('no_payment_needed');?>
    </div>

    <button class="blue" onclick="SubmitNoPaymentOrder()"><?php echo lang('submit_order');?></button>

    <script>
    function SubmitNoPaymentOrder()
    {

        $.post('<?php echo base_url('/checkout/submit-order');?>', function(data){
            if(data.errors != undefined)
            {
                var error = '<div class="alert red">';
                $.each(data.errors, function(index, value)
                {
                    error += '<p>'+value+'</p>';
                });
                error += '</div>';

                $.gumboTray(error);
            }
            else
            {
                if(data.orderId != undefined)
                {
                    window.location = '<?php echo site_url('order-complete/');?>/'+data.orderId;
                }
            }
        }, 'json');

    }
    </script>

<?php else: ?>
    <div class="paymentError"></div>
    <div class="col-nest">
        <div class="col" data-cols="1/3">
            <table class="table">
            <?php foreach ($modules as $key => $module):?>
                <?php if($module['class']->isEnabled()):?>
                    <tr onclick="$(this).find('input').prop('checked', true).trigger('change');">
                        <td style="width:20px;"><input type="radio" name="paymentMethod" value="payment-<?php echo $key;?>"></td>
                        <td><?php echo $module['class']->getName();?></td>
                    </tr>
                <?php endif;?>
            <?php endforeach;?>
            </table>
        </div>
        <div class="col" data-cols="2/3">

            <?php foreach ($modules as $key => $module):?>
                <?php if($module['class']->isEnabled()):?>
                    <div id="payment-<?php echo $key;?>" class="paymentMethod">
                        <?php echo $module['class']->checkoutForm();?>
                    </div>
                <?php endif;?>
            <?php endforeach;?>

        </div>

    <script>
        $('[name="paymentMethod"]').change(function(){
            var paymentMethod = $(this);
            $('.paymentMethod').hide();
            $( '#'+paymentMethod.val() ).fadeIn(100);
        });
    </script>
<?php endif;?>
