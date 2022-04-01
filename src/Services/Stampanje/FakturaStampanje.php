<?php

namespace App\Services\Stampanje;
use App\Entity\Faktura;
use App\Services\Stampanje;
use App\Interfaces\FakturaStampanjeServiceInterface;

class FakturaStampanje {

    private FakturaStampanjeServiceInterface $stampac;
    public function __construct($type) {
        $klasa = 'App\Services\Stampanje\\'.$type.'FakturaStampanje';
        $this->stampac= new $klasa();
    }

    public function stampaj(Faktura $faktura){
        return $this->stampac->stampajFakturu($faktura);
    }

}