</div>

<footer>
    <div class="container">
        <nav>
            <?php page_loop(0, 'class="nav nav-center"');?>
        </nav>

        <div class="credits">
            <a href="http://gocartdv.com" target="_blank">
                Driven by GoCart
            </a>
            <br>
            <a href="http://gocartdv.com" target="_blank">Designed by Clear Sky Designs</a>
        </div>
    </div>
</footer>

<script>
setInterval(function(){
    resizeCategories();
}, 200);

function updateItemCount(items)
{
    $('#itemCount').text(items);
}

function resizeCategories()
{
    $('.categoryItem').each(function(){
        $(this).height($(this).width());
        var look = $(this).find('.look');
        var margin = 0-look.height()/2;
        look.css('margin-top', margin);
    });
}
</script>

</body>
</html>