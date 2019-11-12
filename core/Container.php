<?php

namespace Core;

class Container
{
    // ----- retorna uma instancia do CONTROLLER solicitado atravez do parametro do metodo
    public static function getController($controller)
    {
        $controller = 'App\\Controller\\'.$controller;
        return new $controller;
    }


    // ---------------------------------------------------------
    // ----- retorna uma instancia da MODEL solicitada para conexao com o banco
    public static function getModel($model)
    {
        $objModel = "\\App\\Model\\".$model;
        return new $objModel(DataBase::getDB());
    }
    
    
    // - revisao do metodo anterior, para chamar mais de uma MODEL
    public static function getModelEx($model, $conn)
    {
        $objModel = "\\App\\Model\\".$model;
        return new $objModel($conn);
    }
    // ---------------------------------------------------------


    // ----- limpa string
    public static function stgClean($string)
    {
        $search  = explode(",", " ,ç,æ,œ,á,é,í,ó,ú,à,è,ì,ò,ù,ä,ë,ï,ö,ü,ÿ,â,ê,î,ô,û,å,e,i,ø,u,ã,Ã,Ç,Á,É,Í,Ó,Ú,À,È,Ì,Ò,Ù,Ä,Ë,Ï,Ö,Ü,Ÿ,Â,Ê,Î,Ô,Û,Å,E,I,Ø,U");
        $replace = explode(",", "-,c,ae,oe,a,e,i,o,u,a,e,i,o,u,a,e,i,o,u,y,a,e,i,o,u,a,e,i,o,u,a,A,C,A,E,I,O,U,A,E,I,O,U,A,E,I,O,U,Y,A,E,I,O,U,A,E,I,O,U,A");

        $string = str_replace($search, $replace, $string);

        return strtolower($string);
    }


    // ----- pagina nao encontrada
    public static function pageNotFound($path = null)
    {
        if( $path && file_exists($erro_path) ){
            $erro_path = __DIR__.'/../app/View/'.$path.'/404.phtml';
        }
        else {
            // echo "<h1>Erro 404</h1><p> Página não encontrada!</p>";
            $erro_path = __DIR__.'/../app/View/site/404.phtml';
        }

        return require_once($erro_path);
    }
}