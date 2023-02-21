<template>
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Cartographie des PAPs</h1>
        </div>
        <div class="card shadow mb-4">
            <div class="row mt-3 ml-2">
                <div class="col-sm-2">
                    <h3>Choisir un AXE</h3>
                </div>
                
                <div class="col-sm-2">
                    <button @click.prevent="loadMap('/cartographie?where=axe.NOM_AXE&search=RN7')" :disabled="selected=='RN7'" class="btn btn-info btn-block">AXE RN7</button>
                </div>
                <div class="col-sm-2">
                    <button @click.prevent="loadMap('/cartographie?where=axe.NOM_AXE&search=RN35')" :disabled="selected=='RN35'" class="btn btn-info btn-block">AXE RN35</button>
                </div>
                <div class="col-sm-2">
                    <button @click.prevent="loadMap('/cartographie?where=axe.NOM_AXE&search=RRS')" :disabled="selected=='RRS'" class="btn btn-info btn-block">AXE RRS</button>
                </div>
                
            </div>
            <div class="card-body map" id="map" style="height: 700px; width: 100%">
               <!-- <h2>Current zoom : {{ currentZoom }} and Current center : {{currentCenter}}</h2>-->
                <l-map
                @update:zoom="updateZoom"
                @update:center="updateCenter"
                :zoom="zoom"
                :center="center">
                <l-tile-layer
                    :url="url"
                    :attribution="attribution"
                />
                
                <l-marker 
                :key="index"
                v-for="(pap,index) in paps"
                :lat-lng="latLng(pap.LAT, pap.LONG)">
                <l-popup>
                    <table class="table table-bordered">
                        <tr>
                            <td style="font-size:14px;font-weight:bold">ID</td>
                            <td style="font-size:14px;font-weight:bold;color:#2c3e50;">{{pap.ID_PAP}}</td>
                        </tr>
                        <tr>
                            <td style="font-size:14px;font-weight:bold">LOCALITE</td>
                            <td style="font-size:14px;font-weight:bold;color:#2c3e50;">{{pap.LOCALITE}}</td>
                        </tr>
                        <tr>
                            <td style="font-size:14px;font-weight:bold">AXE</td>
                            <td style="font-size:14px;font-weight:bold;color:#2c3e50;">{{pap.AXE}}</td>
                        </tr>
                        <tr>
                            <td style="font-size:14px;font-weight:bold">NOM PRENOM</td>
                            <td style="font-size:14px;font-weight:bold;color:#2c3e50;">{{pap.NOM_PRENOM}}</td>
                        </tr>
                        <tr>
                            <td style="font-size:14px;font-weight:bold">SEXE</td>
                            <td style="font-size:14px;font-weight:bold;color:#2c3e50;">{{pap.SEXE}}</td>
                        </tr>
                        <tr>
                            <td style="font-size:14px;font-weight:bold">TYPE PAIEMENT</td>
                            <td style="font-size:14px;font-weight:bold;color:#2c3e50;">{{pap.TYPE_PAIEMENT}}</td>
                        </tr>
                        <tr>
                            <td style="font-size:14px;font-weight:bold">PAYÉ</td>
                            <td style="font-size:14px;font-weight:bold;color:#2c3e50;">{{pap.PAIEMENT}}</td>
                        </tr>
                        <tr>
                            <td style="font-size:14px;font-weight:bold">TYPE PAP</td>
                            <td style="font-size:14px;font-weight:bold;color:#2c3e50;">{{pap.TYPE_PAP}}</td>
                        </tr>
                    </table>
                </l-popup>
                </l-marker>
                </l-map>
            </div>
        </div>
    </div>
</template>

<script>
    import { LMap, LTileLayer, LMarker, LPopup } from "vue2-leaflet";
    export default {
        props:['paps_data'],
        components: {
            LMap,
            LTileLayer,
            LMarker,
            LPopup,
        },
        data () {
            return {
                loading:false,
                axes: {
                    1: {id: 1, val: 'RN7'},
                    2: {id: 2, val: 'RN35'},
                    3: {id: 3, val: 'RRS'},
                },
                selected: '',
                paps:null,
                currentZoom: null,
                currentCenter: null,
                zoom: null,
                center: null,
                //currentZoom:this.paps.zoom,
                //currentCenter: this.paps.center,
                //zoom: this.paps.zoom,
                //center: this.paps.center,
                url: 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
                //url: 'https://tile.thunderforest.com/neighbourhood/{z}/{x}/{y}.png?apikey=64a58efb8ab044cf99d3044640f467dc',
                attribution:'&copy; <a href="https://compact-niger.org/">COMPACT NIGER</a>',                
            }
        },
        created() {
            this.selected = this.paps_data.axe;
            this.currentZoom = this.paps_data.zoom;
            this.currentCenter = this.paps_data.center;
            this.zoom = this.paps_data.zoom;
            this.center = this.paps_data.center;
            this.paps = this.paps_data.paps;
        },
        methods: {
            loadMap: function(url){
                location.replace(url);
            },
            latLng: function(lat,lng){
                return L.latLng(lat,lng);
            },
            updateZoom: function(zoom){
                this.currentZoom = zoom;
            },
            updateCenter: function(center){
                this.currentCenter = center;
            }
        },
        beforeDestroy: function(){
        },
        computed: {
            getErrors() {
                return this.errors;
            }
        }
    }
</script>


