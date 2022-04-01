<?php

namespace App\Interfaces;

use App\Entity\Faktura;

interface FakturaStampanjeServiceInterface {

    public  function stampajFakturu(Faktura $faktura):string;

}