<?php

namespace App\Libraries;

require_once './../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;

/**
 * Utilisation de PhpOffice\PhpSpreadsheet pour exporter vers excel (plusieurs onglets) à partir du jeu de données
 *
 * @author christian
 */
class ExportToExcelMulti
{

    protected $spreadsheet;
    protected $sheet;

    /**
     * Méthode principale
     * @param array $data
     * @param array $perimetre
     * @param string $title
     * @return string le nom du fichier sauvegardé
     */
    public function export($data, $perimetre, $title = '')
    {
        $this->spreadsheet = new Spreadsheet();
        $this->spreadsheet->getProperties()->setCreator('Eohs')
                ->setLastModifiedBy('')
                ->setTitle('Fiche territoire EOHS')
                ->setSubject('Fiche territoire EOHS, ' . $title)
                ->setDescription('Fiche territoire EOHS, ' . $title);
        // les styles par defaut
        $this->spreadsheet->getDefaultStyle()->applyFromArray(
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
        $this->spreadsheet->getActiveSheet()->getDefaultRowDimension()->setRowHeight(-1);

        //($_SESSION, $data, $perimetre);

        $i = 0;
        foreach ($data as $key => $dataset) {
            if ($key == 'var') {
                continue;
            }
            if ($key == 'fd_logemt') {
                $this->exportFdLogemt($dataset, $perimetre, $i);
            } else {
                $this->sheetname = substr($key, 0, 30);
                $this->sheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($this->spreadsheet, $this->sheetname);
                $this->spreadsheet->addSheet($this->sheet, $i++);
                $this->spreadsheet->setActiveSheetIndexByName($this->sheetname);
                $row = 1;
                if ($key == 'rpls') {
                    $this->exportRpls($dataset, $row, $perimetre);
                }
                if ($key == 'filosofi') {
                    $this->exportFilosofi($dataset, $row, $perimetre);
                }
                if ($key == 'sne') {
                    $this->exportSne($dataset, $row, $perimetre, $data['var']);
                }
                if ($key == 'sitadel_autorise' || $key == 'sitadel_commence') {
                    $this->exportSitadel0($dataset, $row, $perimetre, $key);
                }
                if (($key == 'sitadel_commence_neuf_ancien') || ($key == 'sitadel_commence_utilisation')) {
                    $this->exportSitadel1($dataset, $row, $perimetre, $key);
                }
                if ($key == 'insee_histo_pop') {
                    $this->exportInseeHistoPop($dataset, $row, $perimetre);
                }
                if ($key == "artificialisation") {
                    $this->exportArtificialisation($dataset, $row, $perimetre);
                }
                if ($key == "lovac") {
                    $this->exportLovac($dataset, $row, $perimetre);
                }
                $this->spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(40);
                $this->spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(20);
            }
        }
        $this->sheetIndex = $this->spreadsheet->getIndex($this->spreadsheet->getSheetByName('Worksheet'));
        $this->spreadsheet->removeSheetByIndex($this->sheetIndex);
        $this->spreadsheet->setActiveSheetIndex(0);
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($this->spreadsheet, 'Xlsx');
        $alea = round(microtime(true));
        $writer->save(WRITEPATH . '/download/export' . $alea . '.xlsx');
        return 'export' . $alea;
    }

    private function exportFdLogemt($dataset, $perimetre, $i)
    {
        foreach ($dataset as $k => $val_0) {
            $row = 1;
            $this->sheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($this->spreadsheet, substr($k, 0, 30));
            $this->spreadsheet->addSheet($this->sheet, $i);
            $this->spreadsheet->setActiveSheetIndex($i++);
            $this->setSheetHeader($k, $_SESSION['perimetre']['annee'], $row++);
            foreach ($val_0 as $sstitle => $val_1) {
                $col = 1;
                $this->spreadsheet->getActiveSheet()->getStyle('A' . $row)->getFont()->setBold('bold');
                $this->sheet = $this->spreadsheet->getActiveSheet()->setCellValue([1, $row++], $sstitle);
                $col = 1;
                $this->sheet = $this->spreadsheet->getActiveSheet()->setCellValue([$col++, $row], "");
                $this->sheet = $this->spreadsheet->getActiveSheet()->setCellValue([$col++, $row], "");
                foreach ($val_1 as $tab_header => $val_2) {
                    $this->sheet = $this->spreadsheet->getActiveSheet()->setCellValue([$col++, $row], $tab_header);
                    $this->sheet = $this->spreadsheet->getActiveSheet()->setCellValue([$col++, $row], "");
                }
                $colheader = 3;
                foreach ($val_1 as $tab_2 => $val_2) {
                    $this->sheet = $this->spreadsheet->getActiveSheet()->mergeCellsByColumnAndRow($colheader++, $row, $colheader++, $row);
                }
                $row++;
                foreach ($val_2 as $tab_3 => $val_3) {
                    $col = 1;
                    $this->sheet = $this->spreadsheet->getActiveSheet()->setCellValue([$col++, $row], "");
                    $this->sheet = $this->spreadsheet->getActiveSheet()->setCellValue([$col++, $row], "Territoire");
                    $n_var = count($val_1);
                    for ($j = 1; $j <= ($n_var); $j++) {
                        $this->sheet = $this->spreadsheet->getActiveSheet()->setCellValue([$col++, $row], "Nb");
                        $this->sheet = $this->spreadsheet->getActiveSheet()->setCellValue([$col++, $row], "%");
                    }
                }
                $row++;
                $lrow = $row;

                foreach ($val_2 as $tab_3 => $val_3) {
                    $col = 1;
                    $libgeo = $this->getLibgeo($tab_3, $perimetre);
                    $this->sheet = $this->spreadsheet->getActiveSheet()->setCellValue([$col++, $row], $tab_3);
                    $this->sheet = $this->spreadsheet->getActiveSheet()->setCellValue([$col++, $row], $libgeo);
                    $row++;
                }

                $territoire = array();
                foreach ($perimetre['codeEtude'] as $code) {
                    $territoire[] = $code;
                }
                if (isset($perimetre['perimComp']) && !empty($perimetre['perimComp'])) {
                    //ksort($perimetre['perimComp']);
                    foreach ($perimetre['perimComp'] as $value) {
                        if ($value != 'secteur') {
                            $territoire[] = $value;
                        }
                    }
                    if (in_array('secteur', $perimetre['perimComp'])) {
                        $territoire = classerTerritoire($fiche, $territoire);
                    }
                }
                $row = $lrow;
                //var_dump($territoire, $val_1);exit;
                foreach ($territoire as $terr) {
                    $col = 3;
                    foreach ($val_1 as $tab_2 => $val_2) {
                        $this->sheet = $this->spreadsheet->getActiveSheet()->setCellValue([$col++, $row], $val_2[$terr]['nb']);
                        $this->sheet = $this->spreadsheet->getActiveSheet()->setCellValue([$col++, $row], $val_2[$terr]['pc']);
                    }
                    $row++;
                }
                $row++;
            }
        }
    }

    private function exportRpls($dataset, $row, $perimetre)
    {
        foreach ($dataset as $values) {
            foreach ($values as $year => $vals) {
                $this->setSheetHeader('RPLS', $year, $row++);
                $this->spreadsheet->getActiveSheet()->setCellValue([1, $row++], "Nombre de logements");
                $this->spreadsheet->getActiveSheet()->getStyle('A2')->getFont()->setBold('bold');
                $this->setColHeader($vals, $row);
                $row++;
                $row = $this->setSheetValues($vals, $row++, $perimetre);
                $row++;
            }
        }
        $this->spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(40);
        $this->spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(20);
    }

    private function exportFilosofi($dataset, $row, $perimetre)
    {
        foreach ($dataset as $values) {
            foreach ($values as $year => $vals) {
                $this->setSheetHeader('FiLoSoFi', $year, $row++);
                $this->spreadsheet->getActiveSheet()->setCellValue([1, $row++], "Niveau de vie");
                $this->spreadsheet->getActiveSheet()->getStyle('A2')->getFont()->setBold('bold');
                $this->setColHeader($vals, $row);
                $row++;
                $row = $this->setSheetValues($vals, $row++, $perimetre);
                $row++;
            }
        }
    }

    private function exportSne($dataset, $row, $perimetre, $vars)
    {
        foreach ($dataset as $values) {
            foreach ($values as $year => $val_1) {
                $this->setSheetHeader('SNE au 31/12', $year, $row++);
                foreach ($val_1 as $sstitle => $vals) {
                    $this->setTitleHeader($row++, $sstitle);
                    $newline = $seconline = true;
                    $row++;
                    if ($newline) {
                        $col = 1;
                        $this->spreadsheet->getActiveSheet()->setCellValue([$col++, $row], "");
                        $this->spreadsheet->getActiveSheet()->setCellValue([$col++, $row], "Territoire");
                        foreach ($vals[key($vals)] as $key => $value) {
                            $headercol = strpos($key, "_pc") ? '' : array_search($key, $vars);
                            $this->spreadsheet->getActiveSheet()->setCellValue([$col++, $row], $headercol);
                        }
                        $colheader = 3;
                        foreach ($vals[key($vals)] as $key) {
                            $this->spreadsheet->getActiveSheet()->mergeCellsByColumnAndRow($colheader++, $row, $colheader++, $row);
                        }
                        $newline = false;
                        $row++;
                    }
                    if ($seconline) {
                        $col = 1;
                        $this->spreadsheet->getActiveSheet()->setCellValue([$col++, $row], "");
                        $this->spreadsheet->getActiveSheet()->setCellValue([$col++, $row], "");
                        foreach ($vals[key($vals)] as $key => $values) {
                            $headercol = strpos($key, "_pc") ? '%' : 'Nb';
                            $this->spreadsheet->getActiveSheet()->setCellValue([$col++, $row], $headercol);
                        }
                        $seconline = false;
                        $row++;
                    }
                    $col = 1;
                    $row++;
                    $row = $this->setSheetValues($vals, $row++, $perimetre);
                    $row++;
                }
                //saut de ligne entre 2 variables
                $row++;
            }
        }
    }

    private function exportSitadel0($dataset, $row, $perimetre, $key)
    {
        $contexte = ($key == 'sitadel_commence') ? 'commencés' : 'autorisés';
        $ssTitle = "Nombre de logements " . $contexte . " en date réelle en ";
        $annee = ($key == 'sitadel_commence') ? $perimetre['anneeSitadel'] : $perimetre['anneeSitadelAutorise'];
        $title = "Fiche detail :: Sit@del " . $contexte;
        $this->setSheetHeader("Sit@del " . $contexte, $annee, $row++);
        foreach ($dataset as $values) {
            foreach ($values as $annee => $val_1) {
                $this->setTitleHeader($row++, $ssTitle . ' - ' . $annee);
                $this->setColHeader($val_1, $row);
                $row++;
                $row = $this->setSheetValues($val_1, $row++, $perimetre);
                $row++;
            }
            $row++;
        }
    }

    private function exportSitadel1($dataset, $row, $perimetre, $key)
    {
        $title = ($key == 'sitadel_commence_neuf_ancien') ? 'Sit@del Logements commencés neuf/ancien ordinaire' : 'Sit@del Logements commencés utilisation ordinaire';
        $ssTitle = ($key == 'sitadel_commence_neuf_ancien') ? 'Logements commencés neuf/ancien ordinaire' : 'Logements commencés utilisation ordinaire';
        $annee = ($key == 'sitadel_commence_neuf_ancien') ? $perimetre['anneeSitadelNeufAncien'] : $perimetre['anneeSitadelUtilisation'];
        $this->setSheetHeader($title, $annee, $row++);
        foreach ($dataset as $values) {
            foreach ($values as $annee => $val_1) {
                $this->setTitleHeader($row++, $ssTitle . ' - ' . $annee);
                $this->setColHeader($val_1, $row);
                $row++;
                $row = $this->setSheetValues($val_1, $row++, $perimetre);
            }
        }
    }

    private function exportInseeHistoPop($dataset, $row, $perimetre)
    {
        $annee = $_SESSION['perimetre']['anneeInseeHistoPop'];
        $annee_5 = $annee - 6;
        $annee_10 = $annee_5 - 5;
        $tab_title = "Evolution de la population " . $annee_10 . " - " . $annee_5 . " - " . $annee;
        $tab_title2 = "Evolution des ménages et desserrement " . $annee_10 . " - " . $annee_5 . " - " . $annee;
        $this->setSheetHeader($tab_title, '', $row++);
        foreach ($dataset as $values) {
            foreach ($values as $val_1) {
                $sstitle = $row > 2 ? $tab_title2 : $tab_title;
                $this->setTitleHeader($row++, $sstitle);
                $newline = true;
                foreach ($val_1 as $tab_2 => $vals) {
                    if ($newline) {
                        $col = 3;
                        foreach ($vals as $tab_3 => $val_3) {
                            $headercol = $this->getHeaderColHistoPop($tab_3, $annee, $annee_5, $annee_10);
                            $this->spreadsheet->getActiveSheet()->setCellValue([$col++, $row], $headercol);
                        }
                        $newline = false;
                        $row++;
                    }
                }
                $row++;
                $row = $this->setSheetValues($val_1, $row++, $perimetre);
                $row++;
            }
        }
    }

    private function exportArtificialisation($dataset, $row, $perimetre)
    {
        foreach ($dataset as $values) {
            foreach ($values as $year => $val_1) {
                $this->setSheetHeader('Artificialisation', $year, $row++);
                foreach ($val_1 as $sstitle => $vals) {
                    $this->setTitleHeader($row++, $sstitle);
                    $this->setColHeader($vals, $row);
                    $row++;
                    $row = $this->setSheetValues($vals, $row++, $perimetre);
                    $row++;
                }
                $row++;
            }
        }
    }

    private function exportLovac($dataset, $row, $perimetre)
    {
        foreach ($dataset as $values) {
            foreach ($values as $year => $vals) {
                $this->setSheetHeader('LOVAC', $year, $row++);
                $this->setColHeader($vals, $row);
                $row++;
                $this->setSheetValues($vals, $row, $perimetre);
            }
        }
    }

    /**
     *
     * @param string $param le code géographique
     * @param array $perimetre le tableau contenat le périmètre
     * @return string
     */
    private function getLibgeo($param, $perimetre)
    {
        $libgeo = array_search($param, $perimetre['labelEtude']);
        if ($param == 'epci') {
            $libgeo = $_SESSION['perimetre']['epciLib'];
        } elseif (strstr($param, 'departement')) {
            $codeDept = ltrim(strstr($param, 'departement'), 'departement');
            $libgeo = $perimetre['deptLib'][$codeDept];
        } elseif (strstr($param, 'region')) {
            $code = ltrim(strstr($param, 'region'), 'region');
            $libgeo = $perimetre['regionLib'][$code];
        } elseif ($param == 'france') {
            $libgeo = "France";
        }
        return $libgeo;
    }

    /**
     * Ecriture de l'entête de la feuille
     * @param strting $source nom de la source de données
     * @param int $param l'année de la source de données
     * @param int $row le numéro de ligne à écrire
     */
    private function setSheetHeader($source, $param, $row)
    {
        if ($param == '') {
            $title = 'Fiche detail :: ' . $source;
        } else {
            $title = 'Fiche detail :: ' . $source . ' :: ' . $param;
        }
        $this->spreadsheet->getActiveSheet()->setCellValue([1, $row++], $title);
        $this->spreadsheet->getActiveSheet()->getStyle('A1')->getFont()->setBold('bold');
        $this->spreadsheet->getActiveSheet()->getRowDimension('1')->setRowHeight(80);
    }

    /**
     * Ecriture des titres de sous rubrique
     * @param int $row
     * @param string $title
     */
    private function setTitleHeader($row, $title = null)
    {
        $this->spreadsheet->getActiveSheet()->setCellValue([1, $row++], "");
        $line = $row;
        $this->spreadsheet->getActiveSheet()->setCellValue([1, $row++], $title);
        $this->spreadsheet->getActiveSheet()->getStyle('A' . $line)->getFont()->setBold('bold');
    }

    /**
     * Ecriture des entêtes de colonnes
     * @param array $param le tableau contenat les données
     * @param int $row
     */
    private function setColHeader($param, $row)
    {
        foreach ($param as $val) {
            $col = 3;
            for ($i = 0, $size = count($val); $i < $size; ++$i) {
                $this->spreadsheet->getActiveSheet()->setCellValue([$col++, $row], key($val));
                next($val);
            }
        }
    }

    /**
     * Ecriture des données
     * @param array $param le tableau contenat les données
     * @param int $row
     * @param array $perimetre
     */
    private function setSheetValues($param, $row, $perimetre)
    {
        foreach ($param as $codeGeo => $vals) {
            $col = 1;
            $this->spreadsheet->getActiveSheet()->setCellValue([$col++, $row], $codeGeo);
            $libgeo = $this->getLibgeo($codeGeo, $perimetre);
            $this->spreadsheet->getActiveSheet()->setCellValue([$col++, $row], $libgeo);
            foreach ($vals as $val) {
                $this->spreadsheet->getActiveSheet()->setCellValue([$col++, $row], $val);
            }
            $row++;
        }
        return $row;
    }

    /**
     * Défint les entêtes de colonnes pour Evolution de la population
     * @param string $param
     * @param string $annee
     * @param string $annee_5
     * @param string $annee_10
     * @return string
     */
    private function getHeaderColHistoPop($param, $annee, $annee_5, $annee_10)
    {
        switch ($param) {
            case "aa_population_n_10":
                $headercol = 'Population en' . " " . $annee_10;
                break;
            case "ab_population_n_5":
                $headercol = 'Population en' . " " . $annee_5;
                break;
            case "ac_population_annee":
                $headercol = 'Population en' . " " . $annee;
                break;
            //
            case "baa__2_evol_annuel_pop_n_5":
                $headercol = 'Taux de variation annuel' . " " . $annee_5 . " - " . $annee;
                break;
            case "bb_solde_naturel_n_5":
                $headercol = 'Solde naturel' . " " . $annee_5 . " - " . $annee;
                break;
            case "bc__2_tx_var_naturel_an_n_5":
                $headercol = 'Taux variation naturel annuel' . " " . $annee_5 . " - " . $annee;
                break;
            case "bd_solde_migratoire_n_5":
                $headercol = 'Solde migratoire' . " " . $annee_5 . " - " . $annee;
                break;
            case "be__2_tx_var_migratoire_an_n_5":
                $headercol = 'Taux variation migratoire annuel' . " " . $annee_5 . " - " . $annee;
                break;
            //
            case "caa__2_evol_annuel_pop_n_10":
                $headercol = 'Taux de variation annuel' . " " . $annee_10 . " - " . $annee;
                break;
            case "cb_solde_naturel_n_10":
                $headercol = 'Solde naturel' . " " . $annee_10 . " - " . $annee;
                break;
            case "cc__2_tx_var_naturel_an_n_10":
                $headercol = 'Taux variation naturel annuel' . " " . $annee_10 . " - " . $annee;
                break;
            case "cd_solde_migratoire_n_10":
                $headercol = 'Solde migratoire' . " " . $annee_10 . " - " . $annee;
                break;
            case "ce__2_tx_var_migratoire_an_n_10":
                $headercol = 'Taux variation migratoire annuel' . " " . $annee_10 . " - " . $annee;
                break;
            //
            case "aca__2_evol_annuel_pop_n_5_5":
                $headercol = 'Taux de variation annuel' . " " . $annee_10 . " - " . $annee_5;
                break;
            case "ad_solde_naturel_n_5_5":
                $headercol = 'Solde naturel' . " " . $annee_10 . " - " . $annee_5;
                break;
            case "adb__2_tx_var_naturel_an_n_5_5":
                $headercol = 'Taux variation naturel annuel' . " " . $annee_10 . " - " . $annee_5;
                break;
            case "ae_solde_migratoire_n_5_5":
                $headercol = 'Solde migratoire' . " " . $annee_10 . " - " . $annee_5;
                break;
            case "af__2_tx_var_migratoire_an_n_5_5":
                $headercol = 'Taux variation migratoire annuel' . " " . $annee_10 . " - " . $annee_5;
                break;
            //
            case "ha__menage_n_10":
                $headercol = 'Ménages en' . " " . $annee_10;
                break;
            case "hb__menage_n_5":
                $headercol = 'Ménages en' . " " . $annee_5;
                break;
            case "hc__menage_annee":
                $headercol = 'Ménages en' . " " . $annee;
                break;
            case "ja_pop_menage_n_10":
                $headercol = 'Pop ménages en' . " " . $annee_10;
                break;
            case "jb_pop_menage_n_5":
                $headercol = 'Pop ménages en' . " " . $annee_5;
                break;
            case "jc_pop_menage_annee":
                $headercol = 'Pop ménages en' . " " . $annee;
                break;
            case "la__2_taille_moy_menage_n_10":
                $headercol = 'Taille moyenne ménages en' . " " . $annee_10;
                break;
            case "lb__2_taille_moy_menage_n_5":
                $headercol = 'Taille moyenne ménages en' . " " . $annee_5;
                break;
            case "lc__2_taille_moy_menage_annee":
                $headercol = 'Taille moyenne ménages en' . " " . $annee;
                break;
            case "na__2_evol_annuel_menage":
                $headercol = 'Evol annuelle ménages' . " " . $annee_10 . " - " . $annee_5;
                break;
            case "nb__2_evol_annuel_menage":
                $headercol = 'Evol annuelle ménages' . " " . $annee_5 . " - " . $annee;
                break;
            case "nc__2_evol_annuel_menage":
                $headercol = 'Evol annuelle ménages' . " " . $annee_10 . " - " . $annee;
                break;
            case "ob__2_desserement_menage":
                $headercol = 'Desserrement des ménages' . " " . $annee_5 . " - " . $annee;
                break;
            case "oc__2_desserement_menage_n_10":
                $headercol = 'Desserrement des ménages' . " " . $annee_10 . " - " . $annee;
                break;
            case "oa__2_desserement_menage_n_5":
                $headercol = 'Desserrement des ménages' . " " . $annee_10 . " - " . $annee_5;
                break;
            default:
                $headercol = '';
                break;
        }
        return $headercol;
    }
}
