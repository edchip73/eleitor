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
 * Cadastro de Usuários
 */
class UsuarioFormList extends Page
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
        $this->activeRecord = 'Usuario';
        
        // instancia um formulário
        $this->form = new FormWrapper(new Form('form_usuarios'));
        $this->form->setTitle('Cadastro de Usuários');
        
        // cria os campos do formulário
        $id   = new Entry('id');
        $nome = new Entry('nome');
        $email = new Entry('email');
        $senha = new Entry('senha');
        
        $id->setEditable(FALSE);
                
        $this->form->addField('Código',     $id,    '10%');
        $this->form->addField('Nome',  $nome,  '50%');
        $this->form->addField('Email',  $email,  '50%');
        $this->form->addField('Senha',  $senha,  '50%');
                
        $this->form->addAction('Salvar', new Action(array($this, 'onSave')));
        $this->form->addAction('Limpar', new Action(array($this, 'onEdit')));
        
        // instancia a Datagrid
        $this->datagrid = new DatagridWrapper(new Datagrid);

        // instancia as colunas da Datagrid
        $id   = new DatagridColumn(     'id',    'Código',   'center','10%');
        $nome     = new DatagridColumn( 'nome',  'Nome','left',  '50%');
        $email     = new DatagridColumn( 'email',  'Email','left',  '50%');
        $senha     = new DatagridColumn( 'senha',  'Senha','left',  '50%');

        // adiciona as colunas à Datagrid
        $this->datagrid->addColumn($id);
        $this->datagrid->addColumn($nome);
        $this->datagrid->addColumn($email);
        $this->datagrid->addColumn($senha);
        
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
