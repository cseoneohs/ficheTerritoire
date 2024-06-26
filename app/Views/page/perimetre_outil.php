<?php
/**
 *
 */
$attributes = array('class' => 'form-horizontal', 'id' => 'form_perimetre');
if (isset($reponse)) {
    echo '<p class="text-success">' . $reponse . '</p>';
}
?>

<div class="form">
    <h1>Extraction de données à partir de fd_logemt</h1>
    <?php
    if (isset($validation)) {
        if (is_array($validation)) {
            $validation = $validation[0];
        }
        echo '<div class="text-warning">' . $validation . '</div>';
    }
    ?>
    <?php echo form_open('perimetre/outil', $attributes); ?>
    <fieldset>
        <legend>Année observée</legend>
        <div class="form-group">
            <div class="col-sm-2"></div>
            <div class="col-sm-10">
                <select class="form-control" id="perimAnnee" name="perimAnnee">
                    <?php
                    foreach ($annee as $key => $value) {
                        echo '<option value="' . $value['annee'] . '">' . $value['annee'] . '</option>';
                    }
                    ?>
                </select>
            </div>
        </div>
    </fieldset>
    <fieldset id="perimEtud">
        <legend>Territoire étudié</legend>
        <div class="form-group" id="selectDept">
            <label class="col-sm-2 control-label" for="perimEtuDep">Département</label>
            <div class="col-sm-10">
                <select class="form-control perim_outil" id="perimEtuDep" name="perimEtuDep">
                    <option value=""></option>
                    <?php
                    foreach ($dept as $key => $value) {
                        echo '<option value="' . $key . '">' . $key . ' - ' . $value . '</option>';
                    }
                    ?>
                </select>
            </div>
        </div>
    </fieldset>
    <div class="clear"></div>
    <input type="submit" class="btn btn-default pull-right" value="Envoyer" title="Soumettre le choix des périmètres étudiés" />
    <br>
    </form>
</div>

