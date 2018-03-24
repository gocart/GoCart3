<div class="page-header"><?php echo lang('twocheckout_api_desc') ?></div>

<div id="twocheckoutapiContainer">  
    <form id="twocheckoutapiCardForm" method="post">
        <div>
            <label><?php echo lang('card_number');?></label>
            <input type="tel" size="20" class="twocheckoutapiCardNumber"/>
        </div>

        <div class="col-nest">
            <div class="col" data-cols="1/2" data-medium-cols="1/2" data-small-cols="1/2">
                <label><?php echo lang('card_month').' / '.lang('card_year');?></label>
                <input type="tel" class="twocheckoutapiCardExpiry" placeholder="MM/YY">
            </div>
            <div class="col" data-cols="1/2" data-medium-cols="1/2" data-small-cols="1/2">
                <label><?php echo lang('card_code');?></label>
                <input type="tel" size="4" class="twocheckoutapiCardCVC"/>
            </div>
        </div>
        
        <button id="twocheckoutapiSubmitButton" class="blue" type="button"><?php echo lang('submit_order');?></button>

    </form>
</div>

<script src="<?php echo base_url('addons/twocheckout_api/assets/js/jquery.payment.js') ?>"></script>
<style type="text/css">
    #twocheckoutapiContainer {
        position:relative;
    }
</style>

<script>
    $('.twocheckoutapiCardNumber').payment('formatCardNumber');
    $('.twocheckoutapiCardExpiry').payment('formatCardExpiry');
    $('.twocheckoutapiCardCVC').payment('formatCardCVC');

    $('#twocheckoutapiSubmitButton').on('click', submitTwocheckoutApiCheckout);

    $.fn.toggleTwocheckoutApiInputError = function(error) {
        this.parent().append('<div class="form-error text-red">'+error+'</div>');
    };

    function submitTwocheckoutApiCheckout()
    {
        $('#twocheckoutapiContainer').spin();
        $('#twocheckoutapiSubmitButton').off('click', submitTwocheckoutApiCheckout).attr('disabled', true);
        
        $('#twocheckoutapiCardForm').find('.form-error').remove();

        // Call our token request function
        var cardType = $.payment.cardType($('.twocheckoutapiCardNumber').val());
        var fail = false;

        if( !$.payment.validateCardNumber( $('.twocheckoutapiCardNumber').val() ) )
        {
            $('.twocheckoutapiCardNumber').toggleTwocheckoutApiInputError('<?php echo lang('invalid_card_number');?>');
            fail = true;
        }

        if( !$.payment.validateCardExpiry( $('.twocheckoutapiCardExpiry').payment('cardExpiryVal') ) )
        {
            $('.twocheckoutapiCardExpiry').toggleTwocheckoutApiInputError('<?php echo lang('invalid_expiration');?>');
            fail = true;
        }

        if( !$.payment.validateCardCVC( $('.twocheckoutapiCardCVC').val(), cardType ) )
        {
            $('.twocheckoutapiCardCVC').toggleTwocheckoutApiInputError('<?php echo lang('invalid_cvv');?>');
            fail = true;
        }

        if(fail)
        {
            $('#twocheckoutapiSubmitButton').on('click', submitTwocheckoutApiCheckout).attr('disabled', false);
            $('#twocheckoutapiContainer').spin(false);
            return;
        }

        $('#twocheckoutapiSubmitButton').attr('disabled', true).addClass('disabled');

        tokenRequest();
        return false;
    }

    var tokenRequest = function() {
        var expiration = $('.twocheckoutapiCardExpiry').payment('cardExpiryVal');
        // Setup token request arguments
        var args = {
            sellerId: "<?php echo $settings['sid']; ?>",
            publishableKey: "<?php echo $settings['public']; ?>",
            ccNo: $(".twocheckoutapiCardNumber").val(),
            cvv: $(".twocheckoutapiCardCVC").val(),
            expMonth: (expiration.month || 0),
            expYear: (expiration.year || 0)
        };

        // Make the token request
        TCO.requestToken(successCallback, errorCallback, args);
    };
    
    var successCallback = function(data) {
        // IMPORTANT: Here we call `submit()` on the form element directly instead of using jQuery to prevent and infinite token request loop.
        $.post('<?php echo site_url('/twocheckoutapi/process-payment');?>', {token: data.response.token.token}, function(data){
            
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
                    window.location = '<?php echo site_url('order-complete');?>/'+data.orderId;
                }
            }

            $('#twocheckoutapiSubmitButton').on('click', submitTwocheckoutApiCheckout).attr('disabled', false);
            $('#twocheckoutapiContainer').spin(false);
            
            return false;

        }, 'json');
    };

    // Called when token creation fails.
    var errorCallback = function(data) {

        //re-enable the buttons
        $('#twocheckoutapiContainer').spin(false);
        $('#twocheckoutapiSubmitButton').on('click', submitTwocheckoutApiCheckout).attr('disabled', false);

        // Retry the token request if ajax call fails
        if (data.errorCode === 200) {
             // This error code indicates that the ajax call failed. We recommend that you retry the token request.
        } else {
            $.gumboTray(data.errorMsg);
        }
    };

    $.getScript('https://www.2checkout.com/checkout/api/2co.min.js', function(){
        // Pull in the public encryption key for our environment
        TCO.loadPubKey('<?php echo ($settings['demo'] == 'YES') ? 'sandbox': 'production'; ?>');
    });
    
</script>