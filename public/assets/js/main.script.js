
$('.link').on('click', function(e){
    e.preventDefault();

    if( $('.screen').hasClass('wait') ) {
        $('.screen').removeClass('wait');
        $('.screen').addClass('message');

        $('.show-footer').find('.btn').remove();

        $('.show-footer').append( $('<a href="/categorias" class="btn escolher">Vamos lá</a>') )
    }
});


$('.show-footer').on('click', '.escolher', function(e){
    e.preventDefault();

    if( $('.screen').hasClass('message') ) {
        $('.screen').removeClass('message');
        $('.screen').addClass('choice');

        $('.show-footer').find('.btn').remove();
    }
});

$('.category').on('click', function(e){
    e.preventDefault();

    if( $('.screen').hasClass('choice') ) {
        $('.screen').removeClass('choice');
        $('.screen').addClass('on');

        if( !$('.show-footer').find('.btn').length ) {
            $('.show-footer').append( $('<a href="/categorias" class="btn min volta-escolher"><<</a>') );
            $('.show-footer').append( $('<a href="/finalizar" class="btn finalizar">Play</a>') );
        }
    }
    else {
        $('.screen').removeClass('on');
        $('.screen').addClass('choice');

        $('.show-footer').find('.btn').remove();
    }
});


$('.show-footer').on('click', '.btn', function(e){
    e.preventDefault();
    
    if( $(this).hasClass('finalizar') ) {
        $('.screen').removeClass('on');
        $('.screen').addClass('message');

        $('.show-footer').find('.btn').remove();
    }
    else {
        $('.screen').removeClass('on');
        $('.screen').addClass('choice');
    }
});




$('.link')
.on('mouseenter', function() {
    this.iid = setInterval(function() {
       $('.screen').addClass('hover-glitch');
    }, 25);
})
// --- MOUSE OUCTH
.on('mouseleave', function(){
    this.iid && clearInterval(this.iid);
    $('.screen').removeClass('hover-glitch');
});



/*
    var categorias = new Swiper ('.categories', {
        direction: 'vertical',
        mousewheel: true,
        height: 80,
        // slidesPerView: 3,
        // autoHeight: true,
        centeredSlides: true,
    });

    var produtos = new Swiper ('.products', {
        slidesPerView: 3,
        grabCursor: true,
        setWrapperSize: true
    });

    window.addEventListener("resize", function(){
        categorias.virtual.update();
        produtos.virtual.update();
    });


    $('.categories-list').slick({
        arrows:false,
        centerMode: true,
        vertical: true,
        verticalSwiping: true,
    });


    $('.products-list').slick({
        arrows: false,
        centerMode: true,
        slidesToShow: 3,
        slidesToScroll: 3
    });
*/