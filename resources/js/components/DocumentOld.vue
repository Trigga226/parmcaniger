<template>
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Gestion des documents</h1>
            <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i class="fas fa-download fa-sm text-white-50"></i> #</a>
        </div>
        <div class="row clearfix">
            <div class="col-sm-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Cartes des emprises par axe et section</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-3"></div>
                            <div class="col-sm-2">
                                <button @click.prevent="changeAxe('rn7')" :disabled="selectedAxe=='rn7'" class="btn btn-success btn-block">AXE RN7</button>
                            </div>
                            <div class="col-sm-2">
                                <button @click.prevent="changeAxe('rn35')" :disabled="selectedAxe=='rn35'" class="btn btn-success btn-block">AXE RN35</button>
                            </div>
                            <div class="col-sm-2">
                                <button @click.prevent="changeAxe('rrs')" :disabled="selectedAxe=='rrs'" class="btn btn-success btn-block">AXE RRS</button>
                            </div>
                            <div class="col-sm-3"></div>
                        </div>
                        <div v-show="selectedAxe=='rn7'" class="row text-center mt-4">
                            <div class="col-sm-12" :key="index" v-for="(s,index) in cartes_data.rn7">
                                <h3>{{cartes_data.rn7[index][0].toUpperCase()}}</h3>
                                <figure @click.prevent="showImage('RN7',cartes_data.rn7[index][0].toUpperCase(),cartes_data.rn7[index][1][key].split('.')[0].toUpperCase(),'/cartes/cartesrn7/'+cartes_data.rn7[index][0]+'/'+cartes_data.rn7[index][1][key])"  class="figure" style="width:100px;margin-right:10px;" :key="key" v-for="(i,key) in cartes_data.rn7[index][1]">
                                    <img :src="'/cartes/cartesrn7/'+cartes_data.rn7[index][0]+'/'+cartes_data.rn7[index][1][key]" class="figure-img img-fluid rounded">
                                    <figcaption class="figure-caption">{{cartes_data.rn7[index][1][key].split('.')[0].toUpperCase()}}</figcaption>
                                </figure>
                                <hr>
                            </div>
                        </div>
                        <div v-show="selectedAxe=='rn35'" class="row text-center mt-4">
                            <div class="col-sm-12" :key="index" v-for="(s,index) in cartes_data.rn35">
                                <h3>{{cartes_data.rn35[index][0].toUpperCase()}}</h3>
                                <figure @click.prevent="showImage('RN35',cartes_data.rn35[index][0].toUpperCase(),cartes_data.rn35[index][1][key].split('.')[0].toUpperCase(),'/cartes/cartesrn35/'+cartes_data.rn35[index][0]+'/'+cartes_data.rn35[index][1][key])" class="figure" style="width:100px;margin-right:10px;" :key="key" v-for="(i,key) in cartes_data.rn35[index][1]">
                                    <img :src="'/cartes/cartesrn35/'+cartes_data.rn35[index][0]+'/'+cartes_data.rn35[index][1][key]" class="figure-img img-fluid rounded">
                                    <figcaption class="figure-caption">{{cartes_data.rn35[index][1][key].split('.')[0].toUpperCase()}}</figcaption>
                                </figure>
                                <hr>
                            </div>
                        </div>
                        <div v-show="selectedAxe=='rrs'" class="row text-center mt-4">
                            <div class="col-sm-12" :key="index" v-for="(s,index) in cartes_data.rrs">
                                <h3>{{cartes_data.rrs[index][0].toUpperCase()}}</h3>
                                <figure @click.prevent="showImage('RRS',cartes_data.rrs[index][0].toUpperCase(),cartes_data.rrs[index][1][key].split('.')[0].toUpperCase(),'/cartes/cartesrrs/'+cartes_data.rrs[index][1][key])" class="figure" style="width:100px;margin-right:10px;" :key="key" v-for="(i,key) in cartes_data.rrs[index][1]">
                                    <img :src="'/cartes/cartesrrs/'+cartes_data.rrs[index][1][key]" class="figure-img img-fluid rounded">
                                    <figcaption class="figure-caption">{{cartes_data.rrs[index][1][key].split('.')[0].toUpperCase()}}</figcaption>
                                </figure>
                                <hr>
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
                                    <tr class="table-yellow">
                                        <th style="width: 20%">NOM</th>
                                        <th style="width: 55%">DESCRIPTION</th>
                                        <th style="width: 15%">DATE</th>
                                        <th style="width: 10%">TÉLÉCHARGER</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="table-green">
                                        <td>PAR RN 7</td>
                                        <td>Plan d’Action de Réinstallation de la réhabilitation du tronçon Dosso-Bella de la RN 7</td>
                                        <td>Septembre 2020</td>
                                        <td><a :href="'/telecharger/par_rn_7'" download class="btn btn-secondary">TÉLÉCHARGER</a></td>
                                    </tr>
                                     <tr class="table-green">
                                        <td>PAR RN 35</td>
                                        <td>Plan d’Action de Réinstallation de la réhabilitation de la RN 35</td>
                                        <td>Septembre 2020</td>
                                        <td><a :href="'/telecharger/par_rn_35'" download class="btn btn-secondary">TÉLÉCHARGER</a></td>
                                    </tr>
                                     <tr class="table-green">
                                        <td>PAR RRS</td>
                                        <td>Plan d’Action de Réinstallation de la Route Rurale de Sambéra (RRS)</td>
                                        <td>Septembre 2020</td>
                                        <td><a :href="'/telecharger/par_rrs'" download class="btn btn-secondary">TÉLÉCHARGER</a></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row clearfix">
            <div class="col-sm-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Autres rapports</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr class="table-yellow">
                                        <th style="width: 90%">DESCRIPTION</th>
                                        <th style="width: 10%">TÉLÉCHARGER</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="table-success">
                                        <td>DOCUMENTS CARTOGRAPHIQUE  DE LA RN7 RN35 ET RRS</td>
                                        <td><a :href="'/docs/DOCUMENTS CARTOGRAPHIQUE  DE LA RN7 RN35 ET RRS.docx'" download class="btn btn-secondary">TÉLÉCHARGER</a></td>
                                    </tr>
                                     <tr class="table-green">
                                        <td>Métadatabase 3 PAR 18052020</td>
                                        <td><a :href="'/docs/metadatabase 3 PAR 18052020.xlsx'" download class="btn btn-secondary">TÉLÉCHARGER</a></td>
                                     <tr class="table-green">
                                        <td>PV_bd_en_Ligne_28042021_REVUE</td>
                                        <td><a :href="'/docs/PV_bd_en_Ligne_28042021_REVUE.pdf'" download class="btn btn-secondary">TÉLÉCHARGER</a></td>
                                    </tr>
                                    <tr class="table-green">
                                        <td>Rapport d'activité mois de Janvier 2021 MGP BERD</td>
                                        <td><a :href="'/docs/Rapport d activite mois de Janvier 2021 MGP BERD.docx'" download class="btn btn-secondary">TÉLÉCHARGER</a></td>
                                    </tr>
                                    <tr class="table-green">
                                        <td>Rapport d'activité mois de  Mai 2021  MGP BERD- 28_05_21</td>
                                        <td><a :href="'/docs/Rapport d activite mois de  Mai 2021  MGP BERD- 28_05_21.docx'" download class="btn btn-secondary">TÉLÉCHARGER</a></td>
                                    </tr>
                                    <tr class="table-green">
                                        <td>Rapport de démarrage Revue BERD 18_03_19clean</td>
                                        <td><a :href="'/docs/Rapport de demarrageRevue BERD 18_03_19clean.docx'" download class="btn btn-secondary">TÉLÉCHARGER</a></td>
                                    </tr>
                                    <tr class="table-green">
                                        <td>Rapport PEPP et MGP _VP 17-05-2019_clean</td>
                                        <td><a :href="'/docs/Rapport PEPP et MGP _VP 17-05-2019_clean.docx'" download class="btn btn-secondary">TÉLÉCHARGER</a></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" style="margin-top: 5px;" id="openShowImage" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header mb-4">
                        <h4  class="title largeModalLabel">{{currentAxe+' '+currentSection+' '+currentImage}}</h4>
                        <button type="button" class="close" @click.prevent="resetShowImage" aria-label="Fermer" :disabled="loading">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body text-center">
                        <img width="100%" :src="currentSrc" alt="">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-warning btn-simple btn-round" @click.prevent="resetShowImage">ANNULER</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import axios from 'axios';
    export default {
        props:['cartes_data'],
        data () {
            return {
                selectedAxe : 'rn7',
                errors: [],
                search_idpap:'',
                loading:false,
                pap:null,
                currentAxe:'',
                currentSection:'',
                currentImage:'',
                currentSrc:''
            }
        },
        mounted() {
            //console.log(this.cartes_data.rn7.fargol11[0]);
        },
        methods: {
            changeAxe(axe){
                this.selectedAxe = axe;
            },
            searchPap(){
                this.loading = true;
                this.pap = {};
                axios.get('/search/',{params: {where: "occupant.ID_PAP",search: this.search_idpap}}).then(response => {
                    this.pap = response.data[0];
                    this.loading = false;
                });
            },
            showImage(axe,section,image,src){
                this.currentAxe = axe;
                this.currentSection = section;
                this.currentImage = image;
                this.currentSrc = src;
                $('#openShowImage').appendTo('body').modal('show');
            },
            resetShowImage(){
                $('#openShowImage').appendTo('body').modal('hide');
                this.currentAxe = '';
                this.currentSection = '';
                this.currentImage = '';
                this.currentSrc = '';
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

<style>
    .emp-profile{
    padding: 3%;
    margin-top: 3%;
    margin-bottom: 3%;
    border-radius: 0.5rem;
    background: #fff;
}
.profile-img{
    text-align: center;
}
.profile-img img{
    width: 70%;
    height: 100%;
}
.profile-img .file {
    position: relative;
    overflow: hidden;
    margin-top: -20%;
    width: 70%;
    border: none;
    border-radius: 0;
    font-size: 15px;
    background: #212529b8;
}
.profile-img .file input {
    position: absolute;
    opacity: 0;
    right: 0;
    top: 0;
}
.profile-head h5{
    color: #333;
}
.profile-head h6{
    color: #0062cc;
}
.profile-edit-btn{
    border: none;
    border-radius: 1.5rem;
    width: 70%;
    padding: 2%;
    font-weight: 600;
    color: #6c757d;
    cursor: pointer;
}
.proile-rating{
    font-size: 12px;
    color: #818182;
    margin-top: 5%;
}
.proile-rating span{
    color: #495057;
    font-size: 15px;
    font-weight: 600;
}
.profile-head .nav-tabs{
    margin-bottom:5%;
}
.profile-head .nav-tabs .nav-link{
    font-weight:600;
    border: none;
}
.profile-head .nav-tabs .nav-link.active{
    border: none;
    border-bottom:2px solid #0062cc;
}
.profile-work{
    padding: 14%;
    margin-top: -15%;
}
.profile-work p{
    font-size: 12px;
    color: #818182;
    font-weight: 600;
    margin-top: 10%;
}
.profile-work a{
    text-decoration: none;
    color: #495057;
    font-weight: 600;
    font-size: 14px;
}
.profile-work ul{
    list-style: none;
}
.profile-tab label{
    font-weight: 600;
}
.profile-tab p{
    font-weight: 600;
    color: #0062cc;
}
</style>
