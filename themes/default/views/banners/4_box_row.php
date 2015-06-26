<div class="col-nest">
	<?php foreach($banners as $banner):?>
	<div class="col" data-cols="1/4">
		<?php
		
		$box_image	= '<img src="'.base_url('uploads/'.$banner->image).'" />';
		if($banner->link != '')
		{
			$target	= false;
			if($banner->new_window)
			{
				$target = 'target="_blank"';
			}
			echo '<a href="'.$banner->link.'" '.$target.' >'.$box_image.'</a>';
		}
		else
		{
			echo $box_image;
		}
		?>
	</div>
	<?php endforeach;?>
</div>
<br class="clear">