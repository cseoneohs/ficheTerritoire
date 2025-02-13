<?php

if (!function_exists('functionSetLibelTerritoire')) {

    /**
     * Retourne le nom du territoire
     * @param string $terr le type de territoire
     * @param object $fiche la fiche
     * @param boolean $toDisplay
     * @param boolean | string $code
     * @return string le libellé du territoire
     */
    function setLibelTerritoire($terr, $fiche, &$toDisplay, &$code = null) {
        //si le territoire est une commune
        if (preg_match('/\A[0-9]{5}\z/', $terr)) {
            $isCommune = $terr;
        } else {
            $isCommune = null;
        }
        $code = isset($isCommune) ? $isCommune : '';
        //si le territoire est un secteur
        if (strpos($terr, '_')) {
            $libel = strstr($terr, '_', true);
            $listCommuneSecteur = $fiche->perimetre->perimCompSecteur[$libel];
            $toDisplay = (strstr($listCommuneSecteur, $isCommune)) ? true : false;
        } elseif (strstr($terr, 'region')) {
            $codeReg = ltrim(strstr($terr, 'region'), 'region');
            $libel = $fiche->perimetre['regionLib'][$codeReg];
        } elseif (strstr($terr, 'departement')) {
            $codeDept = ltrim(strstr($terr, 'departement'), 'departement');
            $libel = $fiche->perimetre['deptLib'][$codeDept];
        } elseif ($terr == 'epci') {
            $libel = $fiche->perimetre['epciLib'];
        } elseif (strstr($terr, 'secteur')) {
            $codeSect = ltrim(strstr($terr, 'secteur'), 'secteur');
            $libel = $fiche->perimetre['secteurLib'][$codeSect]['libel'];
        } else {
            $libel = array_search($terr, $fiche->perimetre['labelEtude']);
        }
        return $libel;
    }

}