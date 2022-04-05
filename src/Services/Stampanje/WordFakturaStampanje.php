<?php

namespace App\Services\Stampanje;

use App\Entity\Faktura;
use App\Interfaces\FakturaStampanjeServiceInterface;
use PhpOffice\PhpWord\PhpWord;

class WordFakturaStampanje implements FakturaStampanjeServiceInterface  {


    public function stampajFakturu(Faktura $faktura):string {
        $imeFajla=$faktura->getBrojRacuna().'Faktura.docx';

        $dokumet = new PhpWord();

        $section = $dokumet->addSection();

        $section->addTitle('Faktura');
        $section->addText('Broj racuna: '.$faktura->getBrojRacuna());
        $section->addText('Datum izdavanja: '.$faktura->getDatumIzdavanja()->format('j. n. Y'));
        $section->addText('Organizacija: '.$faktura->getOrganizacija());
        $section->addText('Stavke fakture: ');

        foreach ($faktura->getStavke() as $stavka){
        $section->addText("Artikl:  ".$stavka->getNazivArtikla().' kolicina: '.$stavka->getKolicina());
        }

        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($dokumet, 'Word2007');
        $objWriter->save($imeFajla);
        return $imeFajla;

    }
}