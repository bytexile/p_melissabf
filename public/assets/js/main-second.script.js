jQuery(function(){

    var email_form = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    var max_select = 10;


    // ----- REMOVE a classe do efeito de transicao
    setIntervalX(function(){
        $('.tv').removeClass('loading');
    }, 600, 1);
    



    // ----- ALERTA - se existir a div na pagina
    function getNotification(type = 'alert', message){
        if( message ){
            // console.log('remove o alerta antigo');
            $('.notification').remove();

            // console.log('montar um alert');
            var notification = template.notification.replace(/\{TYPE\}/g, type)
                                                    .replace(/\{MESSAGE\}/g, message);

            $('.show-content').append(notification);
            $('.notification').removeClass('hide');

            setIntervalX(getNotification, 3000, 1);
        }
        else {
            if($('.notification').length > 0){
                // console.log('tem um alert');
                if( $('.notification').hasClass('hide') ){
                    // console.log('mostrar');
                    setIntervalX(function(){
                        $('.notification').removeClass('hide');
                        
                        setIntervalX(getNotification, 5000, 1);
                    }, 500, 1);
                }
                else {
                    // console.log('esconder e remover');
                    $('.notification').addClass('hide');
                    
                    /*setIntervalX(function(){
                        $('.notification').remove();
                    }, 2000, 1);*/
                }
            }
        }
    };


    $('.tv').on('click', '.notification', function() {
        getNotification();
    });


    getNotification();
    


    // ----- CADASTRO 
    function checkInputs(arr){
        // console.log(typeof arr);
        var retorno = {};
        
        for (var input of arr) {
            var name  = input.getAttribute('name');
            var value = input.value;
            
            if( value == '' || value == undefined ){
                getNotification('wait', 'campo ['+ name +'] esta vazio!');

                $(input).focus();
                retorno = false;

                return false;
            }
            else if( name == 'email' && !email_form.test(String(value).toLowerCase()) ){
                getNotification('error', 'campo ['+ name +'] é inválido!');

                $(input).focus();
                retorno = false;

                return false;
            }
            else {
                retorno[name] = value;
                // retorno[name] = '';
            }
        }

        return retorno;
    };

    
    // ----- SUBMIT formulario
    $('form').on('submit', function(e){
        // console.log('>> formulario enviando dados');
        e.preventDefault();

        var campos = $(this).find('input');
        var enviar = checkInputs(campos);
        
        if( enviar ){
            $.ajax({
                type: 'POST',
                dataType: 'json',
                data: enviar,
                url: '/cadastro/novo',

                success: function(result){
                    if( result.status ){
                        getNotification('alert', result.message);
                        
                        setIntervalX(function(){
                            window.location.replace("/bem-vindo");
                        }, 1000, 1)
                    }
                    else {
                        getNotification('error', result.message);
                        $('input[name="'+ result.input +'"]').focus();
                    }
                }
            });
        }
    });
    

    // ----- ancora para força o formulario a um evento SUBMIT
    $('.submit').on('click', function(e){
        // console.warn('>> botao forçando o formulario');

        e.preventDefault();

        $('form').submit();
    });




    // ---- pagina CATEGORIA/PRODUTOS
    if( $('.categories').length && $('.products').length ){
        sliderInit();

        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: '/produtos/lista',

            success: function(produtos) {
                var cat_prim = $('.categories-list .category').attr('rel');

                // -- corrigindo, quando tem tem itens forçar o tipo array
                wishlist = (!wishlist) ? [] : wishlist;

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


        // -- atualiza o contador
        $('.count').text( (wishlist.length).pad(2) );

        $('.total').html(max_select);
        
        if( wishlist.length > 0 ){
            $('.counter').removeClass('off');
        }


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


        // -- carregar lista de produtos CLICANDO na categoria
        $('.tv').on('click', '.category', function(e){
            e.preventDefault();

            var cat_id   = $(this).attr('rel');
            var cat_name = $(this).attr('href').split("/")[2];
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

                    // -- alerta
                    if( count == 10 ){
                        getNotification('alert', '10 itens selecionados');
                    }
                }
                else {
                    // console.error('ja foram selecionandos todos!');
                    // -- alerta
                    if( count == 10 ){
                        getNotification('wait', 'Você já tem 10 itens. Mais ianda pde trocar');
                    }

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

            // -- animação do contador
            if( wishlist.length > 0 ){
                $('.counter').removeClass('off');
            }
            else {
                $('.counter').addClass('off');
            }
        });
    }


    // ---- pagina CATEGORIA/PRODUTOS
    if( $('.wish-list').length ){
        sliderInit();
    }
    
});




// ---- pagina CATEGORIA/PRODUTOS
function sliderInit(){
    $('.products-run').slick({
        accessibility: false,
        autoplay: false,
        arrows: false,
        centerMode: true,
        infinite: false,
        centerPadding: '0'
    })
};

/*
- remove os itens da galeria
- loop para montar HTML dos produtos listado da categoria
- compara com a variavel "wishlist" para marcar os itens ja selecionados
*/
function montaGaleria(lista) {
    // -- remove os itens ja listado
    $('.products-run').slick('slickRemove', null, null, true);

    lista.forEach(produto => {
        //var prodID = 'c'+produto.categoria+'-p'+produto.id_produto;

        //if( wishlist.includes(prodID) ){
        if( wishlist.includes(produto.id_produto) ){
                var checkeded = 'checked';
        }
        else {
            var checkeded = '';
        }

        var prod_html = template.produto.replace(/\{NOME\}/g, produto.nome)
                                        .replace(/\{IMAGEM\}/g, produto.imagem)
                                        .replace(/\{ID\}/g, produto.id_produto)
                                        .replace(/\{CHECKED\}/g, checkeded); 

        // $('.products-run').append(prod_html);
        $('.products-run').slick('slickAdd', prod_html);
    });
};


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
};


//- funcao para executar uma ação, N vezes, no tempo determinado
function setIntervalX(callback, delay, repetitions){
    var thisX = 0;
    var intervalID = window.setInterval(function () {
        callback();

        if (++thisX === repetitions) {
            window.clearInterval(intervalID);
        }
    }, delay);
};


//- COOKIE
//- definir
function setCookie(name,value,days) {
    var expires = "";
    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days*24*60*60*1000));
        expires = "; expires=" + date.toUTCString();
    }
    document.cookie = name + "=" + (value || "")  + expires + "; path=/";
}
//- pegar
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
//- apagar
function eraseCookie(name) {   
    document.cookie = name+'=; Max-Age=-99999999;';  
}


var template = {
    'notification': '<div class="notification hide {TYPE}"><div class="notification-inner"><div class="msg">{MESSAGE}</div></div></div>',

    'produto': '<label class="product {CHECKED}"><input type="checkbox" name="wishlist" value="{ID}" {CHECKED}><img class="product-thumb" src="/gallery/{IMAGEM}" alt="{NOME}"><div class="product-name">{NOME}</div></label>'
};