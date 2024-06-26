<?php

namespace App\Models;

use App\Models\FicheModel;

class SneModel extends FicheModel
{
    public $perimetre = null;
    private $andGeo = '';
    private $dataSource = 'data_sne';
    private $geoEtude = null;
    public $data = array();
    public $ficheType = null;
    private $annee = null;

    public function __construct()
    {
        parent::__construct();
        $this->tRubrique = array(0 => array(), 1 => array(), 2 => array(), 3 => array(), 4 => array(), 5 => array(), 6 => array(), 7 => array(), 8 => array(), 9 => array());
    }

    /**
     * requete dans les tables temporaires et construction du jeu de donnees
     * @param string $where
     * @return array
     */
    public function process()
    {
        $this->geoEtude = $this->perimetre['codeEtude'];
        $this->geoData = ($_SESSION['territoireEtude'] == 'commune') ? $this->perimetre['codeEtude'] : $this->perimetre['codeGeo'];
        reset($this->geoEtude);
        $data = array();
        $this->annee = $this->perimetre['anneeSne'];
        //pour chaque commune etudie
        foreach ($this->geoEtude as $key => $value) {
            $insee = str_replace(',', "','", $this->geoData[$key]);
            $this->andGeo = " AND code_insee IN ('" . $insee . "') ";
            $data = $this->arrayMergeRecursiveMy($data, $this->getData($value));
        }
        //ce a quoi on compare
        if (isset($this->perimetre['perimComp']) && is_array($this->perimetre['perimComp'])) {
            //pour chaque territoire auquel on peut comparer
            foreach ($this->perimetre['perimComp'] as $value) {
                $this->andGeo = $this->getGeoComp($value);
                $data = $this->arrayMergeRecursiveMy($data, $this->getData($value));
            }
        }
        return $data;
    }

