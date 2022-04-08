<?php

namespace App\Services\Stampanje;

use App\Exceptions\PrinterException;
use App\Repository\FakturaRepository;

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
                throw new PrinterException('Stampac ne postoji');
        }

        return new $klasa();
    }

}