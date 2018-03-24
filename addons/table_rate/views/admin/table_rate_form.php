<div class="page-header">
    <h2><?php echo lang('table_rate');?></h2>
</div>

<?php echo form_open_multipart('admin/table-rate/form'); ?>

    <div class="row">
        <div class="col-md-12 form-group">
             <div class="pull-right">  
               <button class="btn btn-primary pull-right" type="button" onclick='add_new_table()'><?php echo lang('add_table');?></button>
            </div>
            <div class="col-md-4 pull-right">
              <select name="enabled" class="form-control">
                <option value="1"<?php echo((bool)$enabled)?' selected="selected"':'';?>><?php echo lang('enabled');?></option>
                <option value="0"<?php echo((bool)$enabled)?'':' selected="selected"';?>><?php echo lang('disabled');?></option>
              </select>
            </div>
            <div class="col-md-4 pull-right">
              <input type="text" id="add_name_input" class="form-control" placeholder="<?php echo lang('table_name');?>"/>
            </div>
    </div>
    </div>
    <div class="form-group">
        <ul id="tables" class="nav nav-pills nav-stacked">

        </ul>
    </div>

    <script type="text/javascript">
        var rates = <?php echo json_encode($rates);?>;
        var table_count = 0;
        var rate_count = 0;
        $(document).ready(function(){
            $.each(rates, function(index, value)
            {
                var table_id = table_count;
                add_table(value);
                $.each(value.rates, function(index, value)
                {
                    
                    add_rate(table_id, index, value);
                });
            });
        });
        function add_new_table()
        {
            var table       = new Array();
            table.name      = $('#add_name_input').val();
            table.country   = 0;
            table.method    = 0;
            
            add_table(table);
            
        }
        function add_table(value)
        {
            var rate = $('#table_form_template').html().split('var_count').join(table_count).split('var_name').join(value.name);
            $('#tables').append(rate);
            $('#country_'+table_count).val(value.country);
            $('#method_'+table_count).val(value.method);
            $('#name_'+table_count).val(value.name);
            table_count++;
        }
        
        function add_rate(table, from, rate)
        {
            var rate_row = $('#rate_form_template').html().split('var_count').join(rate_count).split('var_table_count').join(table);
        
            $('#rates_'+table).append(rate_row);
            $('#from_line_'+rate_count).val(from);
            $('#rate_line_'+rate_count).val(rate);
        
            rate_count++;
        }
        
        function delete_table(id)
        {
            if(confirm('<?php echo lang('delete_table');?>'))
            {
                $('#table_'+id).remove();
            }
        }
        function delete_rate(id)
        {
            if(confirm('<?php echo lang('delete_rate');?>'))
            {
                $('#rate_'+id).remove();
            }
        }
        function toggle_table(table)
        {
            $('#table_details_'+table).toggle();
        }
        
        $('form').submit(function(){
            $('#kill_on_save').remove();
        });
    </script>

    <div id="kill_on_save">
        <div id="table_form_template" style="display:none">
        
            <li class="active"id="table_var_count">
                <button onclick="delete_table(var_count);" type="button" class="btn btn-primary btn-danger pull-right" style="margin-top:3px; margin-right:3px;"><i class="icon-times "></i></button>
                <a href="#" onclick="toggle_table(var_count); return false;">var_name</a>
                <ul id="table_details_var_count" class="row table_details" style="display:none; list-style-type:none;">
                    <li class="col-md-12">
                        <div class="row">
                            <div class="col-md-12">
            
                                <div class="row">
                                    <div class="col-md-3" style="margin-top:5px;">
                                        <label><?php echo lang('table_name');?></label>
                                        <?php echo form_input(array('name'=>'rate[var_count][name]', 'class'=>'form-control', 'id'=>'name_var_count'));?>
                        
                                        <label><?php echo lang('method');?></label>
                                        <?php
                                        $options = array('price'=>lang('price'),'weight'=>lang('weight'));
                                        echo form_dropdown('rate[var_count][method]', $options, '', 'class="form-control" id="method_var_count"');
                                        ?>
                        
                                        <label><?php echo lang('country');?></label>
                                        <?php
                                            echo form_multiselect('rate[var_count][country][]', $countries, '', 'class="form-control" id="country_var_count"');
                                        ?>
                                    </div>
                                    <div class="col-md-9">
                                        <div class="row">
                                            <div class="col-md-9">
                                                <div class="form-inline pull-right" style="margin:10px 0px;">
                                                    <div class="form-group">
                                                    <input class="form-control" type="input" class="col-md-1" placeholder="<?php echo lang('from');?>" id="from_field_var_count"/>
                                                    </div>
                                                    <div class="form-group">
                                                    <input type="input" class="form-control col-md-1" step="0.01" placeholder="<?php echo lang('rate');?>" id="rate_field_var_count"/>
                                                    </div>
                                                    <button type="button" class="btn btn-primary" onclick="add_rate(var_count, $('#from_field_var_count').val(),$('#rate_field_var_count').val()); $('#from_field_var_count').val('');$('#rate_field_var_count').val('')"><i class="icon-plus"></i> <?php echo lang('add_rate');?></button>
                                                </div>
                                            </div>
                                        </div>
                                        <table class="table table-striped">
                                            <tbody id="rates_var_count">
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
            
                            </div>
                        </div>
                    </li>
                </ul>
            </li>
        
        </div>

        <table style="display:none;">
            <tbody id="rate_form_template" >
                <tr id="rate_var_count" class="form-inline">
                    <td><?php echo lang('from');?> </td>
                    <td><input id="from_line_var_count" type="input" name="rate[var_table_count][rates][var_count][from]" class="col-md-1 form-control"/></td>
                    <td><?php echo lang('rate');?> </td>
                    <td><input id="rate_line_var_count" type="number" step="0.01" name="rate[var_table_count][rates][var_count][rate]" class="col-md-1 form-control"/></td>
                    <td>
                        <span class="pull-right">
                            <button class="btn btn-primary btn-danger" type="button" onclick="delete_rate(var_count)"><i class="icon-times "></i></button>
                        </span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary"><?php echo lang('save');?></button>
    </div>
</form>

<script type="text/javascript">
$('form').submit(function() {
    $('.btn .btn-primary').attr('disabled', true).addClass('disabled');
});
</script>