    /**
     * requetes des donnees
     * @param  string $geo
     * @return array
     */
    private function getData($geo)
    {
        $fiche = ($this->ficheType == 'detail') ? 'sne_detail' : 'sne_synthese';
        $from = " FROM " . $this->dataSource . " WHERE annee = " . $this->annee . $this->andGeo;
        $data = array();
        //Pression de la demande
        $sql0 = "SELECT (SUM(hors_mutation)+SUM(mutation))/NULLIF(SUM(attributions),0) as pression " . $from;
        $query0 = $this->db->query($sql0);
        $result0 = $query0->getResultArray();
        $data[$fiche][$this->annee]['Pression de la demande'][$geo] = $result0[0];
        if (empty($this->tRubrique[0])) {
            $this->tRubrique[0]['var_croise_lib'] = 'Pression de la demande';
            $this->tRubrique[0]['var_croise_ancre'] = 'pression_demande';
        }
        //Ancienneté de la demande
        $sql1 = "SELECT SUM(1_an) as 1_an, 100*SUM(1_an)/(SUM(1_an) + SUM(1_2_ans) + SUM(2_3_ans) + SUM(3_4_ans) + SUM(4_5_ans) + SUM(5_10_ans) + SUM(10_ans_ou_plus)) as 1_an_pc,
        SUM(1_2_ans) as 1_2_ans, 100*SUM(1_2_ans)/(SUM(1_an) + SUM(1_2_ans) + SUM(2_3_ans) + SUM(3_4_ans) + SUM(4_5_ans) + SUM(5_10_ans) + SUM(10_ans_ou_plus)) as 1_2_ans_pc,
        SUM(2_3_ans) as 2_3_ans, 100*SUM(2_3_ans)/(SUM(1_an) + SUM(1_2_ans) + SUM(2_3_ans) + SUM(3_4_ans) + SUM(4_5_ans) + SUM(5_10_ans) + SUM(10_ans_ou_plus)) as 2_3_ans_pc,
        SUM(3_4_ans) as 3_4_ans, 100*SUM(3_4_ans)/(SUM(1_an) + SUM(1_2_ans) + SUM(2_3_ans) + SUM(3_4_ans) + SUM(4_5_ans) + SUM(5_10_ans) + SUM(10_ans_ou_plus)) as 3_4_ans_pc,
        SUM(4_5_ans) as 4_5_ans, 100*SUM(4_5_ans)/(SUM(1_an) + SUM(1_2_ans) + SUM(2_3_ans) + SUM(3_4_ans) + SUM(4_5_ans) + SUM(5_10_ans) + SUM(10_ans_ou_plus)) as 4_5_ans_pc,
        SUM(5_10_ans) as 5_10_ans, 100*SUM(5_10_ans)/(SUM(1_an) + SUM(1_2_ans) + SUM(2_3_ans) + SUM(3_4_ans) + SUM(4_5_ans) + SUM(5_10_ans) + SUM(10_ans_ou_plus)) as 5_10_ans_pc,
        SUM(10_ans_ou_plus) as 10_ans_ou_plus, 100*SUM(10_ans_ou_plus)/(SUM(1_an) + SUM(1_2_ans) + SUM(2_3_ans) + SUM(3_4_ans) + SUM(4_5_ans) + SUM(5_10_ans) + SUM(10_ans_ou_plus)) as 10_ans_ou_plus_pc,
        (SUM(1_an) + SUM(1_2_ans) + SUM(2_3_ans) + SUM(3_4_ans) + SUM(4_5_ans) + SUM(5_10_ans) + SUM(10_ans_ou_plus)) as total, 100 as total_pc " . $from;
        $query1 = $this->db->query($sql1);
        $result1 = $query1->getResultArray();
        $data[$fiche][$this->annee]['Ancienneté de la demande'][$geo] = $result1[0];
        if (empty($this->tRubrique[1])) {
            $this->tRubrique[1]['var_croise_lib'] = 'Ancienneté de la demande';
            $this->tRubrique[1]['var_croise_ancre'] = 'anciennete_demande';
        }
        $sql2 = "SELECT (SUM(chambre)+SUM(t1)) as t1, 100*(SUM(chambre)+SUM(t1))/(SUM(chambre)+SUM(t1)+SUM(t2)+SUM(t3) + SUM(t4) + SUM(t5) + SUM(t6_ou_plus)) as t1_pc, SUM(t2) as t2, 100*SUM(t2)/(SUM(chambre)+SUM(t1)+SUM(t2)+SUM(t3) + SUM(t4) + SUM(t5) + SUM(t6_ou_plus)) as t2_pc, SUM(t3) as t3, 100*SUM(t3)/(SUM(chambre)+SUM(t1)+SUM(t2)+SUM(t3) + SUM(t4) + SUM(t5) + SUM(t6_ou_plus)) as t3_pc, SUM(t4) as t4, 100*SUM(t4)/(SUM(chambre)+SUM(t1)+SUM(t2)+SUM(t3) + SUM(t4) + SUM(t5) + SUM(t6_ou_plus)) as t4_pc, (SUM(t5)+SUM(t6_ou_plus)) as t5, 100*(SUM(t5)+SUM(t6_ou_plus))/(SUM(chambre)+SUM(t1)+SUM(t2)+SUM(t3) + SUM(t4) + SUM(t5) + SUM(t6_ou_plus)) as t5_pc, (SUM(chambre)+SUM(t1)+SUM(t2)+SUM(t3) + SUM(t4) + SUM(t5) + SUM(t6_ou_plus)) as total, 100*((SUM(chambre)+SUM(t1)+SUM(t2)+SUM(t3) + SUM(t4) + SUM(t5) + SUM(t6_ou_plus))/(SUM(chambre)+SUM(t1)+SUM(t2)+SUM(t3) + SUM(t4) + SUM(t5) + SUM(t6_ou_plus))) as total_pc " . $from;
        $query2 = $this->db->query($sql2);
        $result2 = $query2->getResultArray();
        $data[$fiche][$this->annee]['Type de logement demandé/attribué'][$geo] = $result2[0];
        if (empty($this->tRubrique[2])) {
            $this->tRubrique[2]['var_croise_lib'] = 'Type de logement demandé/attribué';
            $this->tRubrique[2]['var_croise_ancre'] = 'logement_attribue';
        }
        //Age du titulaire
        $sql3Total = "SELECT (SUM(moins_20_ans)+SUM(20_24_ans)+SUM(25_29_ans)+SUM(30_34_ans)+SUM(35_39_ans)+SUM(40_44_ans)+SUM(45_49_ans)+SUM(50_54_ans)+SUM(55_59_ans)+SUM(60_64_ans)+SUM(65_69_ans)+SUM(70_74_ans)+SUM(75_ans_et_plus)) as total " . $from;
        $query3Total = $this->db->query($sql3Total);
        $result3Total = $query3Total->getResultArray();
        $total3 = $result3Total[0]['total'];

        if ($total3 > 0 && !is_null($total3)) {
            $sql3 = "SELECT (SUM(moins_20_ans)+SUM(20_24_ans)+SUM(25_29_ans)) as m30ans, 100*(SUM(moins_20_ans)+SUM(20_24_ans)+SUM(25_29_ans))/" . $total3 . " as m30ans_pc, (SUM(30_34_ans)+SUM(35_39_ans)) as 30_39ans, 100*(SUM(30_34_ans)+SUM(35_39_ans))/" . $total3 . " as 30_39ans_pc, (SUM(	40_44_ans)+SUM(45_49_ans)) as 40_49ans, 100*(SUM(40_44_ans)+SUM(45_49_ans))/" . $total3 . " as 40_49ans_pc, (SUM(50_54_ans)+SUM(55_59_ans)+SUM(60_64_ans)) as 50_65ans, 100*(SUM(50_54_ans)+SUM(55_59_ans)+SUM(60_64_ans))/" . $total3 . " as 50_65ans_pc, (SUM(65_69_ans)+SUM(70_74_ans)+SUM(75_ans_et_plus)) as 65ansp, 100*(SUM(65_69_ans)+SUM(70_74_ans)+SUM(75_ans_et_plus))/" . $total3 . " as 65ansp_pc, " . $total3 . " as total, 100*$total3/" . $total3 . " as total_pc " . $from;
            $query3 = $this->db->query($sql3);
            $result3 = $query3->getResultArray();
            $data[$fiche][$this->annee]['Age du titulaire'][$geo] = $result3[0];
            if (empty($this->tRubrique[3])) {
                $this->tRubrique[3]['var_croise_lib'] = 'Age du titulaire';
                $this->tRubrique[3]['var_croise_ancre'] = 'age_titulaire';
            }
        }
        //Taille du ménage
        $sql4Total = "SELECT (SUM(1_pers)+SUM(2_pers)+SUM(3_pers)+SUM(4_pers)+SUM(5_pers)+SUM(6_pers)+SUM(7_pers)+SUM(	supegal_8_pers)) as total " . $from;
        $query4Total = $this->db->query($sql4Total);
        $result4Total = $query4Total->getResultArray();
        $total4 = $result4Total[0]['total'];
        if ($total4 > 0 && !is_null($total4)) {
            $sql4 = "SELECT SUM(1_pers) as 1_pers, 100*SUM(1_pers)/" . $total4 . " as 1_pers_pc, SUM(2_pers) as 2_pers, 100*SUM(2_pers)/" . $total4 . " as 2_pers_pc, SUM(3_pers) as 3_pers, 100*SUM(3_pers)/" . $total4 . " as 3_pers_pc, SUM(4_pers) as 4_pers, 100*SUM(4_pers)/" . $total4 . " as 4_pers_pc, (SUM(5_pers)+SUM(6_pers)+SUM(7_pers)+SUM(supegal_8_pers)) as 5perssup , 100*(SUM(5_pers)+SUM(6_pers)+SUM(7_pers)+SUM(supegal_8_pers))/" . $total4 . " as 5perssup_pc, " . $total4 . " as total, 100*$total4/" . $total4 . " as total_pc " . $from;
            $query4 = $this->db->query($sql4);
            $result4 = $query4->getResultArray();
            $data[$fiche][$this->annee]['Taille du ménage'][$geo] = $result4[0];
            if (empty($this->tRubrique[4])) {
                $this->tRubrique[4]['var_croise_lib'] = 'Taille du ménage';
                $this->tRubrique[4]['var_croise_ancre'] = 'taille_menage';
            }
        }
        //Composition du ménage
        $sql5Total = "SELECT (SUM(isole)+SUM(isole_1_pac)+SUM(isole_2_pac)+SUM(isole_3_pac)+SUM(isole_4_pac)+SUM(isole_5_pac)+SUM(isole_6_pac_ou_plus)+SUM(2_cotitul_ou_plus)+SUM(2_cotitul_ou_plus_et_1_pac)+SUM(2_cotitul_ou_plus_et_2_pac)+SUM(2_cotitul_ou_plus_et_3_pac)+SUM(2_cotitul_ou_plus_et_4_pac)+SUM(2_cotitul_ou_plus_et_5_pac)+SUM(2_cotitul_ou_plus_et_6_pac_ou_plus)) as total " . $from;
        $query5Total = $this->db->query($sql5Total);
        $result5Total = $query5Total->getResultArray();
        $total5 = $result5Total[0]['total'];
        if ($total5 > 0 && !is_null($total5)) {
            $sql5 = "SELECT SUM(isole) as isole, 100*SUM(isole)/" . $total5 . " as isole_pc, SUM(2_cotitul_ou_plus) as 2_cotitul_ou_plus, 100*SUM(2_cotitul_ou_plus)/" . $total5 . " as2_cotitul_ou_plus_pc, SUM(isole_1_pac) as isole_1_pac, 100*SUM(isole_1_pac)/" . $total5 . " as isole_1_pac_pc, SUM(isole_2_pac) as isole_2_pac, 100*SUM(isole_2_pac)/" . $total5 . " as isole_2_pac_pc ,(SUM(isole_3_pac)+SUM(isole_4_pac)+SUM(isole_5_pac)+SUM(isole_6_pac_ou_plus)) as isole_3_pac_p, 100*(SUM(isole_3_pac)+SUM(isole_4_pac)+SUM(isole_5_pac)+SUM(isole_6_pac_ou_plus))/" . $total5 . " as isole_3_pac_p_pc, SUM(2_cotitul_ou_plus_et_1_pac) as 2_cotitul_ou_plus_et_1_pac, 100*SUM(2_cotitul_ou_plus_et_1_pac)/" . $total5 . " as 2_cotitul_ou_plus_et_1_pac_pc,  SUM(2_cotitul_ou_plus_et_2_pac) as 2_cotitul_ou_plus_et_2_pac, 100*SUM(2_cotitul_ou_plus_et_2_pac)/" . $total5 . " as 2_cotitul_ou_plus_et_2_pac_pc, (SUM(2_cotitul_ou_plus_et_3_pac)+SUM(2_cotitul_ou_plus_et_4_pac)+SUM(2_cotitul_ou_plus_et_5_pac)+SUM(2_cotitul_ou_plus_et_6_pac_ou_plus))as 2_cotitul_ou_plus_pac, 100*(SUM(2_cotitul_ou_plus_et_3_pac)+SUM(2_cotitul_ou_plus_et_4_pac)+SUM(2_cotitul_ou_plus_et_5_pac)+SUM(2_cotitul_ou_plus_et_6_pac_ou_plus))/" . $total5 . " as 2_cotitul_ou_plus_pac_pc, " . $total5 . "  as total , 100*$total5/" . $total5 . " as total_pc " . $from;
            $query5 = $this->db->query($sql5);
            $result5 = $query5->getResultArray();
            $data[$fiche][$this->annee]['Composition du ménage'][$geo] = $result5[0];
            if (empty($this->tRubrique[5])) {
                $this->tRubrique[5]['var_croise_lib'] = 'Composition du ménage';
                $this->tRubrique[5]['var_croise_ancre'] = 'compo_menage';
            }
        }
        //Motif de la demande
        $sql6Total = "SELECT (SUM(sans_logement_propre)+SUM(demolition)+SUM(logement_non_habitable)+SUM(logement_repris)+SUM(procedure_expulsion)+SUM(violences_familiales)+SUM(handicap)+SUM(raisons_de_sante)+SUM(logement_trop_cher)+SUM(logement_trop_grand)+SUM(divorce_separation)+SUM(decohabitation)+SUM(logement_trop_petit)+SUM(futur_couple)+SUM(regroupement_familial)+SUM(assistante_maternelle)+SUM(pb_environnement_voisinage)+SUM(mutation_professionnelle)+SUM(rapprochement_travail)+SUM(rapprochement_services)+SUM(rapprochement_famille)+SUM(proprietaire_en_difficulte)+SUM(autre_motif)) as total " . $from;
        $query6Total = $this->db->query($sql6Total);
        $result6Total = $query6Total->getResultArray();
        $total6 = $result6Total[0]['total'];
        if ($total6 > 0 && !is_null($total6)) {
            $sql6 = "SELECT (SUM(demolition)+SUM(logement_non_habitable)+SUM(logement_repris)+SUM(procedure_expulsion)+SUM(sans_logement_propre)) as pb_log, 100*(SUM(demolition)+SUM(logement_non_habitable)+SUM(logement_repris)+SUM(procedure_expulsion)+SUM(sans_logement_propre))/" . $total6 . " as pb_log_pc, (SUM(divorce_separation)+SUM(decohabitation)+SUM(futur_couple)+SUM(regroupement_familial)+SUM(rapprochement_famille)) as fam, 100*(SUM(divorce_separation)+SUM(decohabitation)+SUM(futur_couple)+SUM(regroupement_familial)+SUM(rapprochement_famille))/" . $total6 . " as fam_pc, (SUM(handicap)+SUM(raisons_de_sante)) as sante, 100*(SUM(handicap)+SUM(raisons_de_sante))/" . $total6 . " as sante_pc ,(SUM(logement_trop_cher)+SUM(logement_trop_grand)+SUM(logement_trop_petit)) as log_na, 100*(SUM(logement_trop_cher)+SUM(logement_trop_grand)+SUM(logement_trop_petit))/" . $total6 . " as log_na_pc, (SUM(pb_environnement_voisinage)+SUM(rapprochement_services)) as pb_env, 100*(SUM(pb_environnement_voisinage)+SUM(rapprochement_services))/" . $total6 . " as pb_env_pc, (SUM(assistante_maternelle)+SUM(mutation_professionnelle)+SUM(rapprochement_travail)) as sit_pro, 100*(SUM(assistante_maternelle)+SUM(mutation_professionnelle)+SUM(rapprochement_travail))/" . $total6 . " as sit_pro_pc, (SUM(violences_familiales)+SUM(proprietaire_en_difficulte)+SUM(autre_motif)) as autre, 100*(SUM(violences_familiales)+SUM(proprietaire_en_difficulte)+SUM(autre_motif))/" . $total6 . " as autre_pc," . $total6 . "  as total , 100*$total6/" . $total6 . " as total_pc " . $from;
            $query6 = $this->db->query($sql6);
            $result6 = $query6->getResultArray();
            $data[$fiche][$this->annee]['Motif de la demande'][$geo] = $result6[0];
            if (empty($this->tRubrique[6])) {
                $this->tRubrique[6]['var_croise_lib'] = 'Motif de la demande';
                $this->tRubrique[6]['var_croise_ancre'] = 'motif_demande';
            }
        } else {
            unset($this->tRubrique[6]);
        }
        //Statut Antérieur
        $sql7Total = "SELECT (SUM(loc_hlm)+SUM(loc_parc_prive)+SUM(logement_fonction)+SUM(proprietaire_occupant)+SUM(chez_parents_enfants)+SUM(chez_particulier)+SUM(loge_gratuit)+SUM(sous_loc_ou_heberge_temp)+SUM(centre_enfance_famille)+SUM(residence_etudiant)+SUM(rhvs)+SUM(rs_foyer)+SUM(structure_hebergement)+SUM(camping_caravaning)+SUM(hotel)+SUM(sans_abri)+SUM(squat)) as total " . $from;
        $query7Total = $this->db->query($sql7Total);
        $result7Total = $query7Total->getResultArray();
        $total7 = $result7Total[0]['total'];
        if ($total7 > 0 && !is_null($total7)) {
            $sql7 = "SELECT SUM(loc_hlm) as hml, 100*SUM(loc_hlm)/" . $total7 . "  as hml_pc, (SUM(loc_parc_prive)+SUM(logement_fonction)) as prive, 100*(SUM(loc_parc_prive)+SUM(logement_fonction))/" . $total7 . "  as prive_pc, SUM(proprietaire_occupant) as po, 100*SUM(proprietaire_occupant)/" . $total7 . "  as po_pc, SUM(chez_parents_enfants) as decohab, 100*SUM(chez_parents_enfants)/" . $total7 . "  as decohab_pc, (SUM(chez_particulier)+SUM(loge_gratuit)+SUM(sous_loc_ou_heberge_temp)) as heberg, 100*(SUM(chez_particulier)+SUM(loge_gratuit)+SUM(sous_loc_ou_heberge_temp))/" . $total7 . "  as heberg_pc, (SUM(centre_enfance_famille)+SUM(residence_etudiant)+SUM(rhvs)+SUM(rs_foyer)+SUM(structure_hebergement)) as foyer, 100*(SUM(centre_enfance_famille)+SUM(residence_etudiant)+SUM(rhvs)+SUM(rs_foyer)+SUM(structure_hebergement))/" . $total7 . "  as foyer_pc, (SUM(camping_caravaning)+SUM(hotel)+SUM(sans_abri)+SUM(squat)) as precaire, 100*(SUM(camping_caravaning)+SUM(hotel)+SUM(sans_abri)+SUM(squat))/" . $total7 . "  as precaire_pc, " . $total7 . "  as total , 100*$total7/" . $total7 . " as total_pc " . $from;
            $query7 = $this->db->query($sql7);
            $result7 = $query7->getResultArray();
            $data[$fiche][$this->annee]['Statut Antérieur'][$geo] = $result7[0];
            if (empty($this->tRubrique[7])) {
                $this->tRubrique[7]['var_croise_lib'] = 'Statut Antérieur';
                $this->tRubrique[7]['var_croise_ancre'] = 'statut_ant';
            }
        } else {
            unset($this->tRubrique[7]);
        }
        //Situation professionnelle du chef de ménage
        $sql8Total = "SELECT (SUM(cdi_ou_fonctionnaire)+SUM(artisan_profession_liberale)+SUM(cdd_stage_interim)+SUM(apprenti)+SUM(retraite)+SUM(chomage)+SUM(etudiant)+SUM(autre)) as total " . $from;
        $query8Total = $this->db->query($sql8Total);
        $result8Total = $query8Total->getResultArray();
        $total8 = $result8Total[0]['total'];
        if ($total8 > 0 && !is_null($total8)) {
            $sql8 = "SELECT (SUM(cdi_ou_fonctionnaire)+SUM(artisan_profession_liberale)) as cdi, 100*(SUM(cdi_ou_fonctionnaire)+SUM(artisan_profession_liberale))/" . $total8 . "  as cdi_pc, (SUM(cdd_stage_interim)+SUM(apprenti)) as cdd, 100*(SUM(cdd_stage_interim)+SUM(apprenti))/" . $total8 . "  as cdd_pc, SUM(retraite) as retraite, 100*SUM(retraite)/" . $total8 . "  as retraite_pc, SUM(chomage) as chomage, 100*SUM(chomage)/" . $total8 . "  as chomage_pc, (SUM(etudiant)+SUM(autre)) as ss_emploi, 100*(SUM(etudiant)+SUM(autre))/" . $total8 . "  as ss_emploi_pc, " . $total8 . "  as total , 100*$total8/" . $total8 . " as total_pc " . $from;
            $query8 = $this->db->query($sql8);
            $result8 = $query8->getResultArray();
            $data[$fiche][$this->annee]['Situation professionnelle du chef de ménage'][$geo] = $result8[0];
            if (empty($this->tRubrique[8])) {
                $this->tRubrique[8]['var_croise_lib'] = 'Situation professionnelle du chef de ménage';
                $this->tRubrique[8]['var_croise_ancre'] = 'situation_pro';
            }
        } else {
            unset($this->tRubrique[8]);
        }
        //Revenus/ Plafonds PLUS
        $sql9Total = "SELECT (SUM(egal_plai)+SUM(sup_plai_et_egal_plus)+SUM(sup_plus_et_egalpls)+SUM(sup_pls)) as total " . $from;
        $query9Total = $this->db->query($sql9Total);
        $result9Total = $query9Total->getResultArray();
        $total9 = $result9Total[0]['total'];
        if ($total9 > 0 && !is_null($total9)) {
            $sql9 = "SELECT SUM(egal_plai) as egal_plai, 100*(SUM(egal_plai))/" . $total9 . "  as egal_plai_pc, SUM(sup_plai_et_egal_plus) as sup_plai_et_egal_plus, 100*(SUM(sup_plai_et_egal_plus))/" . $total9 . "  as sup_plai_et_egal_plus_pc, SUM(sup_plus_et_egalpls) as sup_plus_et_egalpls, 100*SUM(sup_plus_et_egalpls)/" . $total9 . "  assup_plus_et_egalpls_pc, SUM(sup_pls) as sup_pls, 100*SUM(sup_pls)/" . $total9 . "  as sup_pls_pc, " . $total9 . "  as total , COALESCE(100*$total9/" . $total9 . ",0) as total_pc " . $from;
            $query9 = $this->db->query($sql9);
            $result9 = $query9->getResultArray();
            $data[$fiche][$this->annee]['Revenus/ Plafonds PLUS'][$geo] = $result9[0];
            if (empty($this->tRubrique[9])) {
                $this->tRubrique[9]['var_croise_lib'] = 'Revenus/ Plafonds PLUS';
                $this->tRubrique[9]['var_croise_ancre'] = 'plafond';
            }
        }
        return $data;
    }
}
