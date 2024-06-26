<?php
/**
 * pour les icones la documentation de Font Awesome v5 est :
 * @see https://fontawesome.com/icons?d=gallery&s=solid&c=editors&m=free
 */
$string_url = (uri_string() == '') ? 'start' : uri_string();
$string_url = (strstr($string_url, 'outil')) ? 'outil' : $string_url;
?>
<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <meta name="viewport" content="width=device-width" />
        <title>Outil Eohs - Fiches territoire</title>
        <link rel="shortcut icon" type="images/ico" href="<?php echo base_url('/images/favicon.ico'); ?>" />
        <link media="all" rel="stylesheet" href="<?php echo base_url('/dist/bootstrap/css/bootstrap.min.css'); ?>" />
        <link media="screen" rel="stylesheet" href="<?php echo base_url('/css/style.css'); ?>" />
        <link media="print" rel="stylesheet" href="<?php echo base_url('/css/print.css'); ?>" />
        <link rel="stylesheet" href="<?php echo base_url('/dist/fontawesome-free-5.15.4/css/all.min.css'); ?>" />
        <script type="text/javascript" src="<?php echo base_url('/js/jquery-2.2.4.min.js'); ?>"></script>
        <script type="text/javascript" src="<?php echo base_url('/dist/bootstrap/js/popper.min.js'); ?>"></script>
        <script type="text/javascript" src="<?php echo base_url('/dist/bootstrap/js/bootstrap.min.js'); ?>"></script>
        <script type="text/javascript" src="<?php echo base_url('/scripts/formAction.js'); ?>"></script>
    </head>
    <body id="haut">
        <div id="wrap">
            <header class="d-print-none">
                <div class="container-fluid">
                    <div class="row bg-light">
                        <div class="switch_display container-fluid">
                            <div class="row">
                                <div class="col-6" id="logo">
                                    <a href="<?php echo base_url(); ?>" class="logo">
                                        <img title="" alt="EOHS : Etude, observation, habitat, statistique" src="<?php echo base_url('/images/logo-eohs-1.png') ?>" class="logo">
                                    </a>
                                </div>
                                <div class="col-6">
                                    <br>
                                    <img class="float-right" alt="Etudes observation habitat statistique" src="<?php echo base_url('/images/etudes_observation_habitat_statistique.png') ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="switch_display container-fluid d-print-none">
                            <nav class="navbar navbar-expand-lg navbar-light" style="background-color: #a8cf38;">
                                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                                    <span class="navbar-toggler-icon"></span>
                                </button>
                                <div class="collapse navbar-collapse" id="navbarNav">
                                    <ul style="margin-right:0px !important;" class="navbar-nav" id="menu-main-menu">
                                        <li class="nav-item <?php echo (strstr($string_url, 'start') ) ? 'active' : ''; ?>" id="nav-item-perimetre">
                                            <a class="nav-link" data-delay="200"  href="<?php echo base_url('start') ?>" title="Réinitialisation des périmètres à étudier">ACCUEIL</a>
                                        </li>
                                        <li class="nav-item <?php echo ((strstr($string_url, 'perimetre') || strstr($string_url, 'fiche')) && ($_SESSION['territoireEtude'] == 'commune')) ? 'active' : ''; ?>" id="nav-item-fiche">
                                            <a class="nav-link" data-delay="200" title="Fiche, construction d'une fiche de synthèse ou d'une fiche détaillée " href="<?php echo base_url('fiche/start/commune') ?>">FICHE communes</a>
                                        </li>
                                        <li class="nav-item <?php echo ((strstr($string_url, 'perimetre') || strstr($string_url, 'fiche')) && $_SESSION['territoireEtude'] == 'epci') ? 'active' : ''; ?>" id="nav-item-fiche">
                                            <a class="nav-link" data-delay="200" title="Fiche, construction d'une fiche de synthèse ou d'une fiche détaillée " href="<?php echo base_url('fiche/start/epci') ?>">FICHE EPCI</a>
                                        </li>
                                        <li class="nav-item mr-5 <?php echo (strstr($string_url, 'outil') ) ? 'active' : ''; ?>" id="nav-item-outil">
                                            <a class="nav-link" data-delay="200" title="Outils d'extraction de données" href="<?php echo base_url('outil/start') ?>">OUTIL</a>
                                        </li>
                                        <?php if ($string_url == 'fiche') { ?>
                                            <li class="nav-item ml-5"><a id="switch_display" class="nav-link" data-delay="200" title="Basculer affichage"><i class="fas fa-text-width" style="font-size: 24px;"></i></a></li>
                                            <li class="nav-item ml-5"><a id="switch_text_display" class="nav-link" data-delay="200" title="Basculer taille de police"><i class="fas fa-text-height" style="font-size: 24px;"></i></a></li>
                                        <?php } ?>
                                    </ul>
                                </div>
                                <?php
                                if (!preg_match('/fiche/', $string_url)) {
                                    echo '<a class="text-muted" title="Déconnexion" href="' . base_url("auth/logout") . '">' . $user["first_name"] . ' ' . $user["last_name"] . '&nbsp;<i class="fas fa-sign-out-alt text-muted"></i></a>';
                                }
                                ?>
                            </nav>
                        </div>
                    </div>
                </div>
                <!-- end main menu -->
            </header>
            <div id="main" class="switch_display container-fluid">
