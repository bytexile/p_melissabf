<?php

namespace Core;

// clase abstrata: somente para herdar metodos, nao pode ser instanciada!
abstract class BaseController
{
    // ////////// ATRIBUTOS
    protected $view;// atributos para guardar informacoes dentro do escopo da classe
    
    private $view_path;// caminho para o HTML carregado no browser
    private $layout_path;// caminho para o layout da pagina


    // ////////// METODOS
    public function __construct()
    {
        // transforma um $this em um objeto PHP
        $this->view = new \stdClass;
    }
    

    /*
    - recebe uma string com o caminho da view
    - segundo paramentro para usar ou nao o Layout padrao
    */
    protected function renderView($viewPath, $layoutPath=false)
    {
        $this->viewPath   = $viewPath;
        $this->layoutPath = $layoutPath;

        // tenta carrega o LAYOUT se for indicado algum
        if($layoutPath) {
            $this->layout();
        }
        else {
            $this->content();
        }
    }
    

    // - verificar se o arquivo da VIEW existe, TRUE ele é carregado
    private function content()
    {
        $myPath = VIEW_PATH.$this->viewPath.'.phtml';
        $myPath = str_replace('\\', '/', $myPath);// sistemas lunix/mac com barra invertida

        if(file_exists($myPath)) {
            return require_once($myPath);
        }
        else {
            echo "<p style='color:red;'>404: arquio VIEW ({$this->viewPath}) nao encontrado</p>";
        }
    }
    

    // - verifica se o arquivo para o layout existe, TRUE ele é carregado
    protected function layout()
    {
        $myViewPath = VIEW_PATH.$this->layoutPath.'.phtml';
        $myViewPath = str_replace('\\', '/', $myViewPath);// sistemas lunix/mac com barra invertida

        if(file_exists($myViewPath)) {
            return require_once($myViewPath);
        }
        else {
            echo "<p style='color:red;'>404: arquio LAYOUT ({$this->layoutPath}) nao encontrado</p>";
        }
    }
    

    // - acesso negado a pagina
    public function denied($objURL)
    {
        // sessao criada para voltar apos login
        Session::set('url_back_save', $objURL->url_back);

        return Redirect::route($objURL->url_acesso, [
            'denied' => "<p style='color:purple;'>Voce nao tem acesso a essa pagina</p>"
        ]);
    }




    public function meuIP()
    {
        function get_client_ip_env() {
            $ipaddress = '';
            if (getenv('HTTP_CLIENT_IP'))
                $ipaddress = getenv('HTTP_CLIENT_IP');
            else if(getenv('HTTP_X_FORWARDED_FOR'))
                $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
            else if(getenv('HTTP_X_FORWARDED'))
                $ipaddress = getenv('HTTP_X_FORWARDED');
            else if(getenv('HTTP_FORWARDED_FOR'))
                $ipaddress = getenv('HTTP_FORWARDED_FOR');
            else if(getenv('HTTP_FORWARDED'))
                $ipaddress = getenv('HTTP_FORWARDED');
            else if(getenv('REMOTE_ADDR'))
                $ipaddress = getenv('REMOTE_ADDR');
            else
                $ipaddress = 'UNKNOWN';
        
            return $ipaddress;
        }

        echo var_export(unserialize(file_get_contents('http://www.geoplugin.net/php.gp?ip='.get_client_ip_env())));
    }
}