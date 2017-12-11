/* extension micogian_lastpictures */
(function($) {
    $(function() {
        var jcarousel = $('.jcarousel');

        jcarousel
            .on('jcarousel:reload jcarousel:create', function () {
                var width = jcarousel.innerWidth();

                if (width >= 600) {
                    width = width / 7;
                } else if (width >= 350) {
                    width = width / 4;
                }

                jcarousel.jcarousel('items').css('width', width + 'px');
            })
            .jcarousel({
                wrap: 'circular'
            });

        $('.jcarousel-control-prev')
            .jcarouselControl({
                target: '-=6'
            });

        $('.jcarousel-control-next')
            .jcarouselControl({
                target: '+=6'
            });

        $('.jcarousel-pagination')
            .on('jcarouselpagination:active', 'a', function() {
                $(this).addClass('active');
            })
            .on('jcarouselpagination:inactive', 'a', function() {
                $(this).removeClass('active');
            })
            .on('click', function(e) {
                e.preventDefault();
            })
            .jcarouselPagination({
                perPage: 6,
                item: function(page) {
                    return '<a href="#' + page + '">' + page + '</a>';
                }
            });
		$('.jcarousel').jcarousel({
			wrap: 'both'
			});
    });
})(jQuery);