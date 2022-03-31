<?php

namespace App\Services;

use App\Interfaces\FakturaStampanjeServiceInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


class FakturaStampanjeExcel implements FakturaStampanjeServiceInterface {


    public function stampajFakturu() {
        $spreadsheet = new Spreadsheet();
//        $spreadsheet->getProperties()->setCreator("TestCreator");
//        $spreadsheet->getProperties()->setTitle("Faktura test excel");
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Proba');

        $writer = new Xlsx($spreadsheet);
        return $writer->save('testFajlExcel.xlsx');

    }
}