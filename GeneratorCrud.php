<?php

namespace App\Models;

use App\Core\Model;

/**
 * Description of GeneratorCrud
 *
 * Classe responsável por recuperar as tabelas de um certo Banco de Dados,
 * e gerar a classe de Model das tabelas com seu respectivo nome,
 * e internamente gerar todo o CRUD para manutenção das mesmas!
 *
 * @author Luciano Charles de Souza
 * E-mail: souzacomprog@gmail.com
 * Github: https://github.com/LucianoCharlesdeSouza
 * @version 1.0.0
 * @since 2017-10-24
 */
class GeneratorCrud extends Model {

    private $database = '',
            $table, $tables = '',
            $result = null,
            $fields = [],
            $bindValues = [],
            $url = '',
            $namespace = '',
            $use = '';

    /* Aplica o nome da base de dados */

    public function setDatabase($database) {
        $this->database = $database;
    }

    /* Aplica o namespace na classe a ser gerada */

    public function setNamespace($namespace) {
        $this->namespace = $namespace;
    }

    /* Recupera o namespace aplicado no método setNamespace() */

    public function getNamespace() {
        if (!empty($this->namespace)) {
            return "namespace " . $this->namespace . ";";
        }
    }

    /* Aplica o Use na classe a ser gerada */

    public function setUse($use) {
        $this->use = $use;
    }

    /* Recupera o use aplicado no método setUse() */

    public function getUse() {
        if (!empty($this->use)) {
            return "use " . $this->use . ";
            ";
        }
    }

    /* Recupera a url da requisição ajax */

    private function getUrl() {
        return $this->url;
    }

    /* Seta a url da requisição ajax */

    public function setUrl($url_do_projeto) {
        $this->url = $url_do_projeto;
    }

    /* Recupera os valores dos Métodos de Crud */

    public function getResult() {
        return $this->result;
    }

    /* Seta o nome da tabela escolhida para gerar a Model CRUD */

    public function setTable($table) {
        $this->table = $table;
    }

    /* Recupera e popula o selectBox com os nomes das Tabelas do Banco */

    private function getAllTables() {
        $sql = "SELECT * FROM information_schema.TABLES "
                . "WHERE TABLE_SCHEMA = :nome_do_bd";
        $sql = $this->db->prepare($sql);
        $sql->bindValue(":nome_do_bd", $this->database);
        try {
            $sql->execute();
            if ($sql->rowCount() > 0) {
                $this->tables = $sql->fetchAll();
                $option = "";
                foreach ($this->tables as $Tables):
                    $option .= '<option value="' . $Tables['TABLE_NAME'] . '">' . $Tables['TABLE_NAME'] . '</option>' . PHP_EOL;

                endforeach;
                $this->result = $option;

                return true;
            }
        } catch (\PDOException $e) {
            die($e->getMessage());
        }
    }

    /* Recupera o nome de apenas 01 Tabela do Banco */

    private function getTable() {
        $sql = "SELECT * FROM information_schema.TABLES "
                . "WHERE TABLE_SCHEMA = :nome_do_bd "
                . "AND TABLE_NAME = :nome_da_tabela;
            ";
        $sql = $this->db->prepare($sql);
        $sql->bindValue(":nome_do_bd", $this->database);
        $sql->bindValue(":nome_da_tabela", $this->table);
        try {
            $sql->execute();
            if ($sql->rowCount() > 0) {
                return $this->result = $sql->fetchAll()[0]['TABLE_NAME'];
            }
        } catch (\PDOException $e) {
            die($e->getMessage());
        }
    }

    /* Recupera todos os nomes das colunas da tabela selecionada */

    private function getColumnsTable() {
        $sql = "SELECT * FROM information_schema.COLUMNS "
                . "WHERE TABLE_SCHEMA = :nome_do_bd "
                . "AND TABLE_NAME = :nome_da_tabela;
            ";
        $sql = $this->db->prepare($sql);
        $sql->bindValue(":nome_do_bd", $this->database);
        $sql->bindValue(":nome_da_tabela", $this->table);
        try {
            $sql->execute();
            if ($sql->rowCount() > 0) {
                $columns = [];
                foreach ($sql->fetchAll() as $Columns):
                    $columns[] = "$" . $Columns['COLUMN_NAME'];
                endforeach;
                $this->fields = $columns;
                $this->bindValues = $columns;
                $columns = implode(',' . PHP_EOL, $columns);
                return $columns;
            }
        } catch (\PDOException $e) {
            die($e->getMessage());
        }
    }

