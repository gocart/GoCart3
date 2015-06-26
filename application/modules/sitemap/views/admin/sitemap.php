<div class="row">
    <div class="col-md-12">
        <button class="btn btn-primary" id="generateButton" onclick="generateSitemap()">Generate Sitemap</button>

        <div class="progressbarContainer">
            <div class="progressbar"></div>
        </div>
        <div id="status"></div>
    </div>
</div>
<style>
.progressbar {
    color: #fff;
    text-align: right;
    height: 25px;
    width: 0;
    background-color: #0ba1b5; 
    border-radius: 3px;
    margin-top: 10px;
}
#status { margin-top: 10px;}
</style>
<script>
var count = 0;
var ProductsCount = '<?php echo $ProductsCount; ?>';

function wrapUp()
{
    $('body').spin(false);
    $('#generateButton').prop('disabled', false);
}

function fail()
{
    $('#status').append('<p>Sitemap generation failed.</p>');
    wrapUp();
}

function generateSitemap()
{
    $('#generateButton').prop('disabled', true);
    $('body').spin();
    $('#status').addClass('alert alert-info').html('<p>Creating a new sitemap.</p>'); //reset the html to "Creating new sitemap"

    //run ajax
    $.get( "<?php echo site_url('admin/sitemap/new-sitemap'); ?>", function( data ) {
        $('#status').append('<p>Processing Products.</p>');
        generateProducts();
    }).fail(function(){
        fail();
    });
}

function generateProducts()
{
    $.post('<?php echo site_url('admin/sitemap/generate-products'); ?>', {'limit':count, 'col-md-offset-':count+100}, function(data){
        if(count >= ProductsCount)
        {
            $('#status').append('<p>Products completed.</p>');
            generateCategories();
        }
        else
        {
            count = count+100;
            var currentCount = Math.min(count, ProductsCount);
            $('#status').append('<p>'+currentCount+' of '+ProductsCount+' products processed</p>');
            
            generateProducts();
        }
    }).fail(function(){
        fail();
    });
}

function generateCategories()
{
    $('#status').append('<p>Processing categories.</p>');
    $.get("<?php echo site_url('admin/sitemap/generate-categories'); ?>", function(data){
        $('#XML').append(data);
        generatePages();
    }).fail(function(){
        fail();
    });
}

function generatePages()
{
    $('#status').append('<p>Processing pages.</p>');
    $.get("<?php echo site_url('admin/sitemap/generate-pages'); ?>", function(data){
        $('#XML').append(data);
        completeSitemap();
    }).fail(function(){
        fail();
    });
}

function completeSitemap()
{
    $('#status').append('<p>Wrapping things up.</p>');
    $.get("<?php echo site_url('admin/sitemap/complete-sitemap'); ?>", function(data){
        $('#XML').append(data);
        $('#status').append('XML sitemap generated successfully!');
        wrapUp();
    }).fail(function(){
        fail();
    });
}

</script>