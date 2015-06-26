<div id="addressFormWrapper">
    <div class="page-header">
        <?php if($addressCount > 0):?>
            <button class="red pull-right input-xs" type="cancel" onclick="closeAddressForm();"><?php echo lang('form_cancel');?></button>
        <?php endif;?>
        <h3><?php echo lang('address_form');?></h3>
    </div>
    
    <div id="addressError" class="alert red hide"></div>

    <?php echo form_open('addresses/form/'.$id, ['id'=>'addressForm']);?>
        <div class="col-nest">
            <div class="col" data-cols="1">
            <label><?php echo lang('address_company');?></label>
                <?php echo form_input(['name'=>'company', 'value'=> assign_value('company',$company)]);?>
            </div>
        </div>

        <div class="col-nest">
            <div class="col" data-cols="1/2" data-medium-cols="1/2" data-small-cols="1/2">
                <label class="required"><?php echo lang('address_firstname');?></label>
                <?php echo form_input(['name'=>'firstname', 'value'=> assign_value('firstname',$firstname)]);?>
            </div>

            <div class="col" data-cols="1/2" data-medium-cols="1/2" data-small-cols="1/2">
                <label class="required"><?php echo lang('address_lastname');?></label>
                <?php echo form_input(['name'=>'lastname', 'value'=> assign_value('lastname',$lastname)]);?>
            </div>
        </div>
        
        <div class="col-nest">
            <div class="col" data-cols="1/2" data-medium-cols="1/2" data-small-cols="1/2">
                <label class="required"><?php echo lang('address_email');?></label>
                <?php echo form_input(['name'=>'email', 'value'=>assign_value('email',$email)]);?>
            </div>
            <div class="col" data-cols="1/2" data-medium-cols="1/2" data-small-cols="1/2">
                <label class="required"><?php echo lang('address_phone');?></label>
                <?php echo form_input(['name'=>'phone', 'value'=> assign_value('phone',$phone)]);?>
            </div>
        </div>
        <div class="col-nest">
            <div class="col" data-cols="1">
                <label class="required"><?php echo lang('address');?></label>
                <?php
                echo form_input(['name'=>'address1', 'value'=>assign_value('address1',$address1)]);
                echo form_input(['name'=>'address2', 'value'=> assign_value('address2',$address2)]);
                ?>
            </div>
        </div>
        <div class="col-nest">
            <div class="col" data-cols="1">
                <label><?php echo lang('address_country');?></label>
                <?php echo form_dropdown('country_id', $countries_menu, assign_value('country_id', $country_id), 'id="country_id"');?>
            </div>
        </div>
        <div class="col-nest">
            <div class="col" data-cols="2/5" data-medium-cols="2/5" data-small-cols="1/3">
                <label class="required"><?php echo lang('address_city');?></label>
                <?php echo form_input(['name'=>'city', 'value'=>assign_value('city',$city)]);?>
            </div>
            <div class="col" data-cols="2/5" data-medium-cols="2/5" data-small-cols="1/3">
                <label><?php echo lang('address_state');?></label>
                <?php echo form_dropdown('zone_id', $zones_menu, assign_value('zone_id', $zone_id), 'id="zone_id"');?>
            </div>
            <div class="col" data-cols="1/5" data-medium-cols="1/5" data-small-cols="1/3">
                <label class="required"><?php echo lang('address_zip');?></label>
                <?php echo form_input(['maxlength'=>'10', 'name'=>'zip', 'value'=> assign_value('zip',$zip)]);?>
            </div>
        </div>

        <button class="blue" type="submit"><?php echo lang('save_address');?></button>
    </form>

    <script>
    $(function(){
        $('#country_id').change(function(){
            $('#zone_id').load('<?php echo site_url('addresses/get-zone-options');?>/'+$('#country_id').val());
        });

        $('#addressForm').on('submit', function(event){
            $('.addressFormWrapper').spin();
            event.preventDefault();
            $.post($(this).attr('action'), $(this).serialize(), function(data){
                if(data == 1)
                {
                    closeAddressForm();
                }
                else
                {
                    $('#addressFormWrapper').html(data);
                }
            });
        })
    });

    <?php if(validation_errors()):
        $errors = \CI::form_validation()->get_error_array(); ?>

        var formErrors = <?php echo json_encode($errors);?>
        
        for (var key in formErrors) {
            if (formErrors.hasOwnProperty(key)) {
                $('[name="'+key+'"]').parent().append('<div class="form-error text-red">'+formErrors[key]+'</div>')
            }
        }
    <?php endif;?>
    </script>
</div>