    /* Monta o html do formulário ja com o selectBox populado */

    public function CreateHtml() {
        $this->getAllTables();
        $html = '';
        $html .= $this->CreateRowNR('<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">');
        $html .= $this->CreateRowNR('<!-- Optional theme -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">');
        $html .= $this->CreateRowNR('<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>');


        $html .= $this->CreateRow('<!-- Latest compiled and minified Jquery -->');
        $html .= $this->CreateRow('<script src="https://code.jquery.com/jquery-3.2.1.js"');
        $html .= $this->CreateRow('integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE="');
        $html .= $this->CreateRowNR('crossorigin="anonymous"></script>');

        $html .= $this->CreateRowNR("<!-- === Formulario criado dinamicamente pela Classe GeneratorCrud !== = -->");
        $html .= $this->CreateRowNR('<div class="content">');
        $html .= $this->CreateRowNR("<div class='alert'></div>");
        $html .= $this->CreateRowNR("<h3>Gerador de Class Crud!</h3>");

        $html .= $this->CreateRowNR("<form method='POST'>");
        $html .= $this->CreateRowNR("<div class='form-group'>");
        $html .= $this->CreateRowNR("<label for='tabelas'>Escolha uma Tabela</label>");
        $html .= $this->CreateRowNR("<select name='name_table' class='form-control' id='tabelas'>");
        $html .= $this->CreateRowNR("<option value=''>Selecione uma Tabela...</option>");
        $html .= $this->CreateRowNR("<option value='CreateAllTables'>Selecionar todas as tabelas</option>");
        $html .= $this->CreateRowNR("{$this->getResult()}");
        $html .= $this->CreateRowNR("</select>");
        $html .= $this->CreateRowNR("</div>");

        $html .= $this->CreateRowNR("<div class='form-group'>");
        $html .= $this->CreateRowNR("<label for='namespace'>Digite um Namespace (*opcional)</label>");
        $html .= $this->CreateRowNR("<input type='text' name='namespace' placeholder='App\Models' class='form-control' id='namespace'>");
        $html .= $this->CreateRowNR("</div>");


        $html .= $this->CreateRowNR("<div class ='form-group'>");
        $html .= $this->CreateRowNR("<label for='use'>Digite um Use (*opcional)</label>");
        $html .= $this->CreateRowNR("<input type='text' name='use' placeholder='App\Core\Model' class='form-control' id='use'>");
        $html .= $this->CreateRowNR("</div>");

        $html .= $this->CreateRowNR("<div class ='form-group'>");
        $html .= $this->CreateRowNR("<label for='directory'>Digite um Diretorio (*se não for passado, será criado na raiz)</label>");
        $html .= $this->CreateRowNR("<input type='text' name='directory' placeholder='App/Models/' class='form-control' id='directory'>");
        $html .= $this->CreateRowNR("</div>");

        $html .= $this->CreateRowNR("<div class='form-group'>");
        $html .= $this->CreateRowNR("<button class='btn btn-success'>Criar Classe</button>");
        $html .= $this->CreateRowNR("</div>");
        $html .= $this->CreateRowNR("</form>");
        $html .= $this->CreateRowNR("</div>");
        $html .= $this->CreateRowNR("</div>");

        $retorno = [
            "Form" => $html,
            "ScriptAjax" => $this->CreateAjax()
        ];
        return $retorno;
    }

    /* Cria o script js resposavel pela requisição ajax */

