function checkfile(file) {
    var validExts = new Array(".csv");
    var fileExt = file.value;
    fileExt = fileExt.substring(fileExt.lastIndexOf('.'));
    if (validExts.indexOf(fileExt) < 0) {

        alert("Fichier invalide, doit être de type " + validExts.toString() + " !");
        var input = $("input:file").addClass('alert');
        return false;
    } else {
        var input = $("input:file").removeClass('alert');
        return true;
    }
}
/**
 * Commutation de l'affichage pleine largeur ou pas
 * @returns {undefined}
 */
function switchDisplay() {
    $(".switch_display").each(function () {
            if ($(this).hasClass('container')) {
                $(this).removeClass('container');
                $(this).addClass('container-fluid');
            } else if ($(this).hasClass('container-fluid')) {
                $(this).removeClass('container-fluid');
                $(this).addClass('container');
            }
        });
}
var regVirg = new RegExp("[,]", "i");
$(document).ready(function () {
    $(window).scroll(function () {
        posScroll = $(document).scrollTop();
        if (posScroll >= 200)
            $('#navRight').fadeIn(600);
        else
            $('#navRight').fadeOut(600);
    });
    $("#switch_display").on("click", function () {
        switchDisplay();
    });
    $("#switch_text_display").on("click", function () {
        $("body").toggleClass("size_sm");
        $("h1").toggleClass("size_sm");
        $("h4").toggleClass("size_sm");
    });

    $('#perimEtuScot, #perimEtuEpci, #perimEtuSect, #perimEtuCom, #perimEtuIris').attr('disabled', 'disabled');
    $("#selectDept").on('change', '#perimEtuDep', function () {
        if ($(this).hasClass('perim_outil')) {
            return;
        }
        $('#perimEtuScot').empty();
        $('#perimEtuEpci').empty();
        $('#perimEtuSect').empty();
        $('#perimEtuCom').empty();
        $('#perimEtuIris').empty();
        $('#perimEtuScot, #perimEtuEpci, #perimEtuCom').removeAttr('disabled');
        var perimEtuDep = $('#perimEtuDep').val();
        $.ajax({
            type: "POST",
            url: "./perimetre/deptSubmit",
            dataType: 'json',
            data: {dept: perimEtuDep},
            success: function (data) {
                if (data) {
                    //$("#selectScot").hide();
                    var items = '<option value=""></option>';
                    $.each(data.scot, function (key, val) {
                        items += "<option value='" + val.codegeo + "'>" + val.libel + "</option>";
                    });
                    $('#perimEtuScot').append(items);
                    //$('#selectScot').fadeIn(2000);
                    // $("#selectEpci").empty().hide();
                    var items = '';
                    $.each(data.epci, function (key, val) {
                        items += "<option value='" + val.code_epci + "'>" + val.lib_epci + "</option>";
                    });
                    $('#perimEtuEpci').append(items);
                    //$('#selectEpci').fadeIn(2000);
                    //$("#selectCommune").empty().hide();
                    var items = '';
                    $.each(data.commune, function (key, val) {
                        items += "<option value='" + val.codegeo + "'>" + val.libgeo + "</option>";
                    });
                    $('#perimEtuCom').append(items);
                }
            }
        });
    });
    $("#selectScot").on('change', '#perimEtuScot', function () {
        $('#perimEtuEpci').empty();
        $('#perimEtuCom').empty();
        var perimEtuScot = $('#perimEtuScot').val();
        if (perimEtuScot != '') {
            $.ajax({
                type: "POST",
                url: "./perimetre/scotSubmit",
                dataType: 'json',
                data: {scot: perimEtuScot},
                success: function (data) {
                    if (data) {
                        var items = '';
                        $.each(data.commune, function (key, val) {
                            items += "<option value='" + val.codegeo + "' selected>" + val.libgeo + "</option>";
                        });
                        $('#perimEtuCom').append(items);
                    }
                }
            });
        } else {
            $("#perimEtuDep").focus();
            $("#perimEtuDep").change();
        }
    });
    $("#selectEpci").on('change', '#perimEtuEpci', function () {
        $('#perimEtuCom').empty();
        $('#perimEtuSect').empty();
        $('#perimEtuSect').removeAttr('disabled');
        var perimEtuEpci = $('#perimEtuEpci').val();
        if (perimEtuEpci != '') {
            $.ajax({
                type: "POST",
                url: "./perimetre/epciSubmit",
                dataType: 'json',
                data: {epci: perimEtuEpci},
                success: function (data) {
                    if (data) {
                        var items = '<option value=""></option>';
                        $.each(data.secteur, function (key, val) {
                            items += "<option value='" + val.codegeo + "'>" + val.libel + "</option>";
                        });
                        $('#perimEtuSect').append(items);
                        var items = '';
                        $.each(data.commune, function (key, val) {
                            items += "<option value='" + val.codegeo + "' selected>" + val.libgeo + "</option>";
                        });
                        $('#perimEtuCom').append(items);
                    }
                }
            });
        }
    });
    $("#selectSecteur").on('change', '#perimEtuSect', function () {
        $('#perimEtuCom').empty();
        var perimEtuSect = $('#perimEtuSect').val();
        if (perimEtuSect != '') { 
            $.ajax({
                type: "POST",
                url: "./perimetre/secteurSubmit",
                dataType: 'json',
                data: {secteur: perimEtuSect},
                success: function (data) {
                    if (data) {
                        var items = '';
                        $.each(data.commune, function (key, val) {
                            items += "<option value='" + val.codegeo + "'>" + val.libgeo + "</option>";
                        });
                        $('#perimEtuCom').append(items);
                    }
                }
            });
            $("#chkSecteur").removeAttr('disabled');
        } else {
            $("#chkSecteur").attr('disabled', 'disabled');
        }
    });
    $("#selectCommune").on('change', '#perimEtuCom', function () {
        $('#perimEtuIris').empty();
        $('#perimEtuIris').removeAttr('disabled');
        var perimEtuCom = $('#perimEtuCom').val();
        if (!regVirg.test(perimEtuCom)) {
            $.ajax({
                type: "POST",
                url: "./perimetre/communeSubmit",
                dataType: 'json',
                data: {commune: perimEtuCom},
                success: function (data) {
                    if (data) {
                        var items = '<option value=""></option>';
                        $.each(data.iris, function (key, val) {
                            items += "<option value='" + val.iris + "'>" + val.libiris + "</option>";
                        });
                        $('#perimEtuIris').append(items);
                    }
                }
            });
        }
    });
    $('form#perimetre').submit(function () {
        var dept = $('#perimEtuDep').val();
        if (!(dept)) {
            alert(' Merci de renseigner le périmètre géographique');
            return false;
        }
        var detail = $('#chkDetail').is(':checked');
        var synt = $('#chkSynthese').is(':checked');
        if (!(detail) && !(synt)) {
            alert(' Merci de renseigner le type de fiche dédirée');
            return false;
        }
    });
    $('#chkDetail, #fd_logemt').change(function () {
        if ($(this).prop("checked") && $('#fd_logemt').prop("checked")) {
            $('#var_fd_logemt').show(600);
        } else {
            $('#var_fd_logemt').hide(600);
        }
    });
    $('#chkSynthese').change(function () {
        if ($(this).prop("checked")) {
            $('#var_fd_logemt').hide(600);
        }
    });
    $('input[type="checkbox"].disabled').change(function () {
        if (!($(this).is(':checked'))) {
            $(this).prop("checked", true);
        }
    });
    $('#importScot').hide();
    $('#importSecteur').hide();
    $("#selectGeo").change(function () {
        var val = $("#selectGeo").val();
        if (val == 'secteur') {
            $('#importScot').hide();
            $('#importSecteur').fadeIn(2000);
        }
        if (val == 'scot') {
            $('#importSecteur').hide();
            $('#importScot').fadeIn(2000);
        }
        if (val == '') {
            $('#importScot').hide();
            $('#importSecteur').hide();
        }
    });

    $('[data-toggle="tooltip"]').tooltip();

    $("th.th_2").each(function () {
        $(this).remove();
    });
    $("th.th_1").each(function () {
        $(this).attr('colspan', 2);
    });
    $("#btn_toggle_var_fd_logemt").button('toggle');
    $("#btn_toggle_var_fd_logemt").click(function() {
        $("#var_fd_logemt input").each(function() {
            $(this).trigger('click');
        });
    });
    $("#btn_all_var_fd_logemt").click(function() {
        $("#var_fd_logemt input").each(function() {
            $(this).prop('checked', 'checked');
        });
    });
});


