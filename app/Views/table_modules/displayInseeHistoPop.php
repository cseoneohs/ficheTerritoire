<?php

if (!function_exists('displayInseeHistoPop')) {

    function displayInseeHistoPop($data, $territoire, $fiche)
    {
        $annee = $_SESSION['perimetre']['anneeInseeHistoPop'];        
        $annee_5 = ($annee - 6);
        $annee_10 = ($annee_5 - 5);
        $anneeShurt = substr($annee, -2, 2);
        $annee_5Shurt = ltrim($annee_5, '20');
        $annee_5Shurt = (strlen($annee_5Shurt) == 2) ? $annee_5Shurt : '0' . $annee_5Shurt;
        $annee_10Shurt = ltrim($annee_10, '20');
        $annee_10Shurt = (strlen($annee_10Shurt) == 2) ? $annee_10Shurt : '0' . $annee_10Shurt;
        $decimal = ($fiche->ficheType == 'detail') ? 2 : 3;
        $titleTable = ($fiche->ficheType == 'detail') ? ' Evolution des ménages et desserrement ' : ' Evolution des ménages ';
        foreach ($data as $cle => $table) {
            if ($cle == 'part_1') {
                echo '<h4 class="text-center"> Evolution de la population</h4>';
                echo '<table class="ficheSynthese table table-bordered">';
                echo '<thead>';
                echo '<tr class="active">';

                echo '<th></th><th>Territoire</th>';
                echo '<th scope="col" colspan="3">Population</th>';
                echo '<th scope="col" colspan="5">' . $annee_10 . ' - ' . $annee_5 . '</th>';
                echo '<th scope="col" colspan="5">' . $annee_5 . ' - ' . $annee . '</th>';
                echo '<th scope="col" colspan="5">' . $annee_10 . ' - ' . $annee . '</th>';
                echo '</tr>';
                echo '<tr class="active">';
                echo '<th></th><th></th>';
                echo '<th>' . $annee_10 . '</th>'
                . '<th>' . $annee_5 . '</th>'
                . '<th>' . $annee . '</th>'
                . '<th>Taux variation annuel</th>'
                . '<th>Solde naturel</th>'
                . '<th>Taux variation naturel annuel</th>'
                . '<th>Solde migratoire</th>'
                . '<th>Taux variation migratoire annuel</th>'
                . '<th>Taux de variation annuel</th>'
                . '<th>Solde naturel</th>'
                . '<th>Taux variation naturel annuel</th>'
                . '<th>Solde migratoire</th>'
                . '<th>Taux variation migratoire annuel</th>'
                . '<th>Taux de variation annuel</th>'
                . '<th>Solde naturel</th>'
                . '<th>Taux variation naturel annuel</th>'
                . '<th>Solde migratoire</th>'
                . '<th>Taux variation migratoire annuel</th>'
                ;
                echo '</tr>';
                echo '</thead>';
                echo '<tbody>';
                displayEvolPopInsee($table, $territoire, $fiche, $decimal);
                echo '</tbody>';
                echo '</table>';
            } else {
                echo '<h4 class="text-center"> ' . $titleTable . $annee_10 . ' - ' . $annee_5 . ' - ' . $annee . '</h4>';
                echo '<table class="ficheSynthese table table-bordered">';
                echo '<thead>';
                echo '<tr class="active">';
                echo '<th></th><th>Territoire</th>';
                echo '<th>Ménages en ' . $annee_10 . '</th>'
                . '<th>Ménages en ' . $annee_5 . '</th>'
                . '<th>Ménages en ' . $annee . '</th>'
                . '<th>Pop ménages en ' . $annee_10 . '</th>'
                . '<th>Pop ménages en ' . $annee_5 . '</th>'
                . '<th>Pop ménages en ' . $annee . '</th>'
                . '<th>Taille moyenne ménages en ' . $annee_10 . '</th>'
                . '<th>Taille moyenne ménages en ' . $annee_5 . '</th>'
                . '<th>Taille moyenne ménages en ' . $annee . '</th>'
                . '<th>Evol annuelle ménages ' . $annee_10Shurt . '-' . $annee_5Shurt . '</th>'
                . '<th>Evol annuelle ménages ' . $annee_5Shurt . '-' . $anneeShurt . '</th>'
                . '<th>Evol annuelle ménages ' . $annee_10Shurt . '-' . $anneeShurt . '</th>'
                . '<th>Desserrement des ménages ' . $annee_10Shurt . '-' . $annee_5Shurt . '</th>'
                . '<th>Desserrement des ménages ' . $annee_5Shurt . '-' . $anneeShurt . '</th>'
                . '<th>Desserrement des ménages ' . $annee_10Shurt . '-' . $anneeShurt . '</th>'

                ;
                echo '</tr>';
                echo '</thead>';
                echo '<tbody>';
                displayEvolPopInsee($table, $territoire, $fiche, $decimal);
                echo '</tbody>';
                echo '</table>';
            }
        }
    }

}