    private function CreateAjax() {
        $ajax = "";
        $ajax .= $this->CreateRowNR("<!--===Script js criado dinamicamente pela Classe GeneratorCrud!===-->");
        $ajax .= $this->CreateRowNR("<script>");
        $ajax .= $this->CreateRowNR("$('form').submit(function() {");
        $ajax .= $this->CreateRowNR("var form = $(this);");
        $ajax .= $this->CreateRowNR("var dados = new FormData($(this)[0]);");
        $ajax .= $this->CreateRowNR("$.ajax({");
        $ajax .= $this->CreateRowNR('url: "' . $this->url . '",');
        $ajax .= $this->CreateRowNR("data: dados,");
        $ajax .= $this->CreateRowNR("type: 'POST',");
        $ajax .= $this->CreateRowNR("dataType: 'json',");
        $ajax .= $this->CreateRowNR("processData: false,");
        $ajax .= $this->CreateRowNR("contentType: false,");
        $ajax .= $this->CreateRowNR("beforeSend: function(data) {");
        $ajax .= $this->CreateRowNR("},");
        $ajax .= $this->CreateRowNR("success: function(data) {");
        $ajax .= $this->CreateRowNR("if(data.campo_vazio) {");
        $ajax .= $this->CreateRow("$('.alert').html");
        $ajax .= $this->CreateRow("('<div ");
        $ajax .= $this->CreateRow('class="alert alert-info">Por favor selecione uma tabela...');
        $ajax .= $this->CreateRowNR("</div>');");
        $ajax .= $this->CreateRowNR("}");
        $ajax .= $this->CreateRowNR("if(data.sucesso) {");
        $ajax .= $this->CreateRow("$('.alert').html");
        $ajax .= $this->CreateRow("('<div ");
        $ajax .= $this->CreateRow('class="alert alert-success">Classe criada com Sucesso!');
        $ajax .= $this->CreateRowNR("</div>');");
        $ajax .= $this->CreateRowNR("}");
        $ajax .= $this->CreateRowNR("}");
        $ajax .= $this->CreateRowNR("});");
        $ajax .= $this->CreateRowNR("return false;");
        $ajax .= $this->CreateRowNR("});");
        $ajax .= $this->CreateRowNR("</script>");

        return $ajax;
    }

    /* Recupera o valor do parametro $row e aplica quebra de linha("\n\r") ao final da mesma */

    private function CreateRowNR($row) {
        return $row . PHP_EOL;
    }

    /* Recupera o valor do parametro $row */

    private function CreateRow($row) {
        return $row;
    }

    /* Aplica todos os métodos para criar a Class e cria o arquivo com nome da Classe mais extensão .php */

    public function CreateClass($table, $directory = null) {
        $Table = str_replace('-', '_', $table);
        $Table = ucfirst($Table);
        $class = "";
        $class .= $this->CreateRowNR("<?php");
        $class .= $this->CreateRowNR("{$this->getNamespace()}");
        $class .= $this->CreateRowNR("{$this->getUse()}");
        $class .= $this->CreateRowNR("class {$Table} extends Model {");
        $class .= $this->CreateRowNR('//Atributo responsavel por receber a Instrucao SQL.');
        $class .= $this->CreateRowNR('private $Sql;');
        $class .= $this->CreateRowNR('//Atributo responsavel por receber o nome da Tabela.');
        $class .= $this->CreateRowNR('private $table = "' . $table . '";');
        $class .= $this->CreateRowNR("//Atributo responsavel por armazenar o resultado dos metodos do CRUD.");
        $class .= $this->CreateRowNR('private $result;');
        $class .= $this->CreateRowNR("private {$this->getColumnsTable()};");
        $class .= $this->CreateGetResult();
        $class .= $this->Create();
        $class .= $this->ReadAll();
        $class .= $this->Update();
        $class .= $this->Delete();
        $class .= $this->ReadById();
        $class .= "}" . PHP_EOL;
        $file = fopen($directory . $Table . ".php", "w");
        fwrite($file, utf8_encode($class));
        fclose($file);
    }

    /* Cria o método getResult() */

    private function CreateGetResult() {
        $class = $this->CreateRowNR('//Metodo que obtem o valor do atributo $result.');
        $class .= $this->CreateRowNR("public function getResult(){");
        $class .= $this->CreateRowNR('return $this->result;');
        $class .= $this->CreateRowNR("}");
        return $class;
    }

    /* Cria o método ReadAll() */

    public function ReadAll() {
        $class = $this->CreateRowNR('//Metodo que fara a consulta a todos os campos da tabela.');
        $class .= $this->CreateRowNR("public function ReadAll(){");
        $class .= $this->CreateRow('$this->Sql = "SELECT * FROM "');
        $class .= $this->CreateRowNR('.$this->table;');
        $class .= $this->CreateRowNR('$this->Sql = $this->db->prepare($this->Sql);');
        $class .= $this->CreateRowNR("try{");
        $class .= $this->CreateRowNR('$this->Sql->execute();');
        $class .= $this->CreateRowNR('if($this->Sql->rowCount() > 0){');
        $class .= $this->CreateRowNR('$this->result = $this->Sql->fetchAll();');
        $class .= $this->CreateRowNR("return true;");
        $class .= $this->CreateRowNR("}");
        $class .= $this->CreateRowNR('} catch (PDOException $e){');
        $class .= $this->CreateRowNR('die($e->getMessage());');
        $class .= $this->CreateRowNR("}");
        $class .= $this->CreateRowNR("}");
        return $class;
    }

