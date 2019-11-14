<?php
// ['ROTA', 'CONTROLADOR', 'ACAO', 'PERMISSAO']
$routes = [
    ['/', 'site\PageController', 'view_index', 'free'],// VIEW

    ['/cadastrar',     'site\PageController', 'view_cadastrar',     'free'],// VIEW
    ['/cadastro/novo', 'site\PageController', 'cadastro_novo', 'free'],
    
    ['/facebook', 'site\PageController', 'facebook', 'free'],// VIEW
    
    ['/bem-vindo', 'site\PageController', 'view_bem_vindo', 'user'],// VIEW
    
    ['/lista-de-desejos', 'site\PageController', 'view_produtos',    'user'],// VIEW
    ['/produtos/lista',   'site\PageController', 'get_listProduto',  'user'],
    ['/manter-lista',     'site\PageController', 'set_sessWishlist', 'user'],
    ['/wishlist/save',    'site\PageController', 'save_wishlist',    'user'],

    ['/obrigado', 'site\PageController', 'view_finish', 'user'],// VIEW


    ['/teste-email', 'site\PageController', 'sendEmail', 'free'],// VIEW
    ['/meu-ip', 'site\PageController', 'meuIP', 'free']// VIEW
];

return $routes;
