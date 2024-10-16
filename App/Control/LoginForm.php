<?php

use Eleitor\Control\Page;
use Eleitor\Control\Action;
use Eleitor\Widgets\Form\Form;
use Eleitor\Widgets\Form\Entry;
use Eleitor\Widgets\Form\Password;
use Eleitor\Widgets\Wrapper\FormWrapper;
use Eleitor\Database\Transaction;
use Eleitor\Session\Session;

/**
 * Formulário de Login
 */
class LoginForm extends Page
{
    private $form; // formulário
    
    /**
     * Construtor da página
     */
    public function __construct()
    {
        parent::__construct();

        // instancia um formulário
        $this->form = new FormWrapper(new Form('form_login'));
        $this->form->setTitle('Eleitor');
        
        $login      = new Entry('login');
        $password   = new Password('password');
        
        $login->placeholder    = 'Seu Login';
        $password->placeholder = 'Sua Senha';
        
        $this->form->addField('Login',    $login,    200);
        $this->form->addField('Senha',    $password, 200);
        $this->form->addAction('Login', new Action([$this, 'onLogin']));
        
        // adiciona o formulário na página
        parent::add($this->form);
    }

/**
     * Login
     */
    public function onLogin($param)
    {
        $data = $this->form->getData();
        if ($data->login == 'eleitor@luknet.com.br' AND $data->password == '@eleitor$73')
        {
            Session::setValue('logged', TRUE);
            echo "<script language='JavaScript'> window.location = 'index.php'; </script>";
        }
    }
    
    /**
     * Logout
     */
    public function onLogout($param)
    {
        Session::setValue('logged', FALSE);
        echo "<script language='JavaScript'> window.location = 'index.php'; </script>";
    }
}
