<?php

$m	= Array(
lang('january')
,lang('february')
,lang('march')
,lang('april')
,lang('may')
,lang('june')
,lang('july')
,lang('august')
,lang('september')
,lang('october')
,lang('november')
,lang('december')
);
?>

<table class="table table-striped">
	<thead>
		<tr>
			<th><?php echo $year;?></th>
			<th><?php echo lang('coupon_discounts');?></th>
			<th><?php echo lang('gift_card_discounts');?></th>
			<th><?php echo lang('products');?></th>
			<th><?php echo lang('shipping');?></th>
			<th><?php echo lang('tax');?></th>
			<th><?php echo lang('grand_total');?></th>
		</tr>
	</thead>
	<tbody>
		<?php

		$fields = ['couponDiscounts', 'giftCardDiscounts', 'products', 'shipping', 'tax'];
		for($i=0; $i<12; $i++):

		?>
		<tr>
			<th><?php echo $m[$i];?></th>
			<?php
				$total = 0;
				foreach($fields as $field)
				{
					echo '<td>';
					if(isset($orders[$field][$i]))
					{
						if($field == 'couponDiscounts' || $field == 'giftCardDiscounts')
						{
							echo format_currency( (abs($orders[$field][$i])*-1) );
							$total = $total - abs($orders[$field][$i]);
						}
						else
						{
							echo format_currency($orders[$field][$i]);
							$total = $total + $orders[$field][$i];
						}
					}
					else
					{
						echo format_currency(0);
					}
					echo '</td>';
				}
				echo '<td>'.format_currency($total).'</td>';
			?>
		</tr>
		<?php endfor;?>
	</tbody>
</table>
