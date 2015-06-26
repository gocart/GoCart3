<div class="row">
	<div class="col-md-12">
		<button class="btn btn-primary" id="generateButton" onclick="generateProductsFeed(0, 100)">Generate Google Feed XML file</button>
		
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
<script type="text/javascript">
var count = 100;
var countI = 0;
var ProductsCount = '<?php echo $ProductsCount; ?>';
	
function generateProductsFeed(col-md-offset-, limit)
{
$('#generateButton').prop('disabled', true);
$('.container').spin();
$('#status').html('Exporting products to XML sitemap '+ count + ' out of  '+ ProductsCount);
setTimeout(function(){
	$.ajax({
		url : "<?php echo site_url('admin/sitemap/generateXMLGoogleFeed'); ?>",
		type: "POST",
		data : {'limit': limit, 'col-md-offset-': col-md-offset-},
		success: function(data, textStatus, jqXHR)
		{		
			if(count >= ProductsCount)
			{
				$('.container').spin(false);
				setProgress( 100 );	
			}			
			else
			{	
				setProgress( Math.ceil(((count/ProductsCount)).toFixed(2) * 100) );
				generateProductsFeed(count,  count + 100);
				count = count + 100;		
								
			}									
		},
		error: function (jqXHR, textStatus, errorThrown)
		{
		    $("#status").removeClass();
			$('#status').addClass('alert alert-error');
			$('#status').html('Google feed XML encountered an error');
		}
	});
},2000);

function setProgress(progress)
	{           
		var progressBarWidth =progress*$(".progressbarContainer").width()/ 100;  
		$(".progressbar").width(progressBarWidth).html(progress + "% ");
	}
}

</script>