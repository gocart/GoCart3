<?php pageHeader(lang('address_form'));?>

<?php echo form_open('admin/customers/address_form/'.$customer_id.'/'.$id);?>

	<div class="row">
		<div class="col-md-3">
			<div class="form-group">
				<label><?php echo lang('company');?></label>
				<?php echo form_input(['name'=>'company','class'=>'form-control', 'value'=> assign_value('company',$company)]);?>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-md-3">
			<div class="form-group">
				<label><?php echo lang('firstname');?></label>
				<?php echo form_input(['name'=>'firstname', 'class'=>'form-control','value'=> assign_value('firstname',$firstname)]);?>
			</div>
		</div>
		<div class="col-md-3">
			<div class="form-group">
				<label><?php echo lang('lastname');?></label>
				<?php echo form_input(['name'=>'lastname', 'class'=>'form-control','value'=> assign_value('lastname',$lastname)]);?>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-md-3">
			<div class="form-group">
				<label><?php echo lang('email');?></label>
				<?php echo form_input(['name'=>'email', 'class'=>'form-control','value'=>assign_value('email',$email)]);?>
			</div>
		</div>
		<div class="col-md-3">
			<div class="form-group">
				<label><?php echo lang('phone');?></label>
				<?php echo form_input(['name'=>'phone', 'class'=>'form-control','value'=> assign_value('phone',$phone)]);?>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-md-6">
			<div class="form-group">
				<label><?php echo lang('country');?></label>
				<?php echo form_dropdown('country_id', $countries_menu, assign_value('country_id', $country_id), 'id="f_country_id" class="form-control"');?>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-md-6">
			<div class="form-group">
				<label><?php echo lang('address');?></label>
				<?php echo form_input(['name'=>'address1', 'class'=>'form-control','value'=>assign_value('address1',$address1)]);?>
			</div>
			<div class="form-group">
				<?php echo form_input(['name'=>'address2', 'class'=>'form-control','value'=> assign_value('address2',$address2)]);?>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-md-2">
			<div class="form-group">
				<label><?php echo lang('city');?></label>
				<?php echo form_input(['name'=>'city','class'=>'form-control', 'value'=>assign_value('city', $city)]);?>
			</div>
		</div>
		<div class="col-md-3">
			<div class="form-group">
				<label><?php echo lang('state');?></label>
				<?php echo form_dropdown('zone_id', $zones_menu, assign_value('zone_id', $zone_id), 'id="f_zone_id" class="form-control"');?>
			</div>
		</div>
		<div class="col-md-1">
			<div class="form-group">
				<label><?php echo lang('zip');?></label>
				<?php echo form_input(['maxlength'=>'10', 'class'=>'form-control', 'name'=>'zip', 'value'=> assign_value('zip',$zip)]);?>
			</div>
		</div>
	</div>

	<input class="btn btn-primary" type="submit" value="<?php echo lang('save');?>"/>

	<script type="text/javascript">
	$(document).ready(function(){
		$('#f_country_id').change(function(){
			$.post('<?php echo site_url('admin/locations/get_zone_menu');?>',{id:$('#f_country_id').val()}, function(data) {
			  $('#f_zone_id').html(data);
			});

		});
	});
	</script>
</form>
