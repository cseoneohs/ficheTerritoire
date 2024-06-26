<?php
/**
 * @see https://github.com/Igor-Vladyka/leaflet.browser.print
 */
$json = '';
foreach ($dataCarto as $value) {
    $json .= '{' . rtrim(json_encode($value->geojson, JSON_FORCE_OBJECT), '"') . '},';
}
$json = str_replace('"\"', '"', $json);
$json = str_replace('\"', '"', $json);
$deptOutreMer = ['971', '972', '973', '974'];
$dept = array_intersect($_SESSION['perimetre']['departement'], $deptOutreMer);
$isDeptOutreMer = (count($dept) > 0) ? true : false;
?>
<link rel="stylesheet" href="<?php echo base_url('/dist/leaflet/leaflet.css'); ?>" />
<link rel="stylesheet" href="<?php echo base_url('/dist/leaflet/leaflet.zoomhome.css'); ?>"/>
<link rel="stylesheet" href="<?php echo base_url('/dist/leaflet/plugins/Control.Geocoder.css'); ?>" />
<style>
    /**
    *leaflet.fullscreen.css
    *
    */
    .leaflet-control-fullscreen a {
        background:#fff url(<?php echo base_url('/dist/leaflet/plugins/fullscreen.png'); ?>) no-repeat 0 0;
        background-size:26px 52px;
    }
    .leaflet-touch .leaflet-control-fullscreen a {
        background-position: 2px 2px;
    }
    .leaflet-fullscreen-on .leaflet-control-fullscreen a {
        background-position:0 -26px;
    }
    .leaflet-touch.leaflet-fullscreen-on .leaflet-control-fullscreen a {
        background-position: 2px -24px;
    }

    /* Do not combine these two rules; IE will break. */
    .leaflet-container:-webkit-full-screen {
        width:100%!important;
        height:100%!important;
    }
    .leaflet-container.leaflet-fullscreen-on {
        width:100%!important;
        height:100%!important;
    }

    .leaflet-pseudo-fullscreen {
        position:fixed!important;
        width:100%!important;
        height:100%!important;
        top:0!important;
        left:0!important;
        z-index:99999;
    }

    @media
    (-webkit-min-device-pixel-ratio:2),
    (min-resolution:192dpi) {
        .leaflet-control-fullscreen a {
            background-image:url(<?php echo base_url('/dist/leaflet/plugins/fullscreen@2x.png'); ?>)
        }
    }
    /**
    * end leaflet.fullscreen.css
    */
    #map {
        width: 100%;
        height: 800px;
        padding: 0;
        margin: 0;
    }
    #map .info {
        padding: 6px 8px;
        font: 14px/16px Arial, Helvetica, sans-serif;
        background: white;
        background: rgba(255,255,255,0.8);
        box-shadow: 0 0 15px rgba(0,0,0,0.2);
        border-radius: 5px;
    }
    #map .info h4 {
        margin: 0 0 5px;
        color: #777;
    }
    #map .legend {
        line-height: 18px;
        color: #555;
    }
    #map .legend i {
        width: 18px;
        height: 18px;
        float: left;
        margin-right: 8px;
        opacity: 0.7;
    }
    .leaflet-tooltip-eohs {
        font-size: 0.9em;
        background: none !important;
        border: none !important;
        ;
        box-shadow: none !important;
    }
    .leaflet-tooltip-eohs-data {
        font-size: 1em;
        font-weight: bold;
    }
    .container_indice {
        display: flex;
        flex-direction:column;
    }
    .leaflet-tooltip-bottom::before {
        top: 5px !important;
        display: none !important;
    }
</style>
<h1 id="carto_titre"><?php echo $variable['lib'][0]; ?></h1>
<?php
if ($isDeptOutreMer) {
    echo '<div class="p-3 mb-2 bg-warning text-dark">Pour les départements d\'outre mer il faut choisir un fond de carte vide car ils sont positionnés de façon à être sur la même vue que la métroole !</div>';
}
?>
<div id="initialmap"></div>
<div class="row">
    <div class="col">
        <label class="bg-info font-weight-bold control-label" style="text-align: right;width: 100%;margin-bottom: 0;height: calc(1.5em + .75rem + 2px);padding: .375rem .75rem;">Indicateurs :</label>
    </div>
    <div class="col">
        <select class="form-control" name="var_choose" id="var_choose">
            <!--<option>Choisir une variable</option>-->
            <?php
            foreach ($variable['lib'] as $key => $value) {
                echo '<option data-symb="' . $symb[$value] . '" value="' . $key . '">' . $value . '</option>';
            }
            ?>
        </select>
    </div>
