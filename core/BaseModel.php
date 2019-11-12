<?php

namespace Core;

use PDO;

// - abstract, apenas seja herdada nao pode ser instanciada
abstract class BaseModel
{
    // ///// ATRIBUTOS
    // - private, so pode existir na classe nem herdeiros tem acesso
    private $pdo;// obj da conexao com o banco
    protected $table;// precisa ser definido na classe que herdar


    // ///// METODOS
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;// obj de conexao
    }


    // - retorna dados do ID indicado
    public function Find(array $id)
    {
        $data = $id;

        $fields = implode(',', array_keys($data));
        $places = ':'.implode(',:', array_keys($data));

        $query  = "SELECT * FROM {$this->table} WHERE {$fields}={$places}";
        $stmt   = $this->pdo->prepare($query);

        foreach($data as $name => $val) {
            $stmt->bindValue(":{$name}", $val);
        }

        $stmt->execute();
        $result = $stmt->fetch();
        $stmt->closeCursor();

        return $result;
    }


    // - retorna dados das IDs indicadas em uma array com KEY com o mesmo nome da coluna
    public function FindGroup(array $columArr)
    {
        $colum  = key($columArr);
        $places = implode(',', $columArr[$colum]);

        $query  = "SELECT * FROM {$this->table} WHERE {$colum} IN ({$places})";
        $stmt   = $this->pdo->prepare($query);

        $stmt->execute();

        $result = $stmt->fetchAll();
        $stmt->closeCursor();

        return $result;
    }
    

    // - RETORNO todos os registros da tabela
    public function All($table)
    {
        // o nome da tabela é dafinido na classe HERDEIRA
        $query = "SELECT * FROM {$table}";
        $stmt  = $this->pdo->prepare($query);
        
        $stmt->execute();
        
        $result = $stmt->fetchAll();
        $stmt->closeCursor();

        return $result;
    }


    // - ACAO guardar novo registro
    public function Save(array $data)
    {
        $cadastrar = false;

        // ----- verifica se o email ja existe
        
        $check_email = $this->pdo->prepare("SELECT email FROM {$this->table} WHERE email='{$data['email']}'");
        $check_email->execute();
        
        if( count($check_email->fetchAll()) == 0 ){
            $cadastrar = true;
        }

        // -----
        
        if( $cadastrar ){
            $fields = implode(',', array_keys($data));
            $places = ':'.implode(',:', array_keys($data));

            $query  = "INSERT INTO {$this->table} ({$fields}) VALUES ({$places})";
            $stmt   = $this->pdo->prepare($query);

            foreach($data as $name => $val) {
                $stmt->bindValue(":{$name}", $val);
            }
            
            $result = $stmt->execute();
            
            $new_id = $this->pdo->lastInsertId();// ID do novo registro
            $stmt->closeCursor();

            return ['status'=>$result, 'id'=>$new_id];
        }
        else {
            return ['status'=>false, 'message'=>'E-mail ja cadastrado'];
        }
    }


    // - ATUALIZAR
    public function Update(array $data, array $id)
    {
        $fields_id = implode(',',
            array_keys($id)
        );

        $fields_change = implode(', ',
            array_map(function($camp){
                return "{$camp}=:{$camp}";
            }, array_keys($data))
        );
        
        $query = "UPDATE {$this->table} SET {$fields_change} WHERE {$fields_id}='{$id[$fields_id]}'";
        $stmt  = $this->pdo->prepare($query);

        foreach($data as $name => $val) {
            $stmt->bindValue(":{$name}", $val);
        }

        $result = $stmt->execute();
        $stmt->closeCursor();

        return $stmt;
    }
}