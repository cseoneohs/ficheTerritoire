<?php

/**
 * Fonction de classement de territoire
 */

if (!function_exists('classerTerritoire')) {

    /**
     * Retourne un tableau de territoire
     * @param object $fiche
     * @param array $territoire
     * @return array
     */
    function classerTerritoire($fiche, $territoire)
    {
        $newTerritoire = array();
        if (isset($fiche->perimetre['perimCompSecteur'])) {
            foreach ($fiche->perimetre['perimCompSecteur'] as $key => $value) {
                $terr = explode(',', $value);
                foreach ($terr as $key2 => $value2) {
                    if (!(in_array($value2, $territoire))) {
                        unset($terr[$key2]);
                    }
                }
                $newTerritoire = array_merge($newTerritoire, $terr);
                $newTerritoire = array_merge($newTerritoire, array($key . '_secteur'));
            }
        }
        foreach ($territoire as $value) {
            if (!in_array($value, $newTerritoire)) {
                $newTerritoire = array_merge($newTerritoire, array($value));
            }
        }
        return $newTerritoire;
    }
}
