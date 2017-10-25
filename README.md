# GeneratorClassCrud
<h3>Classe responsável por recuperar as tabelas de um banco de dados e gerar a classe de CRUD das mesmas!</h3>

<p> <strong>Exemplo de uso:</strong></p>
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
        

<h3>Exemplo de uso na estrutura MVC</h3>
# Arquivo de classe createcrudController.php

    private $Crud,
            $dados = [];
            
    public function __construct() {
        $this->Crud = new GeneratorCrud();
        $this->Crud->setDatabase("nome_do_seu_banco");
    }

    public function index() {         
        $this->Crud->setUrl("http://localhost/seuaprojeto/createcrud/create");
        $this->dados = $this->Crud->CreateHtml();

        $this->loadView('crudview', $this->dados);
    }

    public function create() {
        $this->Crud->GenerateClass();
    }
