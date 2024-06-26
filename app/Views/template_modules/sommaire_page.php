<?php

if (!isset($pdf) && !isset($csv)) :
    ?>
    <div id="drop_down_list_1" class="dropdown float-right d-print-none">
        <a class="btn btn-outline-secondary dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Accès direct à une thématique
        </a>
        <div class="dropdown-menu dropdown-menu-right">
            <?php
            foreach ($tSsRubrique as $value) {
                if (!isset($value['var_croise_ancre']) || !isset($value['var_croise_lib'])) {
                    continue;
                }
                echo '<a class="dropdown-item" href="#' . $value['var_croise_ancre'] . '" >' . $value['var_croise_lib'] . '</a>';
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