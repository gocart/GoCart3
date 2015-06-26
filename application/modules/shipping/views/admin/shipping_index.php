<?php pageHeader(lang('shipping_modules')) ?>

<?php if(count($shipping_modules) >0): ?>
    <table class="table table-striped">
        <tbody>
        <?php foreach($shipping_modules as $module): ?>
            <tr>
                <td><?php echo $module['name']; ?></td>
                <td>
                    <span class="btn-group pull-right">
                <?php if(array_key_exists($module['class'], $enabled_modules)): ?>
                    <a class="btn btn-default" href="<?php echo site_url('admin/'.$module['key'].'/form/');?>"><i class="icon-gear"></i></a>
                    <a class="btn btn-danger" href="<?php echo site_url('admin/'.$module['key'].'/uninstall/');?>" onclick="return areyousure();"><i class="icon-times"></i></a>
                <?php else: ?>
                    <a class="btn btn-success" href="<?php echo site_url('admin/'.$module['key'].'/install/');?>"><i class="icon-check"></i></a>
                <?php endif; ?>
                    </span>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
</div>