<?php

namespace App\Services\Stampanje;

use App\Entity\Faktura;
use App\Interfaces\FakturaStampanjeServiceInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


class ExcelFakturaStampanje implements FakturaStampanjeServiceInterface {


    public function stampajFakturu(Faktura $faktura):string {
        $imeFajla=$faktura->getBrojRacuna().'Faktura.xlsx';

        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()
            ->setCreator("User")
            ->setLastModifiedBy("Ulogovani user")
            ->setTitle("Faktura")
            ->setSubject("Faktura")
            ->setCategory("fakture");

        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Faktura');

        $sheet->setCellValue('A2', 'Broj racuna');
        $sheet->setCellValue('B2', $faktura->getBrojRacuna());

        $sheet->setCellValue('A3', 'Datum izdavanja');
        $sheet->setCellValue('B3', $faktura->getDatumIzdavanja());

        $sheet->setCellValue('A4', 'Organizacija');
        $sheet->setCellValue('B4', $faktura->getOrganizacija());

        $sheet->setCellValue('A5', 'Stavke');
        $sheet->setCellValue('A6', 'Artikl');
        $sheet->setCellValue('B6', 'Kolicina');
        $sheet->setCellValue('C6', 'JM');
        $sheet->setCellValue('D6', 'Cena po jedinici');
        $sheet->setCellValue('E6', 'Iznos');

        $index=7;
        foreach ($faktura->getStavke() as $stavka){
            $sheet->setCellValue('A'.$index, $stavka->getProizvod()->getNazivProizvoda());
            $sheet->setCellValue('B'.$index, $stavka->getKolicina());
            $sheet->setCellValue('C'.$index, $stavka->getProizvod()->getJedinicaMere()->getOznaka());
            $sheet->setCellValue('D'.$index, $stavka->getProizvod()->getCenaPoJedinici());
            $sheet->setCellValue('E'.$index, $stavka->getKolicina()*$stavka->getProizvod()->getCenaPoJedinici());
            $index++;
        }
        $sheet->setCellValue('D'.$index, 'Ukupan iznos');
        $sheet->setCellValue('E'.$index, $faktura->getUkupanIznos());

        $writer = new Xlsx($spreadsheet);
        $writer->save($imeFajla);
        return $imeFajla;
    }
}