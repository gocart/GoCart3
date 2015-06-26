<?php if(isset($banners[0])): $b = $banners[0]; ?>

<!--[if (lte IE 8)]><style type="text/css">.btn-logan:after {display:none; text-decoration:none;} .btn-logan:before {padding: 5px 10px 5px 10px;}</style><![endif]-->

<div class="logan-box" style="background-image:url(<?php echo base_url('uploads/'.$b->image);?>)">

	<div class="logan-title">
		<?php echo $b->name;?>
		<?php
			if($b->link)
			{
				$target=false;
				if($b->new_window)
				{
					$target=' target="_blank"';
				}
				echo '<a class="btn-logan" href="'.$b->link.'"'.$target.' data-title="Read More" style="text-decoration:none;"></a>';
			}
		?>
	</div>
	
</div>

<?php endif;?>