<div class="addressFormContainer">
    <?php
    $countries = CI::Locations()->get_countries_menu();

    $country_keys = array_keys($countries);
    $zone_menu = [''=>'']+CI::Locations()->get_zones_menu(array_shift($country_keys));

    ?>
    <div id="addressError" class="alert red hide"></div>
    
    <div class="col-nest">
        <?php echo form_open('checkout/add-address', ['id'=>'addressForm']);?>
            <div class="col" data-cols="1">

                <label><?php echo lang('address_company');?></label>
                <?php echo form_input(['name'=>'company']);?>

                <div class="col-nest">
                    <div class="col" data-cols="1/2" data-medium-cols="1/2" data-small-cols="1/2">
                        <label class="required"><?php echo lang('address_firstname');?></label>
                        <?php echo form_input(['name'=>'firstname']);?>
                    </div>
                    <div class="col" data-cols="1/2" data-medium-cols="1/2" data-small-cols="1/2">
                        <label class="required"><?php echo lang('address_lastname');?></label>
                        <?php echo form_input(['name'=>'lastname']);?>
                    </div>
                </div>

                <div class="col-nest">
                    <div class="col" data-cols="1/2" data-medium-cols="1/2" data-small-cols="1/2">
                        <label class="required"><?php echo lang('address_email');?></label>
                        <?php echo form_input(['name'=>'email']);?>
                    </div>

                    <div class="col" data-cols="1/2" data-medium-cols="1/2" data-small-cols="1/2">
                        <label class="required"><?php echo lang('address_phone');?></label>
                        <?php echo form_input(['name'=>'phone']);?>
                    </div>
                </div>

                <label class="required"><?php echo lang('address_country');?></label>
                <?php echo form_dropdown('country_id',$countries,false, 'id="country_id"');?>

                <label class="required"><?php echo lang('address1');?></label>
                <?php echo form_input(['name'=>'address1']);?>
                <?php echo form_input(['name'=>'address2']);?>

                <div class="col-nest">
                    <div class="col" data-cols="1/3">
                        <label class="required"><?php echo lang('address_city');?></label>
                        <?php echo form_input(['name'=>'city']);?>
                    </div>
                    <div class="col" data-cols="1/3">
                        <label class="required"><?php echo lang('address_state');?></label>
                        <?php echo form_dropdown('zone_id',$zone_menu, false, 'id="zone_id" class="address" ');?>
                    </div>
                    <div class="col" data-cols="1/3">
                        <label class="required"><?php echo lang('address_zip');?></label>
                        <?php echo form_input(['name'=>'zip']);?>
                    </div>
                </div>

                <button class="blue" type="submit"><?php echo lang('form_continue');?></button>
            </div>
        </form>
    </div>
</div>
<script>
$(document).ready(function(){
    $('#addressForm').on('submit', function(event){
        $('.cartSummary').spin();
        event.preventDefault();
        $.post($(this).attr('action'), $(this).serialize(), function(data){
            
        });
    })

    $('#country_id').change(function(){
        $.post('<?php echo site_url('locations/get_zone_menu');?>',{id:$('#country_id').val()}, function(data) {
            $('#zone_id').html(data);
        });
    }); 
});

</script>