    /* Cria o método ReadById() */

    public function ReadById() {
        $class = $this->CreateRowNR('//Metodo que fara a consulta a todos os campos da tabela.');
        $class .= $this->CreateRowNR('public function ReadById($id){');
        $class .= $this->CreateRowNR('$this->Sql = "SELECT * FROM  " .$this->table." WHERE ' . $this->FieldId() . ' = ' . $this->BindId() . '";');
        $class .= $this->CreateRowNR('$this->Sql = $this->db->prepare($this->Sql);');
        $class .= $this->CreateRowNR('$this->Sql->bindValue("' . $this->BindId() . '", $id);');
        $class .= $this->CreateRowNR("try{");
        $class .= $this->CreateRowNR('$this->Sql->execute();');
        $class .= $this->CreateRowNR('if($this->Sql->rowCount() > 0){');
        $class .= $this->CreateRowNR('$this->result = $this->Sql->fetchAll();');
        $class .= $this->CreateRowNR("return true;");
        $class .= $this->CreateRowNR("}");
        $class .= $this->CreateRowNR('} catch (PDOException $e){');
        $class .= $this->CreateRowNR('die($e->getMessage());');
        $class .= $this->CreateRowNR("}");
        $class .= $this->CreateRowNR("}");
        return $class;
    }

    /* Cria a string com os nomes da tabela para substituição de valores  */

    private function CreateBindValue() {
        $this->getColumnsTable();
        unset($this->bindValues[0]);
        $Bind = '';
        foreach ($this->bindValues as $binds):
            $Bind .= '$this->Sql->bindValue("' . str_replace('$', ':', $binds) . '",'
                    . '$dados_form["' . str_replace('$', '', $binds) . '"]);' . PHP_EOL;
        endforeach;
        return $Bind;
    }

    /* Recupera os nomes dos campos da tabela á ser passado na instrução SQL */

    private function FieldsForCreate() {
        unset($this->fields[0]);
        $this->fields = str_replace('$', '', $this->fields);
        return implode(',', $this->fields);
    }

    /* Cria  nomes dos campos da tabela á ser passado na instrução SQL como parametros de substituição */

    private function ValuesForCreate() {
        unset($this->bindValues[0]);
        $this->bindValues = str_replace('$', ':', $this->bindValues);
        return implode(',', $this->bindValues);
    }

    /* Cria o método Create() */

    public function Create() {
        $class = $this->CreateRowNR('//Metodo que fara a insercao de todos os campos da tabela.');
        $class .= $this->CreateRowNR('public function Create(array $dados_form){');
        $class .= $this->CreateRow('$this->Sql = "INSERT INTO "');
        $class .= $this->CreateRowNR('.$this->table. " (' . $this->FieldsForCreate() . ') VALUES (' . $this->ValuesForCreate() . ')";');
        $class .= $this->CreateRowNR('$this->Sql = $this->db->prepare($this->Sql);');
        $class .= $this->CreateRowNR($this->CreateBindValue());
        $class .= $this->CreateRowNR("try{");
        $class .= $this->CreateRowNR('$this->Sql->execute();');
        $class .= $this->CreateRowNR('if($this->Sql->rowCount() == 1){');
        $class .= $this->CreateRowNR('$this->result = $this->db->lastInsertId();');
        $class .= $this->CreateRowNR("return true;");
        $class .= $this->CreateRowNR("}");
        $class .= $this->CreateRowNR('} catch (PDOException $e){');
        $class .= $this->CreateRowNR('die($e->getMessage());');
        $class .= $this->CreateRowNR("}");
        $class .= $this->CreateRowNR("}");
        return $class;
    }

    /* Cria os nomes dos campos mais os parametros de substituição da instrução SQL
     * retirando a última virgula */

    private function CreateSETUpdate() {
        $this->getColumnsTable();
        unset($this->bindValues[0]);
        $Bind = '';
        $cont = count($this->bindValues);
        for ($i = 1; $i <= $cont; $i++):
            $virgula = ($i < $cont ? ", " : "");
            $Bind .= str_replace('$', '', $this->bindValues[$i]) . " = " . str_replace('$', ':', $this->bindValues[$i]) . $virgula;
        endfor;
        return $Bind;
    }

