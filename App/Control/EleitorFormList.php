<?php
use Eleitor\Control\Page;
use Eleitor\Control\Action;
use Eleitor\Widgets\Form\Form;
use Eleitor\Widgets\Form\Entry;
use Eleitor\Widgets\Form\Combo;
use Eleitor\Widgets\Container\VBox;
use Eleitor\Widgets\Datagrid\Datagrid;
use Eleitor\Widgets\Datagrid\DatagridColumn;

use Eleitor\Database\Transaction;

use Eleitor\Traits\DeleteTrait;
use Eleitor\Traits\ReloadTrait;
use Eleitor\Traits\SaveTrait;
use Eleitor\Traits\EditTrait;

use Eleitor\Widgets\Wrapper\DatagridWrapper;
use Eleitor\Widgets\Wrapper\FormWrapper;
use Eleitor\Widgets\Container\Panel;

/**
 * Cadastro Eleitor
 */
class EleitorFormList extends Page
{
    private $form;
    private $datagrid;
    private $loaded;
    private $connection;
    private $activeRecord;
    
    use EditTrait;
    use DeleteTrait;
    use ReloadTrait {
        onReload as onReloadTrait;
    }
    use SaveTrait {
        onSave as onSaveTrait;
    }
    
    /**
     * Construtor da página
     */
    public function __construct()
    {
        parent::__construct();

        $this->connection   = 'Eleitor';
        $this->activeRecord = 'eleitor';
        
        // instancia um formulário
        $this->form = new FormWrapper(new Form('form_Eleitor'));
        $this->form->setTitle('Cadastro de Eleitores');
        
        // cria os campos do formulário
        $id             = new Entry('id');
        $cpf            = new Entry('cpf');
        $nome           = new Entry('nome');
        $telefone       = new Entry('telefone');
        $endereco       = new Entry('endereco');
        $bairro         = new Entry('bairro');
        $cep            = new Entry('cep');
        $email          = new Entry('email');
        $titulo         = new Entry('titulo');
        $secao          = new Entry('secao');
        $zona           = new Entry('zona');
        $colegio        = new Entry('colegio');
        $genero         = new Entry('genero');
        $id_lider       = new Combo('id_lider');
               
        $id->setEditable(FALSE);
              
      /*
      * Trecho Implementado: chamada dos objetos
      */
//Lideres   
        Transaction::open('eleitor');
                $lideres = Lider::all();
                $items = array();
                foreach ($lideres as $obj_lider)
                {
                    $items[$obj_lider->id] = $obj_lider->nome;
                }
        Transaction::close();
                $id_lider->addItems($items);

              
//Montagem do Formulário                
        $this->form->addField('Código', $id, '10%');
        $this->form->addField('CPF', $cpf, '50%');
        $this->form->addField('Nome', $nome, '50%');
        $this->form->addField('Telefone', $telefone, '50%');
        $this->form->addField('Endereço', $endereco, '50%');
        $this->form->addField('Bairro', $bairro, '50%');
        $this->form->addField('Cep', $cep, '50%');
        $this->form->addField('Email', $email, '50%');
        $this->form->addField('Título', $titulo, '50%');
        $this->form->addField('Seção', $secao, '50%');
        $this->form->addField('Zona', $zona, '50%');
        $this->form->addField('Colégio', $colegio, '50%');
        $this->form->addField('Gênero', $genero, '50%');
        $this->form->addField('Líder', $id_lider, '50%');
              
        $this->form->addAction('Salvar', new Action(array($this, 'onSave')));
        $this->form->addAction('Limpar', new Action(array($this, 'onEdit')));
                
        // instancia a Datagrid
        $this->datagrid = new DatagridWrapper(new Datagrid);

        // instancia as colunas da Datagrid
        $id             = new DatagridColumn('id',             'Código',         'center', '10%');
        $cpf            = new DatagridColumn('cpf',            'CPF',            'left',   '50%');
        $nome           = new DatagridColumn('nome',           'Nome',           'left',   '50%');
        $telefone       = new DatagridColumn('telefone',       'Telefone',       'left',   '50%');
        $endereco       = new DatagridColumn('endereco',       'Endereço',       'left',   '50%');
        $bairro         = new DatagridColumn('bairro',         'Bairro',         'left',   '50%');
        $cep            = new DatagridColumn('cep',            'Cep',            'left',   '50%');
        $email          = new DatagridColumn('email',          'Email',          'left',   '50%');
        $titulo         = new DatagridColumn('titulo',         'Título',         'left',   '50%');
        $secao          = new DatagridColumn('secao',          'Seção',          'left',   '50%');
        $zona           = new DatagridColumn('zona',           'Zona',           'left',   '50%');
        $colegio        = new DatagridColumn('colegio',        'Colégio', 	     'left',   '50%');     
        $genero         = new DatagridColumn('genero',         'Gênero', 	     'left',   '50%');  
        $id_lider       = new DatagridColumn('nome_lider',     'Líder', 	     'left',   '50%');          

        // adiciona as colunas à Datagrid
        $this->datagrid->addColumn($id);
        $this->datagrid->addColumn($cpf);
        $this->datagrid->addColumn($nome);
        $this->datagrid->addColumn($telefone);
        $this->datagrid->addColumn($endereco);
        $this->datagrid->addColumn($bairro);
        $this->datagrid->addColumn($cep);
        $this->datagrid->addColumn($email);
        $this->datagrid->addColumn($titulo);
        $this->datagrid->addColumn($secao);
        $this->datagrid->addColumn($zona);
        $this->datagrid->addColumn($colegio);
        $this->datagrid->addColumn($genero);
        $this->datagrid->addColumn($id_lider);
       
        $this->datagrid->addAction( 'Editar',  new Action([$this, 'onEdit']),   'id', 'fa fa-edit');
        $this->datagrid->addAction( 'Excluir', new Action([$this, 'onDelete']), 'id', 'fa fa-trash');
        
        // monta a página através de uma tabela
        $box = new VBox;
        $box->style = 'display:block';
        $box->add($this->form);
        $box->add($this->datagrid);
        
        parent::add($box);
    }
    
    /**
     * Salva os dados
     */
    public function onSave()
    {
        $this->onSaveTrait();
        $this->onReload();
    }
    
    /**
     * Carrega os dados
     */
    public function onReload()
    {
        $this->onReloadTrait();   
        $this->loaded = true;
    }

    /**
     * exibe a página
     */
    public function show()
    {
        // se a listagem ainda não foi carregada
        if (!$this->loaded)
        {
            $this->onReload();
        }
        parent::show();
    }
}
