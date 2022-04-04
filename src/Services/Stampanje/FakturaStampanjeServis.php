<?php

namespace App\Services\Stampanje;

use App\Entity\Faktura;
use App\Interfaces\FakturaStampanjeSericeInterace;
use App\Repository\FakturaRepository;
use App\Services\Stampanje;

class FakturaStampanjeServis {

    private FakturaRepository $fakturaRepository;

    /**
     * @param FakturaRepository $fakturaRepository
     */
    public function __construct(FakturaRepository $fakturaRepository) {
        $this->fakturaRepository = $fakturaRepository;
    }


    public function stampaj(int $idFakture, string $formatFakture) {
        $printer = $this->getPrinter($formatFakture);
        $faktura = $this->fakturaRepository->find($idFakture);

        return $printer->stampajFakturu($faktura);

    }

    private function getPrinter($type) {
        $klasa = null;
        switch ($type) {
            case 'Excel':
                $klasa = ExcelFakturaStampanje::class;
                break;
            case 'Word':
                $klasa = WordFakturaStampanje::class;
                break;
            default:
                return false;
        }

        return new $klasa();
    }

}