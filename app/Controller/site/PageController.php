<?php

namespace App\Controller\site;

use Core\BaseController;
use Core\Container; 
use Core\DataBase;
use Core\Helper;
use Core\Redirect;
use Core\Session;

class PageController extends BaseController
{
    public function __construct()
    {
        session_start();
    }



    // ---------- VIEW - pagina inicial ---------------------
    public function view_index()
    {
        @$this->view->channel = 'Participe';

        $this->view->buttons  = false;

        $this->view->tvClass  = 'wait index';
        
        $this->view->links = [
            // ['url'=>'/facebook', 'text'=>'> Entrar com Facebook'],
            ['url'=>'/cadastrar', 'text'=>'> Cadastre-se']
        ];

        if( Session::get('bf_player') ){
            $mySess = Session::get('bf_player');

            $linkTxt = ($mySess['my_state']) ? '> Continuar selecionadno' : '> Minha wishlist';

            unset($this->view->links);
            
            $this->view->links = [
                ['url'=>'/lista-de-desejos', 'text'=>$linkTxt]
            ];
        }

        // -----

        $this->renderView('site\index', 'site\layout-v1');
    }



    // ---------- VIEW - formulario de cadastro ---------------------
    public function view_cadastrar()
    {
        if( Session::get('bf_player') ){
            return Redirect::route("/bem-vindo");
        }

        // -----

        @$this->view->channel = 'Novo<br> canal';
        $this->view->tvClass  = 'wait cadastrar';
        $this->view->buttons  = true;

        $this->view->buttons = [
            ['classes'=>'gold min', 'url'=>'/', 'text'=>'<<'],
            ['classes'=>'gold submit', 'url'=>'/cadastrar/novo', 'text'=>'Cadastrar']
        ];

        // -----
        
        $this->renderView('site\cadastrar', 'site\layout-v1');
    }

    // ----- SAVAR CADASTRO NOVO 
    public function cadastro_novo($params = null)
    {
        if( $params ){
            $registrar = false;
            $retorno   = new \stdClass;

            // --- verifica se algum campo esta vazio
            foreach( $params->post as $campo => $valor ){
                // CAMPO VAZIO
                if( empty($valor) && ($valor == null) ){
                    echo json_encode(['status'=>false, 'message'=>"Campo [$campo] deve ser preenchido", 'input'=>$campo], true);
                    return false;
                }
                // CPF VALIDO
                else if( $campo == 'cpf' && !Helper::validaCPF($valor) ){
                    echo json_encode(['status'=>false, 'message'=>"CPF inválido", 'input'=>$campo], true);
                    return false;
                }
                // EMAIL VALIDO
                else if( $campo == 'email' && !filter_var($valor, FILTER_VALIDATE_EMAIL)) {
                    echo json_encode(['status'=>false, 'message'=>"E-mail inválido", 'input'=>$campo], true);
                    return false;
                }
                else {
                    $registrar = true;
                }
            }

            // -- retorna um JSON com a resposta a requisicao
            if( $registrar ){
                $conn = DataBase::getDB();// - conexao com o banco
                $this->user = Container::getModelEx("User", $conn);// - acesso a tabela indicada

                $params->post->status     = 1;
                $params->post->last_login = date('Y-m-d H:i:s');// -- data do registro
                $params->post->location   = json_encode(Helper::getLocation());// -- guarda dados de localizacao
                
                $retorno = $params;
                $result  = $this->user->Save((array)$retorno->post);

                if( $result['status'] ){
                    Session::set('bf_player', [
                        'my_email' => $params->post->email,
                        'the_name' => $params->post->nome,
                        'wishlisti_me' => '',// tranformar array em JSON
                        'my_state' => 1
                    ]);

                    header("Content-Type: application/json; charset: utf-8");
                    echo json_encode(['status'=>true, 'message'=>'Cadastro efetuado com sucesso!'], true);
                }
                // -- SEM RETORNO
                else {
                    header("Content-Type: application/json; charset: utf-8");
                    echo json_encode(['status'=>false, 'message'=>$result['message']], true);
                }
            }
            // -- NAO recebeu parametros
            else {
                header("Content-Type: application/json; charset: utf-8");
                echo json_encode(['status'=>false, 'message'=>'Sem acesso a essa URL'], true);
            }
        }
        // -- SEM ACESSO
        else {
            header("Content-Type: application/json; charset: utf-8");
            echo json_encode(['status'=>false, 'message'=>'Ops ocorreu algum erro!'], true);
        }
    }

    

    // ---------- VIEW - BOAS VINDAS ---------------------
    public function view_bem_vindo()
    {
        if( !Session::get('bf_player') ){
            return Redirect::route("/cadastrar");
        }

        // -----

        @$this->view->channel = false;
        $this->view->tvClass  = 'wait wait-message topo';
        $this->view->title    = 'Vamos nessa!';
        $this->view->text     = '<p>Quer receber vantagens na <i>Black Friday Melissa!</i><p>Você só precisa selecionar as <i>10 Melissas</i> que mais quer e adicionar na sua wish list.<br>No dia <i>26/11</i> você recebe em primeira mão quais Melissas estarão na BF</p>';

        $this->view->links = false;
        
        $this->view->buttons = [
            ['classes'=>'', 'url'=>'/lista-de-desejos', 'text'=>'Vamos lá']
        ];

        // -----

        $this->renderView('site\mensagem', 'site\layout-v1');
    }
    


