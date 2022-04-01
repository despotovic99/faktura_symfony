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
        $writer = new Xlsx($spreadsheet);
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

        $index=7;
        foreach ($faktura->getStavke() as $stavka){
            $sheet->setCellValue('A'.$index, $stavka->getNazivArtikla());
            $sheet->setCellValue('B'.$index, $stavka->getKolicina());
            $index++;
        }

        $writer = new Xlsx($spreadsheet);
        $writer->save($imeFajla);
        return $imeFajla;
    }
}