<button class="addressForm blue pull-right" data-address-id="0"><?php echo lang('add_address');?></button>

<script>
    $('.addressForm').click(function(){
        $.post('<?php echo site_url('addresses/form');?>/'+$(this).attr('data-address-id'), function(data){
            $.gumboTray(data);
        });
    });
    function deleteAddress(id)
    {
        if( confirm('<?php echo lang('delete_address_confirmation');?>') )
        {
            $.post('<?php echo site_url('addresses/delete');?>/'+id, function(){
                loadAddresses();
            });
        }
    }
</script>

<h3><?php echo lang('address_manager');?></h3>

<?php if(count($addresses) > 0):?>
    
    <table class="table zebra">
    <?php foreach($addresses as $a):?>
        <tr>
            <td>
                <?php echo format_address($a, true);?>
            </td>
            <td class="text-right">
                <button type="button" class="addressForm btn-primary black" data-address-id="<?php echo $a['id'];?>"><?php echo lang('form_edit');?></button>
                <button class="red" onclick="deleteAddress(<?php echo $a['id'];?>)"><?php echo lang('form_delete');?></a>
            </td>
        </tr>
    <?php endforeach;?>
    </table>
<?php endif;?>