<?php
use Eleitor\Database\Record;

class ViewMeta extends Record
{
    const TABLENAME = 'view_meta';

    //Lider
private $lider;
public function get_lider()
    {
        if (empty($this->lider)) 
        {
            $this->lider = new Lider($this->id_lider);
        }

        return $this->lider;
    }

    public function get_nome_lider()
    {
        if (empty($this->lider)) 
        {
            $this->lider = new Lider($this->id_lider);
        }

        return $this->lider->nome;
    }
}

