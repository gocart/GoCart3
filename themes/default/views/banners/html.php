
<div class="banners">

	<?php foreach($banners as $banner):?>
		<div class="banner">
			<?php echo str_replace('{{image}}', base_url('uploads/'.$banner->image), $banner->html);?>
		</div>
	<?php endforeach;?>

	<a class="controls" data-direction="back" href="#"><i class="icon-chevron-left"></i></a>
	<a class="controls" data-direction="forward" href="#"><i class="icon-chevron-right"></i></a>
	<div class="banner-timer"></div>
</div>