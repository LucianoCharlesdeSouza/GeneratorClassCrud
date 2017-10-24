# GeneratorClassCrud
Classe responsável por recuperar as tabelas de um banco de dados e gerar a classe de CRUD das mesmas

# Arquivo index.php
<?php
        
        $dados = [];
        
        $Crud = new GeneratorCrud();
        $Crud->setDatabase("nome_do_banco_de_dados");
        
        $Crud->setUrl("http://localhost/seuprojeto/seuarquivo");
        $dados = $Crud->CreateHtml();
        
        echo $dados['Form'];
        echo $dados['ScriptAjax'];
  ?>
