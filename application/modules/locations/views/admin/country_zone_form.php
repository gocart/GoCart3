<?php pageHeader(lang('zone_form')) ?>

<?php echo form_open('admin/locations/zone_form/'.$id); ?>

    <div class="form-group">
        <label for="country_id"><?php echo lang('country');?></label>
        <?php
        $country_ids = [];
        foreach($countries as $c)
        {
            $country_ids[$c->id] = $c->name;
        }
        
        ?>
        <?php echo form_dropdown('country_id', $country_ids, assign_value('country_id', $country_id) ,'class="form-control"');?>
    </div>

    <div class="form-group">
        <label for="name"><?php echo lang('name');?></label>
        <?php echo form_input(['name'=>'name', 'value'=>assign_value('name', $name), 'class'=>'form-control']); ?>
    </div>

    <div class="form-group">
        <label for="code"><?php echo lang('code');?></label>
        <?php echo form_input(['name'=>'code', 'class' => 'form-control', 'value'=>assign_value('code', $code)]);?>
    </div>

    <div class="form-group">
        <label for="code"><?php echo lang('tax');?></label>
        <div class="input-group">
            <span class="input-group-addon">%</span>
            <?php echo form_input(['name'=>'tax', 'class' =>'form-control', 'maxlength'=>'10', 'value'=>assign_value('tax', $tax)]);?>
        </div>
    </div>

    <div class="form-group">
        <div class="checkbox">
            <label><?php echo form_checkbox(['name'=>'status', 'value'=>1, 'checked'=>set_checkbox('status', 1, (bool)$status)]); ?> <?php echo lang('enabled');?></label>
        </div>
    </div>

    <button type="submit" class="btn btn-primary"><?php echo lang('save');?></button>

</form>

<script type="text/javascript">
$('form').submit(function() {
    $('.btn .btn-primary').attr('disabled', true).addClass('disabled');
});
</script>