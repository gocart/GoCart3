<?php pageHeader(sprintf(lang('zone_area_form'), $zone->name)) ?>

<?php echo form_open('admin/locations/zone_area_form/'.$zone_id.'/'.$id); ?>

    <div class="form-group">
        <label for="code"><?php echo lang('code');?></label>
        <?php
        $data   = array( 'name'=>'code', 'value'=>assign_value('code', $code), 'class'=>'form-control');
        echo form_input($data);
        ?>
    </div>
    
    <div class="form-group">
        <label for="code"><?php echo lang('tax');?></label>
        <div class="input-group">
          <span class="input-group-addon">%</span>
              <?php
              $data = array('name'=>'tax', 'class'=>'form-control', 'maxlength'=>'10', 'value'=>assign_value('tax', $tax));
              echo form_input($data);
            ?>  
        </div>
    </div>

    <button type="submit" class="btn btn-primary"><?php echo lang('save');?></button>

</form>

<script type="text/javascript">
$('form').submit(function() {
    $('.btn .btn-primary').attr('disabled', true).addClass('disabled');
});
</script>