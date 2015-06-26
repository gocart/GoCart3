<?php include('header.php');?>

<script type="text/javascript">

<?php if( CI::input()->post('submit') ):?>
$(window).ready(function(){
    $('#iframe_uploader', window.parent.document).height($('body').height());
});
<?php endif;?>

<?php if($file_name):?>
    var filename = '<?php echo $file_name;?>';
    var uploaded = filename.split('.');
    parent.addProductImage(uploaded[0], filename, '', '', '');
<?php endif;?>

</script>

<?php if (!empty($error)): ?>
    <div class="alert alert-warning alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <?php echo $error; ?>
    </div>
<?php endif; ?>

<?php echo form_open_multipart('admin/products/product_image_upload');?>
    <div class="input-group">
        <?php echo form_upload(array('name'=>'userfile', 'class'=>'form-control'));?>
        <span class="input-group-btn">
            <button class="btn btn-primary" name="submit" type="submit"><i class="icon-upload"></i></button>
        </span>
    </div>
    
</form>

<?php include('footer.php');