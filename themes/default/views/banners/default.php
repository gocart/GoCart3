<div class="banners">

	<?php foreach($banners as $banner):?>
		<div class="banner"><?php
						
			$banner_image	= '<img src="'.base_url('uploads/'.$banner->image).'" />';
			if($banner->link)
			{
				$target=false;
				if($banner->new_window)
				{
					$target=' target="_blank"';
				}
				echo '<a href="'.$banner->link.'"'.$target.'>'.$banner_image.'</a>';
			}
			else
			{
				echo $banner_image;
			}
			?></div>
	<?php endforeach;?>

	<a class="controls" data-direction="back"><i class="icon-chevron-left"></i></a>
	<a class="controls" data-direction="forward"><i class="icon-chevron-right"></i></a>
	<div class="banner-timer"></div>
</div>