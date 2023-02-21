<template>
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Dossier des scans relatif aux PAPs</h1>
            <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i class="fas fa-download fa-sm text-white-50"></i> #</a>
        </div>
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <button @click.prevent="showHide" class="btn btn-success">STRUCTURATION</button>
                </h6>
            </div>
            <div class="card-body" v-show="showhide">
                <div class="row ">
                    <div class="col-sm-4">
                        <h6>AXE : RN7</h6>
                        <table class="table table-bordered">
                            <thead>
                                <th>SECTIONS</th>
                                <th>COMMUNES</th>
                            </thead>
                            <tbody>
                                <tr><td>3DOSSO</td><td>[Dosso]</td></tr>
                                <tr><td>2FARGOL2</td><td>[Farrey-Gollé]</td></tr>
                                <tr><td>1FARGOL1</td><td>[Farrey-Gollé]</td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-sm-4">
                        <h6>AXE : RN35</h6>
                        <table class="table table-bordered">
                            <thead>
                                <th>SECTIONS</th>
                                <th>COMMUNES</th>
                            </thead>
                            <tbody>
                                <tr><td>1BIRFAB</td><td>[Birni N'Goure-Fabidji]</td></tr>
                                <tr><td>7GAYA</td><td>[Gaya]</td></tr>
                                <tr><td>5OUNA</td><td>[Sambera-Tanda]</td></tr>
                                <tr><td>6TANDA</td><td>[Tanda]</td></tr>
                                <tr><td>2FALM2</td><td>[Fabidji-Falmey]</td></tr>
                                <tr><td>3FALM1</td><td>[Falmey]</td></tr>
                                <tr><td>4SAMB</td><td>[Sambera]</td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-sm-4">
                        <h6>AXE : RRS</h6>
                        <table class="table table-bordered">
                            <thead>
                                <th>SECTIONS</th>
                                <th>COMMUNES</th>
                            </thead>
                            <tbody>
                                <tr><td>1RRS1</td><td>[Golle]</td></tr>
                                <tr><td>2RRS2</td><td>[Sambera]</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Zone De Recherches</h6>
            </div>
            <div class="card-body">
                <div class="row ">
                    <div class="col-sm-1"></div>
                    <form class="col-sm-2">
                        <div class="form-group">
                            <label class="text-info">RECHERCHE PAR AXE</label>
                            <input type="text" class="form-control" v-on:keyup="getAxes" v-model="search_axe" placeholder="Tapez la non de l'axe...">
                            <div class="panel-footer" v-if="resAxes.length">
                                <ul class="list-group">
                                    <li class="list-group-item"  v-for="(r,index) in resAxes" v-on:click="selectAxe(r)"  :key="index">{{ r.NOM_AXE }}</li>
                                </ul>
                            </div>
                            <button type="button" class="btn btn-info btn-round btn-default mt-2 btn-block" @click.prevent="searchByAxe" :disabled="loading">
                                <template v-if="!loading">RECHERCHE</template>
                                <template v-if="loading"><i class='fa fa-spin fa-spinner'></i> PATIENTEZ...</template>
                            </button>
                        </div>
                    </form>
                    <form class="col-sm-2">
                        <div class="form-group">
                            <label class="text-info">RECHERCHE PAR SECTION</label>
                            <input type="text" class="form-control" v-on:keyup="getSections" v-model="search_section" placeholder="Tapez la section...">
                            <div class="panel-footer" v-if="resSections.length">
                                <ul class="list-group">
                                    <li class="list-group-item"  v-for="(r,index) in resSections" v-on:click="selectSection(r)"  :key="index">{{ r.SECTION }}</li>
                                </ul>
                            </div>
                            <button type="button" class="btn btn-info btn-round btn-default mt-2 btn-block" @click.prevent="searchBySection" :disabled="loading">
                                <template v-if="!loading">RECHERCHE</template>
                                <template v-if="loading"><i class='fa fa-spin fa-spinner'></i> PATIENTEZ...</template>
                            </button>
                        </div>
                    </form>
                    <form class="col-sm-2">
                        <div class="form-group">
                            <label class="text-info">RECHERCHE PAR COMMUNE</label>
                            <input type="text" class="form-control" v-on:keyup="getCommunes" v-model="search_commune" placeholder="Tapez la commune...">
                            <div class="panel-footer" v-if="resCommunes.length">
                                <ul class="list-group">
                                    <li class="list-group-item"  v-for="(r,index) in resCommunes" v-on:click="selectCommune(r)"  :key="index">{{ r.NOM_COMMUNE }}</li>
                                </ul>
                            </div>
                            <button type="button" class="btn btn-info btn-round btn-default mt-2 btn-block" @click.prevent="searchByCommune" :disabled="loading">
                                <template v-if="!loading">RECHERCHE</template>
                                <template v-if="loading"><i class='fa fa-spin fa-spinner'></i> PATIENTEZ...</template>
                            </button>
                        </div>
                    </form>

                    <form class="col-sm-2">
                        <div class="form-group">
                            <label class="text-info">RECHERCHE PAR LOCALITÉ</label>
                            <input type="text" class="form-control" v-on:keyup="getLocalites" v-model="search_localite" placeholder="Tapez la localité...">
                            <div class="panel-footer" v-if="resLocalites.length">
                                <ul class="list-group">
                                    <li class="list-group-item"  v-for="(r,index) in resLocalites" v-on:click="selectLocalite(r)"  :key="index">{{ r.NOM_LOCALITE }}</li>
                                </ul>
                            </div>
                            <button type="button" class="btn btn-info btn-round btn-default mt-2 btn-block" @click.prevent="searchByLocalite" :disabled="loading">
                                <template v-if="!loading">RECHERCHE</template>
                                <template v-if="loading"><i class='fa fa-spin fa-spinner'></i> PATIENTEZ...</template>
                            </button>
                        </div>
                    </form>
                    <form class="col-sm-2">
                        <div class="form-group">
                            <label class="text-info">RECHERCHE PAR PAP</label>
                            <input type="text" class="form-control" v-model="search_idpap" placeholder="Tapez l'id pap...">
                            <button type="button" class="btn btn-info btn-round btn-default mt-2 btn-block" @click.prevent="searchByIdPap" :disabled="loading">
                                <template v-if="!loading">RECHERCHE</template>
                                <template v-if="loading"><i class='fa fa-spin fa-spinner'></i> PATIENTEZ...</template>
                            </button>
                        </div>
                    </form>
                    
                    
                    
                    
                    <div class="col-sm-1"></div>
                </div>
            </div>
        </div>
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Resultat de recherches</h1>
        </div>
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered dataTable" id="" width="100%" cellspacing="0">
                        <thead>
                        <tr>
                            <th>ID PAP</th>
                            <th>PHOTO</th>
                            <th>CONTACT</th>
                            <th>INFORMATIONS</th>
                            <th>AXE</th>
                            <th>LOCALITE</th>
                            <th>SECTION</th>
                            <th>COMMUNE</th>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <th>ID PAP</th>
                            <th>PHOTO</th>
                            <th>CONTACT</th>
                            <th>INFORMATIONS</th>
                            <th>AXE</th>
                            <th>LOCALITE</th>
                            <th>SECTION</th>
                            <th>COMMUNE</th>
                        </tr>
                        </tfoot>
                        <tbody>
                        <tr class="text-center" v-show="paps.length" v-for="(pap, index) in paps" :key="pap.id">
                            <td class="align-middle">{{pap.ID_PAP}}</td>
                            <td class="align-middle">
                                <img width="60" :src="pap.PHOTO" alt="">
                            </td>
                            <td class="align-middle">
                                <p>{{pap.CONTACT_1}}</p>
                                <p>{{pap.CONTACT_OCUPANT_1}}</p>
                            </td>
                            <td class="align-middle">
                                <p><strong>PRENOM : </strong>{{pap.PRENOM_OCCUP}}</p>
                                <p><strong>NOM : </strong>{{pap.NOM_OCCUP}}</p>
                                <p><strong>AGE : </strong>{{pap.AGE}} ANS</p>
                                <p class="text-danger"><strong>COMP : {{pap.TOTAL}} FCFA</strong></p>
                            </td>
                            <td class="align-middle">{{pap.NOM_AXE}}</td>
                            <td class="align-middle">{{pap.NOM_LOCALITE}}</td>
                            <td class="align-middle">{{pap.SECTION}}</td>
                            <td class="align-middle">{{pap.NOM_COMMUNE}}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="row clearfix">
            <div class="col-sm-8">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">#</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <div class="container emp-profile">
                                <div class="row mb-2">
                                    <div class="col-sm-3">
                                        <form>
                                            <div class="form-group">
                                                <input type="text" class="form-control" v-model="search_idpap" placeholder="Tapez l'id PAP...">
                                                
                                            </div>
                                        </form>
                                    </div>
                                    <div class="col-sm-3">
                                         <button type="button" class="btn btn-info btn-round btn-default" @click.prevent="searchPap" :disabled="loading">
                                            <template v-if="!loading">RECHERCHE</template>
                                            <template v-if="loading"><i class='fa fa-spin fa-spinner'></i> PATIENTEZ...</template>
                                        </button>
                                    </div>
                                </div>
                                <form method="post" v-if="pap">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="profile-img">
                                                <img :src="pap.PHOTO" alt=""/>
                                                <div class="file btn btn-lg btn-primary">
                                                    PHOTO DE LA PAP
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="profile-head">
                                                <h5>
                                                    {{pap.PRENOM_OCCUP}} {{pap.NOM_OCCUP}}
                                                </h5>
                                                <p class="proile-rating">AGE : <span>{{pap.AGE}} ANS</span></p>
                                                <p class="proile-rating">COMP : <span>{{pap.TOTAL}} FCFA</span></p>
                                                <p>Liste des documents</p>
                                                <ul class="nav">
                                                    <li class="nav-item" v-for="(file, index) in pap.FILES">
                                                        <a :href="'/downloadscan/'+pap.ID_PAP+'/'+file" download class="btn btn-secondary mr-2">{{file}}</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </form>           
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import axios from 'axios';
    export default {
        props:[],
        data () {
            return {
                errors: [],
                search_idpap:'',
                loading:false,
                pap:null,
                paps:[],
                search:'',
                search_idpap:'',
                search_axe:'',
                search_localite:'',
                search_section:'',
                search_commune:'',
                selected_axe:'',
                selected_localite:'',
                selected_section:'',
                selected_commune:'',
                resLocalites:[],
                resAxes:[],
                resSections:[],
                resCommunes:[],
                axe:'',
                localite:'',
                section:'',
                commune:'',
                where:'',
                showhide: false
            }
        },
        mounted() {
            let url  = window.location.href;
            let idpap = url.split("/").pop();
            if(idpap && idpap !== 'dossier-pap'){
                this.search_idpap = idpap;
                this.searchPap();
            }
        },
        methods: {
            searchPap(){
                this.loading = true;
                this.pap = {};
                axios.get('/search/',{params: {where: "occupant.ID_PAP",search: this.search_idpap}}).then(response => {
                    this.pap = response.data[0];
                    this.loading = false;
                });
            },
            searchPaps(){
                this.loading = true;
                this.results = [];
                if(this.search.length > 0){
                    axios.get('/search/',{params: {where: this.where,search: this.search}}).then(response => {
                        this.paps = response.data;
                        this.loading = false;
                    });
                }
            },
             searchByIdPap(){
                this.search_localite = "";
                this.search_axe = "";
                this.search_section = "";
                this.search_commune = "";
                this.search = this.search_idpap;
                this.where = "occupant.ID_PAP";
                this.searchPaps();
            },
            searchByAxe(){
                this.search_localite = "";
                this.search_idpap = "";
                this.search_section = "";
                this.search_commune = "";
                this.search = this.search_axe;
                this.where = "axe.NOM_AXE";
                this.searchPaps();
            },
            searchByLocalite(){
                this.search_axe = "";
                this.search_idpap = "";
                this.search_section = "";
                this.search_commune = "";
                this.search = this.search_localite;
                this.where = "localite.NOM_LOCALITE";
                this.searchPaps();
            },
            searchBySection(){
                this.search_axe = "";
                this.search_idpap = "";
                this.search_localite = "";
                this.search_commune = "";
                this.search = this.search_section;
                this.where = "localite.SECTION";
                this.searchPaps();
            },
            searchByCommune(){
                this.search_axe = "";
                this.search_idpap = "";
                this.search_section = "";
                this.search_localite = "";
                this.search = this.search_commune;
                this.where = "commune.NOM_COMMUNE";
                this.searchPaps();
            },
            getLocalites(){
                this.resLocalites = [];
                if(this.search_localite.length > 0){
                    axios.get('/search/localite',{params: {search: this.search_localite}}).then(response => {
                        this.resLocalites = response.data;
                    });
                }
            },
            selectLocalite(current){
                this.search_localite = current.NOM_LOCALITE;
                this.resLocalites = [];
            },
            getAxes(){
                this.resAxes = [];
                if(this.search_axe.length > 0){
                    axios.get('/search/axe',{params: {search: this.search_axe}}).then(response => {
                        this.resAxes = response.data;
                    });
                }
            },
            selectAxe(current){
                this.search_axe = current.NOM_AXE;
                this.resAxes = [];
            },
            getSections(){
                this.resSections = [];
                if(this.search_section.length > 0){
                    axios.get('/search/section',{params: {search: this.search_section}}).then(response => {
                        this.resSections = response.data;
                    });
                }
            },
            selectSection(current){
                this.search_section = current.SECTION;
                this.resSections = [];
            },
            getCommunes(){
                this.resCommunes = [];
                if(this.search_commune.length > 0){
                    axios.get('/search/commune',{params: {search: this.search_commune}}).then(response => {
                        this.resCommunes = response.data;
                    });
                }
            },
            selectCommune(current){
                this.search_commune = current.NOM_COMMUNE;
                this.resCommunes = [];
            },
            showHide(){
                if(this.showhide){
                    this.showhide = false;
                }else{
                    this.showhide = true;
                }
            },
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
