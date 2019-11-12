<?php

namespace Core;

class Router
{
    // ////////// PARAMETROS
    private $routes;


    // ////////// METODOS
    public function __construct(array $routes)
    {
        $this->setRoutes($routes);
        $this->Run();
    }
    

    // - responsavel por controlar as rotas e acoes
    private function Run(){
        // echo 'classe Router() <br>';

        $url    = $this->getURL();
        $urlArr = $this->cleanArr(explode('/', $url));
        $found  = false;
        $parmtr = new \stdClass;// PARAMETROS para o controller
        $access = new Permission();
        
        // ----- procura a URL solicitada nas ROTAS definidas
        foreach($this->routes as $route){
            $routeArr = explode('/', $route[0]);
            $routeArr = $this->cleanArr($routeArr);

            // ----- passsando por cada sessao da ROTA
            for( $i = 0; $i < count($routeArr); $i++ ){
                // se a ROTA possuir um caracter de '{' e tiver o mesmo tamanho da URL atual
                if( (strpos($routeArr[$i], "{id") !== false) && (count($urlArr) == count($routeArr)) ) {
                    // o valor o parametros encontrado na URL sera passado para a ROTA
                    $routeArr[$i] = $urlArr[$i];
                    $parmtr->id   = $urlArr[$i];
                }
            }
            
            // ----- se URL atual for igual a ROTA
            if( implode($urlArr, '/') == implode($routeArr, '/') ){
                $found      = true;
                $controller = $route[1];// controller a ser chamado
                $metodo     = $route[2];// metodo executado pelo modulo
                $action     = $route[3];// metodo executado pelo modulo

                // ----- controle de acesso a pagina
                if( $access->check() && $action != 'free' ){
                    $metodo = 'denied';
                    $parmtr->url_back = $url;

                    if( $action == 'user' ){
                        $parmtr->url_acesso = '/entrar';
                    }
                    else if( $action == 'admin' ){
                        $parmtr->url_acesso = '/admin/entrar';
                    }
                }

                break;
            }
        }

        // ----- testa e carrega os arquivo e metodos para mostrar a pagina
        if( $found ){
            // echo "<h3 style='color:blue;'>Pagina encontrada</h3>";

            $controller_file = CTRLL_PATH."{$controller}.php";
            $controller_file = str_replace('\\', '/', $controller_file);// sistemas lunix/mac com barra invertida

            if( file_exists($controller_file) ){
                // echo "<h3 style='color:olive;'>arquivo <u>$controller</u> encontrado</h3>";

                $thisController = Container::getController($controller);

                if(method_exists($thisController, "{$metodo}")){
                    // echo "<h3 style='color:olive;'>metodo <u>$metodo</u> do controller <i>$controller</i> encontrado</h3>";

                    // ----- se existir parametros para serem enviados ao metodos
                    if((count((array)$this->getRequest()) > 0) || (count((array)$parmtr) > 0)) {
                        // echo "<h5>Parametros encontrados</h5>";

                        $newParams = (object)array_merge((array)$this->getRequest(), (array)$parmtr);

                        $thisController->$metodo($newParams);
                    }
                    else {
                        // echo "<h5>Parametros NAO encontrados</h5>";
                        $thisController->$metodo();
                    }
                }
                else {
                    echo "<h3 style='color:red;'>metodo <u>$metodo</u> do controller <i>$controller</i> NAO encontrado</h3>";
                }
            }
            else {
                echo "<h3 style='color:red;'>arquivo <u>$controller</u> NAO encontrado</h3>";
            }
        }
        else {
            // echo "<h3>Pagina NAO encontrada</h3>";
            Container::pageNotFound();
        }
    }
    

    // - regras para analizar os dados definidas em 'my_routes.php'
    private function setRoutes($routes)
    {
        $this->routes = $routes;
    }

    // - retorna as variaveis globais _GET e _POST para um novo objeto
    private function getRequest()
    {
        $obj = new \stdClass;

        foreach ($_GET as $key => $value){
            @$obj->get->$key = $value;
        }

        foreach ($_POST as $key => $value){
            @$obj->post->$key = $value;
        }

        foreach ($_FILES as $key => $value){
            @$obj->files->$key = $value;
        }

        return $obj;
    }

    // - remove elementos vazios de arrays e adiciona um elemento vazio no inicio da pilha
    private function cleanArr(array $myArr)
    {
        // *** para evitar bug quando existir uma ou mais barras no final da URL ***
        // limpando elementos vazios do array
        $cleanUrl = array_filter($myArr);
        // jogando um unico elemento vazio no inicio array mesmo se tiver outros elementos
        // quando o (explode();) for revertido com (implode();) a string sempre vai possuir uma '/' no inicio
        array_unshift($cleanUrl, '');

        return $cleanUrl;
    }

    // - retorna a URL atual da pagina
    private function getURL()
    {
        return parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    }
}