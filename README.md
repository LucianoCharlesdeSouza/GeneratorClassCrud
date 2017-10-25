# GeneratorClassCrud
<h3>Classe responsável por recuperar as tabelas de um banco de dados e gerar a classe de CRUD das mesmas!</h3>

# Arquivo index.php
      
        $dados = [];
        
        $Crud = new GeneratorCrud();
        $Crud->setDatabase("nome_do_banco_de_dados");
        
        $Crud->setUrl("http://localhost/seuprojeto/requisicao_ajax.php");
        $dados = $Crud->CreateHtml();
        
        echo $dados['Form'];
        echo $dados['ScriptAjax'];
 
# Arquivo requisicao_ajax.php
        
        $Crud = new GeneratorCrud();
        $Crud->GenerateClass();
        
