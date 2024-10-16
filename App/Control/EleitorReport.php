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
class EleitorReport extends Page
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
            // inicia transação com o banco 'catalogo'
            Transaction::open('eleitor');
            $replaces['eleitor'] = Eleitores_View::all();
            Transaction::close(); // finaliza a transação
        }
        catch (Exception $e)
        {
            new Message('error', $e->getMessage());
            Transaction::rollback();
        }
        
        $content = $twig->render('eleitor_report2.html', $replaces);
        
        //Chamada do PDF
        $options = new Options;
        $options->set('dpi', 128);

        $dompdf = new Dompdf('Eleitores');
        $dompdf->loadHtml($content);
        $dompdf->setPaper('A4','landscape');
        $dompdf->render();

        $filename = 'tmp/Eleitores.pdf';

        file_put_contents($filename, $dompdf->output());
        
        //echo "<script> window.open('{$filename}')</script>";

        // cria um painél para conter o formulário
        $panel = new Panel('Relatório de Eleitores');
        $panel->add($content);
        
        parent::add($panel);

        //header("Content-type: application/vnd.ms-excel");
        //header("Content-Disposition: attachment;Filename=Eleitores.xls");
       
    }
    
}