    // ---------- VIEW - LISTA DE CATEGORIAS ---------------------
    public function view_produtos()
    {
        // -- redireciona caso nao exista a sessao do usuario
        if( !Session::get('bf_player') ){
            return Redirect::route("/cadastrar");
        }
        else {
            $mySessio = Session::get('bf_player');
            @$this->view->wish_list = ($mySessio['wishlisti_me']) ? $mySessio['wishlisti_me'] : array();

            // -----

            $mySess = (object)Session::get('bf_player');

            if( $mySess->my_state != 0 ) {
                @$this->view->channel = 'Pause ||';
                $this->view->tvClass  = 'choice catg';
                
                $conn = DataBase::getDB();// - conexao com o banco
                $this->categorias = Container::getModelEx("Categorias", $conn);// - acesso a tabela indicada

                $categorias = $this->categorias->All('categorias');
                $this->view->categorias = $categorias;
                
                $this->view->buttons = false;
            }
            else {
                @$this->view->channel = 'Minha<br>wishlist';
                $this->view->tvClass  = 'choice catg';

                $conn = DataBase::getDB();// - conexao com o banco
                $this->produtos = Container::getModelEx("Produtos", $conn);// - acesso a tabela indicada
                

                $coluna['id_produto'] = json_decode($mySessio['wishlisti_me']);

                $this->view->wishlist = $this->produtos->FindGroup($coluna);
            }

            // -----

            $this->renderView('site\lista_de_desejos', 'site\layout-v1');
                
            // -- joga o array com a lista de produtos no HTML
            $my_session = json_decode(Session::get('bf_player')['wishlisti_me'], true);
        }
    }

    // ----- RETORNA lista de produtos
    public function get_listProduto()
    {
        if( !Session::get('bf_player') ){
            return Redirect::route("/cadastrar");
        }

        // -----

        $conn = DataBase::getDB();// - conexao com o banco
        $this->produtos = Container::getModelEx("Produtos", $conn);// - acesso a tabela indicada
        
        $produtos = $this->produtos->All('produtos');

        // -----

        header("Content-Type: application/json; charset: utf-8");
        echo json_encode($produtos, true);
    }

    // ----- atualiza a sessao com os itens selecionados
    public function set_sessWishlist($params = null)
    {
        if( Session::get('bf_player') ){
            $mySessio = Session::get('bf_player');

            Session::get('wishlisti_me');

            if( gettype($params) != 'object' ){
                @$params->post->wish_list = [];
            }
            
            $_SESSION['bf_player']['wishlisti_me'] = json_encode($params->post->wish_list, true);

            header("Content-Type: application/json; charset: utf-8");
            echo json_encode($_SESSION['bf_player']['wishlisti_me'], true);
        }
    }

    // ----- salvar wisthlist e encerra sessao
    public function save_wishlist()
    {
        if( !Session::get('bf_player') ){
            return Redirect::route("/cadastrar");
        }

        // -----

        $sess  = (object)Session::get('bf_player');// - pega dados da sessao
        $lista = json_decode($sess->wishlisti_me, true);// - wishlist

        // -----

        if (count($lista) >= 1) {
            $conn = DataBase::getDB();// - conexao com o banco

            $how_find = ['email' => $sess->my_email];
            $dados_up = [
                'wishlist' => json_encode($lista, true),
                'status' => 0
            ];

            $_SESSION['bf_player']['my_state'] = 0;

            $this->user = Container::getModelEx("User", $conn);// - acesso a tabela indicada
            $resulta = $this->user->Update($dados_up, $how_find);

            if ($resulta) {
                return Redirect::route("/obrigado");
            }
        }
        else {
            return Redirect::route("/lista-de-desejos");
        }
    }



    // ---------- VIEW - AGRADECIMENTO ---------------------
    public function view_finish()
    {
        if( !Session::get('bf_player') ){
            return Redirect::route("/cadastrar");
        }

        // -----
        
        // @$this->view->tvClass = 'message bottom'; 
        @$this->view->tvClass = 'wait wait-message topo';
        $this->view->channel  = false;
        $this->view->finish   = true;
        $this->view->title    = 'Arrasou!';
        $this->view->text     = '<p>Obrigada por participar!<br>Agora só esperar as promos incríveis que estamos preparando pra você.</p>';
        $this->view->buttons = [
            ['classes'=>'gold btn-obg', 'url'=>'/lista-de-desejos', 'text'=>'Voltar a minha wishlist']
        ];

        $this->renderView('site\mensagem', 'site\layout-v1');

        // DELETA A SESSAO
        // Session::dell();
    }
    








    // ---------- VIEW - AGRADECIMENTO ---------------------
    public function sendEmail()
    {
        echo '<form action="/teste-email"><input type="email" name="email" placeholder="E-mail para enviar"><input type="submit" value="enviar"></form>';
    }
}