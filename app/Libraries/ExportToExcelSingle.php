<?php

namespace App\Libraries;

require_once './../vendor/autoload.php';


/**
 * Exporte des données vers EXCEL
 */
class ExportToExcelSingle
{

    /**
     * Exporte des données HTML vers EXCEL (une seule feuille)
     * @param string $html le contenu a exporter
     * @param string $title le titre du document
     * @return string
     */
    public function export($html, $title)
    {
        $html = '<!DOCTYPE html><html lang="fr"><head></head><body>' . $html . '</body></html>';

        $tmpfile = './../writable/tmp/' . time() . '.html';
        file_put_contents($tmpfile, $html);

        // Read the contents of the file into PHPExcel Reader class
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Html();
        $objPHPExcel = $reader->load($tmpfile);

        $sheet = $objPHPExcel->getActiveSheet();
        $sheet->setTitle($title);
        $objPHPExcel->getProperties()->setCreator('Eohs')
                ->setLastModifiedBy('')
                ->setTitle('Fiche territoire EOHS')
                ->setSubject('Fiche territoire EOHS, ' . $title)
                ->setDescription('Fiche territoire EOHS, ' . $title);

        // les styles par defaut
        $sheet->getParent()->getDefaultStyle()->applyFromArray(
                array(
                    'font' => array(
                        'name' => 'Arial',
                        'size' => 11,
                        'bold' => false),
                    'alignment' => array(
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                        'rotation' => 0,
                        'wrap' => true
                    ),
                    'numberFormat' => array('formatCode' => \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER)
                )
        );
        //Hauteur de ligne automatique (dans Excel seulement)
        $objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(-1);
        $sheet->getRowDimension('1')->setRowHeight(80);
        $styleBorderArray = array(
            'borders' => array(
                'allBorders' => array(
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ),
            )
        );
        $maxCell = $sheet->getHighestRowAndColumn();        
        $sheet->getStyle('A1:' . $maxCell['column'] . $maxCell['row'])->applyFromArray($styleBorderArray);
        $sheet->getStyle('A1:A' . $sheet->getHighestRow())->getAlignment()->setWrapText(true);
        $sheet->getStyle('A1:A' . $sheet->getHighestRow())->getAlignment()->setShrinkToFit(true);
        $sheet->getStyle('B1:B' . $sheet->getHighestRow())->getAlignment()->setWrapText(true);
        $sheet->getStyle('B1:B' . $sheet->getHighestRow())->getAlignment()->setShrinkToFit(true);
        $sheet->getStyle('A1:A' . $sheet->getHighestRow())->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $sheet->getStyle('B1:B' . $sheet->getHighestRow())->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $sheet->getColumnDimension('A')->setWidth(40);
        $sheet->getColumnDimension('B')->setWidth(20);
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($objPHPExcel, "Xlsx");
        $alea = round(microtime(true));
        $writer->save(WRITEPATH . '/download/' . $alea . '.xlsx');
        // Delete temporary file
        unlink($tmpfile);
        return $alea;
    }
}
