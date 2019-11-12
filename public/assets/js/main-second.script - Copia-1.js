jQuery(function(){

    var max_select = 10;
    // var wishlist   = [];


    // ----- REMOVE a classe do efeito de transicao
    setTimeout(function(){
        $('.tv').removeClass('loading');
    }, 600);


    // ----- ancora para força o formulario a um evento SUBMIT
    $('.submit').on('click', function(e){
        e.preventDefault();

        // $('form').submit();
    });


    // ---- SO EXECUTAR NA PAGINA, CATEGORIA/PRODUTOS
    if( $('.categories').length ){
        sliderInit();


        // -- atualiza o contador
        $('.count').text( (wishlist.length).pad(2) );
        
        if( wishlist.length > 0 ){
            $('.counter').removeClass('off');
        }


        // -- carregar lista de produtos CLICANDO na categoria
        $('.tv').on('click', '.category', function(e){
            e.preventDefault();

            var cat_id   = $(this).attr('rel');
            var cat_name = $(this).attr('href').split("/")[2];
            
            // console.log('nome: ['+ cat_name +'] - id: ['+ cat_id +']');
        });


        // ///////////////////////////
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: '/produtos/lista',

            success: function(produtos) {
                var cat_prim = $('.categories-list .category').attr('rel');

                // -- corrigindo, quando tem tem itens forçar o tipo array
                wishlist = (!wishlist) ? [] : wishlist;

                $('.total').html(max_select);

                /*
                - remove os itens da galeria
                - loop para montar HTML dos produtos listado da categoria
                - compara com a variavel "wishlist" para marcar os itens ja selecionados
                */
                function montaGaleria(lista) {
                    // -- remove os itens ja listado
                    $('.products-run').slick('slickRemove', null, null, true);

                    lista.forEach(produto => {
                        var prodID = 'c'+produto.categoria+'-p'+produto.id_produto;

                        if( wishlist.includes(prodID) ){
                            var checkeded = 'checked';
                        }
                        else {
                            var checkeded = '';
                        }

                        var prod_html = template.produto.replace(/\{NOME\}/g, produto.nome)
                                                        .replace(/\{IMAGEM\}/g, produto.imagem)
                                                        .replace(/\{ID\}/g, prodID)
                                                        .replace(/\{CHECKED\}/g, checkeded); 

                        // $('.products-run').append(prod_html);
                        $('.products-run').slick('slickAdd', prod_html);
                    });
                };

                /*
                - quando clica na categoria
                - verifica se a div.screen tem a classe .catg (lista de produtos oculta)
                - remove ou coloca a classe .select na categoria selecionada
                */
                $('.tv').on('click', '.category', function(e) {
                    e.preventDefault();

                    var $this = $(this);

                    if( $('.screen.choice').hasClass('catg') ){
                        // console.log('OCULTA A LISTA DE CATEGORIAS E MOSTRA OS PROTUDOS');

                        $('.screen.choice').removeClass('catg');
                        $this.addClass('select');
                        
                        // --- ID da categoria selecionada
                        var catg_id = $this.attr('rel');

                        // --- filtra a Array dos produtos e retorna somente os produtos da categoria/ID selecionado
                        var filtro_prod = produtos.filter(function(items){
                            return items.categoria == catg_id;         
                        });
                        
                        // --- monta a galeria e atualiza o HTML da pagina
                        montaGaleria(filtro_prod);

                        // --- muda o texto da div.channel
                        $('.channel').html('wish<br>list');

                        // --- coloca os botoes .voltar e .finalizar
                        $('.show-footer').html('<a class="btn min showCategories" href="/lista-de-desejos"> << </a><a class="btn" href="/wishlist/save">Enviar lista</a>');
                    }
                    else {
                        // console.log('REMOVE OS PRODUTOS E MOSTRA A LISTA DE CATEGORIAS');

                        $('.screen.choice').addClass('catg');
                        $('.category').removeClass('select');

                        // --- muda o texto da div.channel
                        $('.channel').html('Pause ||');

                        // --- remove os botoes
                        $('.show-footer').html('');
                    }
                });
            }
        });

        
        // -- BTN remove galeria de produtos
        $('.tv').on('click', '.showCategories', function(e){
            e.preventDefault();

            // --- muda o texto da div.channel
            $('.channel').html('Pause ||');

            // -- remove a classe .select do item marcado na lista de categorias
            $('.category').removeClass('select');
            
            // -- coloca a classe .carg da div.screen
            $('.screen.choice').addClass('catg');

            // --- remove os botoes
            $('.show-footer').html('');
        });

        
        // -- clique no item da galeria de produtos
        $('.products-run').on('change', 'input', function(e){
            var count = wishlist.length;

            // -- ADICIONA. atualiza o array e add uma classe ao container do item
            if( $(this).is(':checked') ){
                //  -- quando clica no produtos; verifica se ja foram selecionadods os 10 itens
                if( count <= (max_select -1) ){
                    // console.warn('pode continuar selecionando!');
                    count++;

                    wishlist.push( $(this).val() );
                    $(this).parent('.product').addClass('checked');

                    $('.count').addClass('add').delay(700).queue(function(next){
                        $(this).removeClass("add");
                        next();
                    });
                }
                else {
                    // console.error('ja foram selecionandos todos!');
                    $(this).prop( "checked", false );
                }
            }
            // -- REMOVE
            else {
                count--;

                wishlist.remove( $(this).val() );
                $(this).parent('.product').removeClass('checked');

                $('.count').addClass('remove').delay(700).queue(function(next){
                    $(this).removeClass("remove");
                    next();
                });
            };
            
            // -- atualiza o contador
            $('.count').text( count.pad(2) );
            
            $.ajax({
                type: 'POST',
                dataType: 'json',
                data: { wish_list: wishlist },
                url: '/manter-lista'
            });

            if( wishlist.length > 0 ){
                $('.counter').removeClass('off');
            }
            else {
                $('.counter').addClass('off');
            }
        });
    };

});



