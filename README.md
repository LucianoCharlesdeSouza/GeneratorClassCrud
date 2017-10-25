# GeneratorClassCrud
<h3>Classe responsável por recuperar as tabelas de um banco de dados e gerar a classe de CRUD das mesmas!</h3>

# Métodos Públicos da classe
<strong>setDatabase("banco");</strong>
<p>Responsável por passar á classe o nome do banco de dados á qual se conectará!</p>

<strong>setUrl("requisicao_ajax.php");</strong>
<p>Responsável por passar a url no qual será feito a requisição ajax</p>

<strong>CreateHtml();</strong>
<p>Responsável por criar o Html do formulário ja preenchido com os nomes das tabelas do banco escolhido em <strong>setDatabase()</strong>, mais campos restantes!</p>
<p>Terá como retorno um array com 02 indices <strong>Form</strong> e <strong>ScriptAjax</strong></p>

<strong>GenerateClass();</strong>
<p>Responsável por receber os dados do formulário e gerar as classes de Crud! (este método de ser passado no arquivo onde será feito a requisição ajax pelo método <strong>setUrl()</strong>)</p>

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
    
    
    
    
    
   <h3>Extrair o Formulário na View</h3>
   # Arquivo crudview.php
   
    
        echo $Form; 
        echo $ScriptAjax;
   
    
