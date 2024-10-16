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
 * Cadastro de Lider
 */
class LiderFormList extends Page
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
        $this->activeRecord = 'lider';
        
        // instancia um formulário
        $this->form = new FormWrapper(new Form('form_lider'));
        $this->form->setTitle('Cadastro de Líderes');
        
        // cria os campos do formulário
        $id   = new Entry('id');
        $nome = new Entry('nome');
        $meta = new Entry('meta');
        $id_comunidade = new Combo('id_comunidade');
                
        $id->setEditable(FALSE);

              /*
      * Trecho Implementado: chamada dos objetos
      */
        //Comunidade   
        Transaction::open('eleitor');
        $comunidades = Comunidade::all();
        $items = array();
        foreach ($comunidades as $obj_comunidade)
        {
            $items[$obj_comunidade->id] = $obj_comunidade->nome;
        }
        Transaction::close();
        $id_comunidade->addItems($items);

        //Montagem do Formulário    
              
        $this->form->addField('Código',     $id,    '10%');
        $this->form->addField('Nome',       $nome,  '50%');
        $this->form->addField('Meta',       $meta,  '50%');
        $this->form->addField('Comunidade', $id_comunidade,  '50%');
                
        $this->form->addAction('Salvar', new Action(array($this, 'onSave')));
        $this->form->addAction('Limpar', new Action(array($this, 'onEdit')));
        
        // instancia a Datagrid
        $this->datagrid = new DatagridWrapper(new Datagrid);

        // instancia as colunas da Datagrid
        $id             = new DatagridColumn('id',              'Código',       'center','10%');
        $nome           = new DatagridColumn('nome',            'Nome',         'left',  '50%');
        $meta           = new DatagridColumn('meta',            'Meta',         'left',  '50%');
        $id_comunidade  = new DatagridColumn('nome_comunidade', 'Comunidade',   'left',  '50%');

        // adiciona as colunas à Datagrid
        $this->datagrid->addColumn($id);
        $this->datagrid->addColumn($nome);
        $this->datagrid->addColumn($meta);
        $this->datagrid->addColumn($id_comunidade);
        
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
