<?php
use Eleitor\Database\Record;

class Lider extends Record
{
    const TABLENAME = 'lider';

//Comunidade
private $comunidade;
public function get_comunidade()
    {
        if (empty($this->comunidade)) 
        {
            $this->comunidade = new Comunidade($this->id_comunidade);
        }

        return $this->comunidade;
    }
public function get_nome_comunidade()
    {
        if (empty($this->comunidade)) {
            $this->comunidade = new Comunidade($this->id_comunidade);
        }

        return $this->comunidade->nome;
    }
}

