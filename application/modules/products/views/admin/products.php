<?php pageHeader(lang('products')); ?>

<?php
//set "code" for searches
if(!empty($term)):
    $term = json_decode($term);
    if(!empty($term->term) || !empty($term->category_id)):?>
        <div class="alert alert-info">
            <?php echo sprintf(lang('search_returned'), intval($total));?>
        </div>
    <?php endif;?>
<?php endif;?>

<script type="text/javascript">
function areyousure()
{
    return confirm('<?php echo lang('confirm_delete_product');?>');
}
</script>
<style type="text/css">
    .pagination {
        margin:0px;
        margin-top:-3px;
    }
</style>
<div class="row">
    <div class="col-md-12">
        <div class="row">
            <div class="col-md-4">
                <?php echo CI::pagination()->create_links();?>  &nbsp;
            </div>
            <div class="col-md-8">
                <?php echo form_open('admin/products', 'class="form-inline form-group" style="float:right"');?>
                    <div class="form-group">
                        <?php
                        unset($categories[-1]);
                        unset($categories[0]);

                        if(!empty($categories))
                        {
                            echo '<select class="form-control" name="category_id">';
                            echo '<option value="">'.lang('filter_by_category').'</option>';
                            foreach($categories as $key=>$name)
                            {
                                echo '<option value="'.$key.'">'.$name.'</option>';
                            }
                            echo '</select>';

                        }?>
                    </div>
                     <div class="form-group">
                        <input type="text" class="form-control" name="term" placeholder="<?php echo lang('search_term');?>" />
                    </div>
                        <button class="btn btn-default" name="submit" value="search"><?php echo lang('search')?></button>
                        <a class="btn btn-default" href="<?php echo site_url('admin/products');?>">Reset</a>
                </form>
            </div>
        </div>
    </div>
</div>

<?php echo form_open('admin/products/bulk_save', array('id'=>'bulk_form'));?>
<div class="text-right form-group">
    <button class="btn btn-primary" href="#"><i class="icon-ok"></i> <?php echo lang('bulk_save');?></button>
    <a class="btn btn-primary" style="font-weight:normal;"href="<?php echo site_url('admin/products/form');?>"><i class="icon-plus"></i> <?php echo lang('add_new_product');?></a>
    <a class="btn btn-primary" style="font-weight:normal;"href="<?php echo site_url('admin/products/gift-card-form');?>"><i class="icon-plus"></i> <?php echo lang('add_new_gift_card');?></a>
</div>
    <table class="table table-striped">
        <thead>
            <tr>
                <?php
                    foreach(['sku', 'name', 'quantity'] as $thead)
                    {
                        echo '<th>';

                        $uristring = 'admin/products/'.$rows.'/'.$thead.'/';
                        $icon = '';
                        if ($order_by == $thead)
                        {
                            if($sort_order == 'asc')
                            {
                                $uristring .= 'desc/';
                                $icon   = ' <i class="icon-sort-alt-up"></i>';
                            }
                            else
                            {
                                $uristring .= 'asc/';
                                $icon   = ' <i class="icon-sort-alt-down"></i>';
                            }
                        }
                        else
                        {
                            $uristring .='asc/';
                        }

                        echo '<a href="'.site_url($uristring.$code.'/'.$page).'">'.lang($thead).$icon.'</a></th>';
                    }
                ?>
                <?php foreach($groups as $group):?>
                    <th><?php echo $group->name;?></th>
                <?php endforeach; ?>
                <th style="width:16%">

                </th>
            </tr>
        </thead>
        <tbody>
        <?php echo (count($products) < 1)?'<tr><td style="text-align:center;" colspan="7">'.lang('no_products').'</td></tr>':''?>
    <?php foreach ($products as $product):?>
            <tr>
                <td><?php echo $product->sku;?></td>
                <td><?php echo $product->name;?></td>
                <td>
                    <?php if ((bool)$product->track_stock):?>
                        <?php echo form_input(['name'=>'product['.$product->id.'][quantity]', 'value'=>assign_value('quantity', $product->quantity), 'class'=>'form-control tableInput']);?>
                    <?php endif; ?>
                </td>
                <?php foreach($groups as $group):?>
                    <td><?php echo ($product->{'enabled_'.$group->id} == '1') ? lang('enabled') : lang('disabled'); ?></td>
                <?php endforeach; ?>
                <td class="text-right">
                    <div class="btn-group">
                        <a class="btn btn-default" href="<?php echo ($product->is_giftcard) ? site_url('admin/products/gift-card-form/'.$product->id) : site_url('admin/products/form/'.$product->id);?>" alt="<?php echo lang('edit');?>"><i class="icon-pencil"></i></a>
                        <a class="btn btn-default" href="<?php echo site_url('admin/products/form/'.$product->id.'/1');?>" alt="<?php echo lang('copy');?>"><i class="icon-copy"></i></a>
                        <a class="btn btn-danger" href="<?php echo site_url('admin/products/delete/'.$product->id);?>" onclick="return areyousure();" alt="<?php echo lang('delete');?>"><i class="icon-times "></i></a>
                    </div>
                </td>
            </tr>
    <?php endforeach; ?>
        </tbody>
    </table>
</form>