</div>
<div class="row">
    <div class="col"><br></div>
</div>
<script src="<?php echo base_url('/dist/leaflet/leaflet.js'); ?>"></script>
<script src="<?php echo base_url('/dist/leaflet/leaflet.zoomhome.min.js'); ?>"></script>
<script src="<?php echo base_url('/dist/leaflet/leaflet.browser.print-master/dist/leaflet.browser.print.min.js'); ?>"></script>
<script src="<?php echo base_url('/dist/dom-to-image_2.6/dom-to-image.min.js'); ?>"></script>
<script src="<?php echo base_url('/dist/leaflet/plugins/Leaflet.fullscreen.min.js'); ?>"></script>
<script src="<?php echo base_url('/dist/leaflet/plugins/Control.Geocoder.min.js'); ?>"></script>

<script>
    window.print = function () {
        return domtoimage
                .toPng(document.querySelector(".grid-print-container"))
                .then(function (dataUrl) {
                    var link = document.createElement('a');
                    link.download = map.printControl.options.documentTitle || "carteFicheTerritoire" + '.png';
                    link.href = dataUrl;
                    link.click();
                });
    };
</script>
<script>
    //le contour des communes
    var communesGeojson = {"type": "FeatureCollection", "name": "a_com2020_geojson_0", "crs": {"type": "name", "properties": {"name": "urn:ogc:def:crs:OGC:1.3:CRS84"}}, "features": [<?php echo $json; ?>]};
    var map;
    //les données au format json
    var dataJsonAll = <?php echo json_encode($dataJsonAll, JSON_FORCE_OBJECT); ?>;
    //les libellées des variables
    var titre = ["<?php echo implode('","', $variable['lib']); ?>"];
    //le numéro de variable sélectionné
    var numIndicateur = 0;
    //la variable contenant les données sélectionnées
    var data;
    //le tableau contenant les couleurs utilsées
    var tColorIndice;
    //le préfixe de l'attribution
    var attributionPrefix = '<a href="https://www.eohs.org/">Eohs</a> | <a href="https://leafletjs.com" title="A JS library for interactive maps"> Leaflet </a>';
    //un symbole complément d'infromation de la donnée (exemple : %)
    $("#var_choose").prop("selectedIndex", 0);
    var symb = $("#var_choose").find(':selected').data("symb");
    drawCarte();
    $("#var_choose").change(function () {
        numIndicateur = this.value;
        $("#carto_titre").text(titre[numIndicateur]);
        symb = $("#var_choose").find(':selected').data("symb");
        drawCarte();
    });
    /**
     * La CARTE
     */
    function drawCarte() {

        document.getElementById('initialmap').innerHTML = '<div id="map"></div>';
        map = L.map('map', {zoomControl: false});
        //map.setView(new L.LatLng(46.4905, 2.6027), 7);
        map.attributionControl.setPrefix('');
        map.addControl(new L.Control.Fullscreen({
            title: {
                'false': 'Voir en plein écran',
                'true': 'Quitter le plein écran'
            }
        }));

        tColorIndice = ['#FEE5D9', '#FC9272', '#FB6A4A', '#A50F15'];

        function getData() {
            let data = new Array();
            for (var key in dataJsonAll[numIndicateur]) {
                data.push(dataJsonAll[numIndicateur][key]['var']);
            }
            data.sort(function (a, b) {
                return a - b;
            });
            return data;
        }

        function roundDecimal(nombre, precision) {
            var precision = precision || 2;
            var tmp = Math.pow(10, precision);
            return Math.round(nombre * tmp) / tmp;
        }

        function getGradesFromData(data) {
            let grades = new Array();
            var min = 100000;
            var max = 0;
            var n = 0;
            for (var key in data) {
                if (data[key] < min) {
                    min = parseFloat(data[key]);
                }
                if (data[key] > max) {
                    max = parseFloat(data[key]);
                }
                n += 1;
            }
            //var pas = parseFloat((max - min) / 5);
            //var inter = [min, parseInt(min + pas, 10), parseInt((min + (2 * pas)), 10), parseInt((min + (3 * pas)), 10), parseInt((min + (4 * pas)), 10), max];
            var pas = parseInt(n / 3);
            if (min < 100) {
                grades = [roundDecimal(min, 2), roundDecimal(data[pas], 2), roundDecimal(data[2 * pas], 2), roundDecimal(max, 2)];
            } else {
                grades = [Math.round(min), Math.round(data[pas]), Math.round(data[2 * pas]), Math.round(max)];
            }
            return grades;
        }

        function highlightFeature(e) {
            var layer = e.target;

            layer.setStyle({
                weight: 3,
                color: '#666',
                dashArray: '',
                fillOpacity: 0.7
            });

            if (!L.Browser.ie && !L.Browser.opera && !L.Browser.edge) {
                layer.bringToFront();
            }

            info.update(layer.feature.properties);
        }

        function resetHighlight(e) {
            geojsonCommunes.resetStyle(e.target);
            info.update();
        }

        function zoomToFeature(e) {
            map.fitBounds(e.target.getBounds());
        }

        function onEachFeature(feature, layer) {
            layer.on({
                mouseover: highlightFeature,
                mouseout: resetHighlight,
                click: zoomToFeature
            });
            displayLabelCommune(layer);
        }

        function onEachFeatureData(feature, layer) {
            layer.bindTooltip("<div class='labelCommune'>" + layer.feature.properties.lib_commune + "</div>",
                    {
                        direction: 'center',
                        permanent: true,
                        sticky: false,
                        offset: [0, 0],
                        opacity: 0.6,
                        className: 'leaflet-tooltip-eohs'
                    });
        }

        function displayLabelCommune(layer) {

            layer.bindTooltip("<div class='labelCommuneData badge badge-secondary'>" + dataJsonAll[numIndicateur][layer.feature.properties.code_insee]['var'] + ' ' + symb + "</div>",
                    {
                        direction: 'bottom',
                        permanent: true,
                        sticky: false,
                        offset: [0, 0],
                        //opacity: 0.6,
                        className: 'leaflet-tooltip-eohs leaflet-tooltip-eohs-data'
                    });
        }

        function getColorIndice(feature) {
            if (dataJsonAll[numIndicateur][feature.properties.code_insee]['var'] == 0) {
                return tColorIndice[0];
            } else if ((dataJsonAll[numIndicateur][feature.properties.code_insee]['var'] >= grades[0]) && (dataJsonAll[numIndicateur][feature.properties.code_insee]['var'] <= grades[1])) {
                return tColorIndice[1];
            } else if ((dataJsonAll[numIndicateur][feature.properties.code_insee]['var'] >= grades[1]) && (dataJsonAll[numIndicateur][feature.properties.code_insee]['var'] <= grades[2])) {
                return tColorIndice[2];
            } else if ((dataJsonAll[numIndicateur][feature.properties.code_insee]['var'] >= grades[2]) && (dataJsonAll[numIndicateur][feature.properties.code_insee]['var'] <= grades[3])) {
                return tColorIndice[3];
            } else if ((dataJsonAll[numIndicateur][feature.properties.code_insee]['var'] >= grades[3]) && (dataJsonAll[numIndicateur][feature.properties.code_insee]['var'] <= grades[4])) {
                return tColorIndice[4];
            } else if ((dataJsonAll[numIndicateur][feature.properties.code_insee]['var'] >= grades[4]) && (dataJsonAll[numIndicateur][feature.properties.code_insee]['var'] <= grades[5])) {
                return tColorIndice[5];
            } else {
                return tColorIndice[6];
            }
        }

        function styleBackgroungCommune(feature) {
            return {
                fillColor: getColorIndice(feature),
                weight: 1,
                opacity: 1,
                color: 'white',
                // dashArray: '3',
                fillOpacity: 0.7
            };
        }
        function styleBackgroungCommuneData(feature) {
            return {
                fillColor: 'none',
                weight: 0,
                opacity: 1,
                color: 'white',
                //  dashArray: 0,
                fillOpacity: 0.7
            };
        }
        data = getData();

        var grades = getGradesFromData(data);

        var geojsonCommunes = new L.geoJson(communesGeojson, {
            onEachFeature: onEachFeature,
            style: styleBackgroungCommune
        }
        ).addTo(map);
        var geojsonCommunesData = new L.geoJson(communesGeojson, {
            onEachFeature: onEachFeatureData,
            style: styleBackgroungCommuneData
        }
        ).addTo(map);
        map.fitBounds(geojsonCommunes.getBounds());
        //La carte est chargée on peut afficher le controle de zoom
        var zoomHome = L.Control.zoomHome({zoomHomeTitle: 'Zoom initial', zoomInTitle: 'Zoom avant', zoomOutTitle: 'Zoom arrière'});
        zoomHome.addTo(map);
        var baseMaps = {
            'OpenStreetMap': basemap(),
            'OSM Stamen Toner': bwmap().addTo(map),
            'Fond blanc': blank()
        };

        L.control.layers(baseMaps, {'<img src="<?php echo base_url('/images/a_com2020_geojson_0.png'); ?>" /> Communes': geojsonCommunes}).addTo(map);

        // INFO
        var info = L.control({position: 'bottomleft'});
        info.onAdd = function (map) {
            this._div = L.DomUtil.create('div', 'info'); // create a div with a class "info"
            this.update();
            return this._div;
        };
        info.update = function (props) {
            this._div.innerHTML = '<h4>Communes</h4>' + (props ?
                    '<b>' + props.lib_commune + '</b> ' + props.code_insee + ' <br />'
                    + titre[0] + ' ' + dataJsonAll[0][props.code_insee]['var'] + ' <br />'
                    + titre[1] + ' ' + dataJsonAll[1][props.code_insee]['var'] + ' <br />'
                    + titre[2] + ' ' + dataJsonAll[2][props.code_insee]['var'] + ' <br />'
                    : 'Survoler une commune');
        };
        info.addTo(map);

        var osmGeocoder = new L.Control.Geocoder({
            collapsed: true,
            position: 'topleft',
            text: 'Search',
            title: 'Testing'
        }).addTo(map);
        document.getElementsByClassName('leaflet-control-geocoder-icon')[0].className += ' fa fa-search';
        document.getElementsByClassName('leaflet-control-geocoder-icon')[0].title += 'Search for a place';

        L.LegendControl = L.Control.extend({
            onAdd: function (map) {

                var div = L.DomUtil.create('div', 'info legend');
                var labels = [];
                labels.push('<h6>' + titre[numIndicateur] + '</h6><div class="container_indice"><div><i style="background:' + tColorIndice[0] + '"></i> ' +
                        0 + '</div>');
                for (var i = 1; i < grades.length; i++) {
                    if (grades[i - 1] == grades[i]) {
                        continue;
                    }
                    labels.push('<div><i style="background:' + tColorIndice[i] + '"></i> ' + grades[i - 1] + ' - ' + grades[i] + ' ' + symb + '</div>');
                }
                labels.push('</div>');
                div.innerHTML = labels.join('');
                return div;
            }
        });

        L.legendControl = function (options) {
            return new L.LegendControl(options);
        };
        L.legendControl({position: 'bottomright'}).addTo(map);

        L.control.browserPrint({
            title: "Exporter la carte en image",
            documentTitle: 'carteFicheTerritoire',
            printModes: [
                L.control.browserPrint.mode.landscape("A3 Paysage", "A3"),
                L.control.browserPrint.mode.landscape("Paysage", "A4"),
                "Portrait",
                L.control.browserPrint.mode.auto("Auto", "A4"),
                L.control.browserPrint.mode.custom("Séléctionnez la zone", "A4")
            ],
            manualMode: false}).addTo(map);

        map.on(L.Control.BrowserPrint.Event.PrintStart, function (e) {
            $(".leaflet-control-attribution").hide();
        });

        map.on(L.Control.BrowserPrint.Event.PrintEnd, function (e) {
            $(".leaflet-control-attribution").show();
        });

        function basemap() {
            var attr = 'Map data © <a href="https://openstreetmap.org">OpenStreetMap</a> contributors.';
            return L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
                opacity: 0.5,
                //minZoom: 6,
                attribution: attributionPrefix + attr
            });
        }

        function bwmap() {
            // maps.stamen.com
            var attr = 'Map tiles by <a href="http://stamen.com">Stamen Design</a>, under <a href="http://creativecommons.org/licenses/by/3.0">CC BY 3.0</a>. Data by <a href="https://openstreetmap.org">OpenStreetMap</a>, under <a href="https://www.openstreetmap.org/copyright">ODbL</a>.';
            return L.tileLayer("http://tile.stamen.com/toner-background/{z}/{x}/{y}.png", {
                opacity: 0.3,
                attribution: attributionPrefix + attr
            });
        }

        function blank() {
            var layer = new L.Layer();
            layer.getAttribution = function () {
                return attributionPrefix;
            };
            layer.onAdd = layer.onRemove = function () {};
            return layer;
        }
    }


</script>