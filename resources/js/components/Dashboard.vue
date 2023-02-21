<template>
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Tableau de bord</h1>
            <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i class="fas fa-download fa-sm text-white-50"></i> #</a>
        </div>
        <div class="row">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Personnes affectées par le projet</div>
                                <div class="h6 mb-0 font-weight-bold text-gray-800">
                                    <p>{{stats_data[0][0]['total_f']}} FEMMES</p>
                                    <p>{{stats_data[0][0]['total_m']}} HOMMES</p>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-users fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-succeProtuitsss text-uppercase mb-1">Localités et Communes</div>
                                <div class="h6 mb-0 font-weight-bold text-gray-800">
                                    <p>{{locations_nbr}} LOCALITÉS</p>    
                                    <p>{{communes_nbr}} COMMUNES</p>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-map fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">COMPENSATIONS</div>
                                <div class="h6 mb-0 font-weight-bold text-gray-800">
                                    <p>{{stats_data[0][0]['total_compensation_rn7']}} FCFA POUR RN7</p>
                                    <p>{{stats_data[0][0]['total_compensation_rrs']}} FCFA POUR RRS</p>
                                    <p>{{stats_data[0][0]['total_compensation_rn35']}} FCFA POUR RN35</p>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas money-bill-wave fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">PAP PAR TYPES DE PERTES</div>
                                <div class="row no-gutters align-items-center">
                                    <div class="col-auto">
                                        <div class="h6 mb-0 mr-3 font-weight-bold text-gray-800">
                                            <p>{{stats_data[0][0]['total_type_agr']}} : AGRICOLE</p>
                                            <p>{{stats_data[0][0]['total_type_hab']}} : HABITAT</p>
                                            <p>{{stats_data[0][0]['total_type_act']}} : ACTVITÉ ÉCONOMIQUE</p>
                                            <p>{{stats_data[0][0]['total_type_equ']}} : EQUIPEMENT</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas list-ul fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row clearfix">
            <div class="col-sm-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Rapports Définitifs des Plans d'Actions de Réinstallation</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th style="width: 20%">NOM</th>
                                        <th style="width: 55%">DESCRIPTION</th>
                                        <th style="width: 15%">DATE</th>
                                        <th style="width: 10%">TÉLÉCHARGER</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th style="width: 20%">NOM</th>
                                        <th style="width: 55%">DESCRIPTION</th>
                                        <th style="width: 15%">DATE</th>
                                        <th style="width: 10%">TÉLÉCHARGER</th>
                                    </tr>
                                </tfoot>
                                <tbody>
                                    <tr>
                                        <td>PAR RN 7</td>
                                        <td>Plan d’Action de Réinstallation de la réhabilitation du tronçon Dosso-Bella de la RN 7</td>
                                        <td>Septembre 2020</td>
                                        <td><a v-show="$can('download')" :href="'/telecharger/par_rn_7'" download class="btn btn-secondary">TÉLÉCHARGER</a></td>
                                    </tr>
                                     <tr>
                                        <td>PAR RN 35</td>
                                        <td>Plan d’Action de Réinstallation de la réhabilitation de la RN 35</td>
                                        <td>Septembre 2020</td>
                                        <td><a v-show="$can('download')" :href="'/telecharger/par_rn_35'" download class="btn btn-secondary">TÉLÉCHARGER</a></td>
                                    </tr>
                                     <tr>
                                        <td>PAR RRS</td>
                                        <td>Plan d’Action de Réinstallation de la Route Rurale de Sambéra (RRS)</td>
                                        <td>Septembre 2020</td>
                                        <td><a v-show="$can('download')" :href="'/telecharger/par_rrs'" download class="btn btn-secondary">TÉLÉCHARGER</a></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Statistiques des sections par axes</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <div class="row mb-2" v-for="(a,index) in stats_data[1]"  :key="index" >
                                <div class="col-sm-12">
                                    <h5>AXE : {{a.axe}}</h5>
                                    <table class="table table-striped table-bordered mt-3">
                                        <thead>
                                            <tr class="table-warning">
                                                <th scope="col">SECTION</th>
                                                <th scope="col">PAPs</th>
                                                <th scope="col">LOCALITÉ</th>
                                                <th scope="col">COMPENSATION</th>
                                                <th scope="col">HOMMES</th>
                                                <th scope="col">FEMMES</th>
                                                <th align="middle"><span style="padding-bottom:115px;">CLASSE D'AGE 1</span><hr><span class="mt-2">DE 15-34 ANS</span></th>
                                                <th scope="col"><span>CLASSE D'AGE 2</span><hr><span>DE 35-64 ANS</span></th>
                                                <th scope="col"><span>CLASSE D'AGE 3</span><hr><span> > À 65 ANS</span></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr class="table-success" v-for="(s,i) in a.stats"  :key="i">
                                                <td>{{s.section}}</td>
                                                <td>{{s.paps}}</td>
                                                <td>{{s.localite}}</td>
                                                <td>{{s.compensation}}</td>
                                                <td>{{s.homme_p}} %</td>
                                                <td>{{s.femme_p}} %</td>
                                                <td>{{s.jeune}} %</td>
                                                <td>{{s.adulte}} %</td>
                                                <td>{{s.vieux}} %</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <hr>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!--<div class="row">
            <div class="col-sm-4">
                <img width="80%" src="/img/carte.jpg" alt="">
            </div>
            <div class="col-sm-12">
                
            </div>
        </div>-->
    </div>
</template>

<script>
    import axios from 'axios';
    export default {
        props:['locations_nbr','paps_nbr','stats_data','communes_nbr'],
        data () {
            return {
                errors: [],
            }
        },
        mounted() {
        },
        methods: {

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

<style>
    .alert{
        border-radius: 0px;
    }
</style>
