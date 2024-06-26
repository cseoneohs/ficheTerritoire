<?php

//var_dump($territoire, $_SESSION);
$n = count($territoire);
$len = ($_SESSION['territoireEtude'] == 'commune') ? 5 : 9;
if (!isset($pdf) && !isset($csv) && ($n > 1)) :
    ?>
    <div id="drop_down_list_1" class="dropdown float-right d-print-none">
        <a class="btn btn-outline-secondary dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Accès direct à un territoire
        </a>
        <div class="dropdown-menu dropdown-menu-right">
                <?php
                foreach ($territoire as $code) {
                    if (preg_match('/\A[0-9]{' . $len . '}\z/', $code)) {
                        $libGeo = array_keys($_SESSION['perimetre']['labelEtude'], $code);
                        echo '<a class="dropdown-item" href="#' . $code . '" >' . $libGeo[0] . '</a>';
                    }
                }
                ?>
            </div>
    </div>
    <?php
endif;
?>
<div id="navRight" class="d-print-none">
    <a role="button" class="ancre_haut btn btn-outline-secondary d-print-none" href="#haut"><i class="fas fa-arrow-circle-up" style="font-size: 48px;"></i></a>
</div>