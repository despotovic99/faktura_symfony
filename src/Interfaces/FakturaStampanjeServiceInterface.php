<?php

namespace App\Interfaces;

use App\Entity\Faktura;

interface FakturaStampanjeSericeInterace {

    public  function stampajFakturu(Faktura $faktura):string;

}