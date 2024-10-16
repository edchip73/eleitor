<?php

use Eleitor\Control\Page;
use Eleitor\Widgets\Dialog\Message;
use Eleitor\Database\Transaction;
use Eleitor\Widgets\Container\Panel;

use Dompdf\Dompdf;
use Dompdf\Options;

/**
 * Relatório de Eleitor
 */
class Desempenho extends Page
{
    /**
     * método construtor
     */
    public function __construct()
    {
        parent::__construct();

        $loader = new \Twig\Loader\FilesystemLoader('App/Resources');
	    $twig = new \Twig\Environment($loader);

        // vetor de parâmetros para o template
        $replaces = array();
        
        try
        {
            // inicia transação com o banco 'Eleitor'
            Transaction::open('Eleitor');
            $replaces['eleitor'] = ViewMeta::all();
            Transaction::close(); // finaliza a transação
        }
        catch (Exception $e)
        {
            new Message('error', $e->getMessage());
            Transaction::rollback();
        }
        
        $content = $twig->render('Eleitor_report.html', $replaces);
        
     
        //Chamada do PDF
        $options = new Options;
        $options->set('dpi', 128);

        $dompdf = new Dompdf('Eleitor');
        $dompdf->loadHtml($content);
        $dompdf->setPaper('A4','portrait');
        $dompdf->render();

        $filename = 'tmp/Líderes.pdf';

        file_put_contents($filename, $dompdf->output());
        //echo "<script> window.open('{$filename}')</script>";

        // cria um painél para conter o formulário
        $panel = new Panel('Desempenho de Líderes');
        $panel->add($content);
       
        parent::add($panel);
  

    }
    
}

