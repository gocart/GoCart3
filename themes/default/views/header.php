<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title><?php echo (!empty($seo_title)) ? $seo_title .' - ' : ''; echo config_item('company_name'); ?></title>

<link rel="shortcut icon" href="<?php echo theme_img('favicon.png');?>" type="image/png" />
<?php if(isset($meta)):?>
<?php echo (strpos($meta, '<meta') !== false) ? $meta : '<meta name="description" content="'.$meta.'" />';?>
<?php else:?>
    <meta name="keywords" content="<?php echo config_item('default_meta_keywords');?>" />
    <meta name="description" content="<?php echo config_item('default_meta_description');?>" />
<?php endif;?>

<link href='//fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,300,400,700,600' rel='stylesheet' type='text/css'>
<?php
$_css = new CSSCrunch();
$_css->addFile('gumbo/normalize');
$_css->addFile('gumbo/base');
$_css->addFile('gumbo/text');
$_css->addFile('gumbo/banners');
$_css->addFile('gumbo/buttons');
$_css->addFile('gumbo/alerts');
$_css->addFile('gumbo/forms');
$_css->addFile('gumbo/grid');
$_css->addFile('gumbo/tabs');
$_css->addFile('gumbo/tables');
$_css->addFile('gumbo/pagination');
$_css->addFile('gumbo/nav');
$_css->addFile('gumbo/colors');
$_css->addFile('gumbo/tray');
$_css->addFile('styles');

$_js = new JSCrunch();
$_js->addFile('jquery');
$_js->addFile('jquery.spin');
$_js->addFile('gumbo');
$_js->addFile('elementQuery.min');

if(true) //Dev Mode
{
    //in development mode keep all the css files separate
    $_css->crunch(true);
    $_js->crunch(true);
}
else
{
    //combine all css files in live mode
    $_css->crunch();
    $_js->crunch();
}


//with this I can put header data in the header instead of in the body.
if(isset($additional_header_info))
{
    echo $additional_header_info;
}
?>
<link href='<?php echo theme_css('gumboIcons.css');?>' rel='stylesheet' type='text/css'>
<link href='<?php echo theme_css('gumbo/elem-grid.css');?>' rel='stylesheet' type='text/css'>
</head>

<body>

<header>
    <div class="container">
        <div class="col-nest">
            <div class="col" data-cols="1/2">
                <a class="logo" href="<?php echo base_url();?>"><img src="<?php echo theme_img('logo.svg');?>" /></a>
            </div>
            <div class="col" data-cols="1/2">
                <nav> 
                    <ul class="nav nav-right mobileNav" style="font-size:14px; font-weight:bold; text-transform:uppercase;">

                        <?php if(CI::Login()->isLoggedIn(false, false)):?>
                            <li>
                                <a><?php echo lang('account');?> <i class="icon-chevron-down"></i></a>
                                <ul>
                                    <li><a href="<?php echo  site_url('my-account');?>"><?php echo lang('my_account')?></a></li>
                                    <li><a href="<?php echo site_url('logout');?>"><?php echo lang('logout');?></a></li>
                                </ul>
                            </li>
                        <?php else: ?>
                            <li><a href="<?php echo site_url('login');?>"><?php echo lang('login');?></a></li>
                        <?php endif; ?>
                        <li>
                            <a href="<?php echo site_url('checkout');?>"><i class="icon-cart"></i>(<span id="itemCount"><?php echo GC::totalItems();?></span>)</a>
                        </li>
                    </ul>
                </nav>

                <?php echo form_open('search', 'class="navbar-search pull-right"');?>
                    <div class="btn-group">
                        <div><input type="text" name="term" class="search-query" placeholder="<?php echo lang('search');?>"/></div>
                        <div><input type="submit" value="<?php echo lang('search');?>"/></div>
                    </div>
                </form>
            </div>
        </div>

        <div class="mobileNavContainer">
            <span class="menuBtn" onclick="$('.navbarMobileContainer').show();"> <span></span> <span></span> <span></span> </span>
            <div class="navbarMobileContainer">
                <div class="navBoxClose" onclick="$('.navbarMobileContainer').hide()">
                    <span></span><span></span>
                </div>
                <ul class="navbarMobile navbar mobile"></ul>
            </div>
        </div>

        <nav class="navPrimary">
            <ul class="nav mobileNav">
                <li><a href="/"><i class="icon-home"></i> <span class="homeLinkText">Home</span></a></li>
                <?php
                    category_loop(0, false, false);
                    page_loop(0, false, false);
                ?>
            </ul>
        </nav>

    </div>
</header>

<div class="main container">
    <?php if (CI::session()->flashdata('message')):?>
        <div class="alert blue">
            <?php echo CI::session()->flashdata('message');?>
        </div>
    <?php endif;?>

    <?php echo CI::breadcrumbs()->generate(); ?>