Array.prototype.remove = function() {
    var what, a = arguments, L = a.length, ax;

    while (L && this.length) {
        what = a[--L];
        while ((ax = this.indexOf(what)) !== -1) {
            this.splice(ax, 1);
        }
    }
    return this;
};

Number.prototype.pad = function(size) {
    var s = String(this);
    while (s.length < (size || 2)) {s = "0" + s;}
    return s;
}


function setCookie(name,value,days) {
    var expires = "";
    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days*24*60*60*1000));
        expires = "; expires=" + date.toUTCString();
    }
    document.cookie = name + "=" + (value || "")  + expires + "; path=/";
}

function getCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for(var i=0;i < ca.length;i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1,c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
    }
    return null;
}

function eraseCookie(name) {   
    document.cookie = name+'=; Max-Age=-99999999;';  
}


function sliderInit(){
    $('.products-run').slick({
        accessibility: false,
        autoplay: false,
        arrows: false,
        centerMode: true,
        infinite: false,
        centerPadding: '0',
        /*
        responsive: [
            {
                breakpoint: 768,
                settings: {
                    centerMode: true,
                    centerPadding: '40px',
                    slidesToShow: 1
                }
            },
            {
                breakpoint: 480,
                settings: {
                    centerMode: true,
                    centerPadding: '40px',
                    slidesToShow: 1
                }
            }
        ]*/
    })
    /*.slick('setPosition')
    .slick('resize')*/;
};



var template = {
    'produto': '<label class="product {CHECKED}">\
                    <input type="checkbox" name="wishlist" value="{ID}" {CHECKED}>\
                    <img width="200" class="product-thumb" src="/gallery/{IMAGEM}" alt="{NOME}">\
                    <div class="product-name">{NOME}</div>\
                </label>'
};