    /* Cria o método Update() */

    public function Update() {
        $class = $this->CreateRowNR('//Metodo que fara a alteracao de todos os campos da tabela.');
        $class .= $this->CreateRowNR('public function Update($id,array $dados_form){');
        $class .= $this->CreateRow('$this->Sql = "UPDATE "');
        $class .= $this->CreateRowNR('.$this->table. " SET ' . $this->CreateSETUpdate() . ' WHERE ' . $this->FieldId() . ' = ' . $this->BindId() . '";');
        $class .= $this->CreateRowNR('$this->Sql = $this->db->prepare($this->Sql);');
        $class .= $this->CreateRowNR('$this->Sql->bindValue("' . $this->BindId() . '", $id);');
        $class .= $this->CreateRowNR($this->CreateBindValue());
        $class .= $this->CreateRowNR("try{");
        $class .= $this->CreateRowNR('$this->Sql->execute();');
        $class .= $this->CreateRowNR('if($this->Sql->rowCount() == 1){');
        $class .= $this->CreateRowNR('$this->result = $this->db->lastInsertId();');
        $class .= $this->CreateRowNR("return true;");
        $class .= $this->CreateRowNR("}");
        $class .= $this->CreateRowNR('} catch (PDOException $e){');
        $class .= $this->CreateRowNR('die($e->getMessage());');
        $class .= $this->CreateRowNR("}");
        $class .= $this->CreateRowNR("}");
        return $class;
    }

    /* Recupera o nome campo auto-incremento da Tabela
     * Este campo sempre deve ser o primeiro da Tabela
     */

    private function FieldId() {
        $this->getColumnsTable();
        $id = $this->fields[0];
        $id = str_replace('$', '', $id);
        return $id;
    }

    /* Cria o paramentro do campo auto-incremento para substituição na instrução SQL */

    private function BindId() {
        $this->getColumnsTable();
        $id = $this->fields[0];
        $id = str_replace('$', ':', $id);
        return $id;
    }

    /* Cria o método Delete() */

    public function Delete() {
        $class = $this->CreateRowNR('//Metodo que fara a exclusao de todos os campos da tabela.');
        $class .= $this->CreateRowNR('public function Delete($id){');
        $class .= $this->CreateRowNR('$this->Sql = "DELETE FROM " .$this->table." WHERE ' . $this->FieldId() . ' = ' . $this->BindId() . '";');
        $class .= $this->CreateRowNR('$this->Sql = $this->db->prepare($this->Sql);');
        $class .= $this->CreateRowNR('$this->Sql->bindValue("' . $this->BindId() . '", $id);');
        $class .= $this->CreateRowNR("try{");
        $class .= $this->CreateRowNR('$this->Sql->execute();');
        $class .= $this->CreateRowNR('if($this->Sql->rowCount() == 1){');
        $class .= $this->CreateRowNR("return true;");
        $class .= $this->CreateRowNR("}");
        $class .= $this->CreateRowNR('} catch (PDOException $e){');
        $class .= $this->CreateRowNR('die($e->getMessage());');
        $class .= $this->CreateRowNR("}");
        $class .= $this->CreateRowNR("}");
        return $class;
    }

    public function GenerateClass() {
        $dados = [];
        $dados_form = filter_input_array(INPUT_POST, FILTER_DEFAULT);
        if (empty($dados_form['name_table'])) {
            $dados['campo_vazio'] = true;
        } elseif ($dados_form['name_table'] === "CreateAllTables") {
            $this->getAllTables();
            foreach ($this->tables as $t) {
                $dados['sucesso'] = true;
                $this->setNamespace($dados_form['namespace']);
                $this->setUse($dados_form['use']);
                $this->setTable($t['TABLE_NAME']);
                $this->CreateClass($t['TABLE_NAME']);
            }
        } elseif (!empty($dados_form['name_table']) && $dados_form['name_table'] != "CreateAllTables") {
            $dados['sucesso'] = true;
            $this->setNamespace($dados_form['namespace']);
            $this->setUse($dados_form['use']);
            $this->setTable($dados_form['name_table']);
            $this->CreateClass($dados_form['name_table'], $dados_form['directory']);
        }

        echo json_encode($dados);
        exit();
    }

}
