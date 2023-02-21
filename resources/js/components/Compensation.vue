<template>
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Gestion des Compensations</h1>
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
                
                <button v-show="$can('generate')" type="button" class="btn btn-success float-left" @click.prevent="excell">
                    GENERER UN EXCELL POUR TOUS LE RESULTAT
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered dataTable" id="" width="100%" cellspacing="0">
                        <thead>
                        <tr>
                            <th>PHOTO</th>
                            <th>INFORMATIONS</th>
                            <th>COMPENSATION</th>
                            <th>ACTIONS</th>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <th>PHOTO</th>
                            <th>INFORMATIONS</th>
                            <th>COMPENSATION</th>
                            <th>ACTIONS</th>
                        </tr>
                        </tfoot>
                        <tbody>
                        <tr class="text-center" v-show="paps.length" v-for="(pap, index) in paps" :key="pap.id">
                            <td class="align-middle">
                                <img width="60" :src="pap.PHOTO" alt="">
                            </td>
                            <td class="align-middle">
                                <p><strong>ID : </strong>{{pap.ID_PAP}}</p>
                                <p><strong>PRENOM : </strong>{{pap.PRENOM_OCCUP}}</p>
                                <p><strong>NOM : </strong>{{pap.NOM_OCCUP}}</p>
                                <p><strong>AGE : </strong>{{pap.AGE}} ANS</p>
                                <p class="text-danger"><strong>COMP : {{pap.TOTAL}} FCFA</strong></p>
                            </td>
                            <td class="align-middle">
                                <template v-if="!pap.NUM_DOSSIER_PAP">
                                    <strong class="text-danger">PAS DE NUMERO DE DOSSIER</strong>
                                </template>
                                <template v-else>
                                    <p><strong>NUMERO DU DOSSIER : </strong>{{pap.NUM_DOSSIER_PAP}}</p>
                                <p><strong>ETAT DU DOSSIER : </strong>{{pap.ETAT_DOSSIER}}</p>
                                <p><strong>OBSERVATION : </strong>{{pap.OBSERVATION}}</p>
                                <p v-show="pap.CERTIFIE=='1'"><strong><span class="badge badge-success">DOSSIER CERTIFIE</span></strong></p>
                                <p v-show="pap.CERTIFIE!='1'"><strong><span class="badge badge-warning">DOSSIER NON CERTIFIE</span></strong></p>
                                </template>
                            </td>
                            <td class="align-middle">
                                <table class='table borderless text-center' v-show="pap.NUM_DOSSIER_PAP">
                                    <tr>
                                        <td><button class="btn btn-sm btn-warning" @click.prevent="edit(pap)"><i class="fa fa-edit"></i></button></td>
                                    </tr>
                                    <tr>
                                        <td><button v-show="$can('generate')" class="btn btn-sm btn-primary" @click.prevent="generateSingleComp(pap.ID_PAP)"><i class="fa fa-download"></i></button></td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="modal fade" style="margin-top: 5px;" id="pap-store" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header mb-4">
                        <h4  class="title largeModalLabel">Modifier l'état de compensation</h4>
                        <button type="button" class="close" @click.prevent="resetForm" aria-label="Fermer" :disabled="loading">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row clearfix">
                            <div class="row">
                                <div class="col-sm-12">
                                    <h5 v-show="form.CERTIFIE=='1'">DOSSIER : <span class="badge badge-success">CERTIFIE</span></h5>
                                    <h5 v-show="form.CERTIFIE!='1'">DOSSIER : <span class="badge badge-warning">NON CERTIFIE</span></h5>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="table">
                                    <tr>
                                        <th>IMAGE</th>
                                        <th>ID PAP</th>
                                        <th>PRENOM</th>
                                        <th>NOM</th>
                                        <th>AGE</th>
                                        <th>AXE</th>
                                        <th>LOCALITE</th>
                                        <th>SECTION</th>
                                        <th>COMMUNE</th>
                                        <th>COMPENSATION</th>
                                    </tr>
                                    <tr>
                                        <td>
                                            <img height="100" :src="'photopap/'+form.ID_PAP+'.jpg'" alt="">
                                        </td>
                                        <td class="align-middle">{{form.ID_PAP}}</td>
                                        <td class="align-middle">{{form.PRENOM_OCCUP}}</td>
                                        <td class="align-middle">{{form.NOM_OCCUP}}</td>
                                        <td class="align-middle">{{form.AGE}}</td>
                                        <td class="align-middle">{{form.NOM_AXE}}</td>
                                        <td class="align-middle">{{form.NOM_LOCALITE}}</td>
                                        <td class="align-middle">{{form.SECTION}}</td>
                                        <td class="align-middle">{{form.NOM_COMMUNE}}</td>
                                        <td class="align-middle">{{form.TOTAL}} FCFA</td> 
                                    </tr>
                                </div>
                                <hr>
                            </div>
                        </div>
                        <div class="row clearfix" v-show="$can('init-comp')">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>NUMERO DU DOSSIER</label>
                                    <input type="text" v-model="morf.NUM_DOSSIER_PAP" class="form-control" placeholder="Entrer le numéro du dossier" />
                                    <span class="text-danger" v-if="getErrors.NUM_DOSSIER_PAP">
                                        {{ getErrors.NUM_DOSSIER_PAP[0] }}
                                    </span>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>ETAT DU DOSSIER</label>
                                    <select v-model="morf.ETAT_DOSSIER" class='form-control' >
                                        <option value='NON CONSTITUE' >NON CONSTITUE</option>
                                        <option value='CONSTITUE' >CONSTITUE</option>
                                    </select>
                                    <span class="text-danger" v-if="getErrors.ETAT_DOSSIER">
                                        {{ getErrors.ETAT_DOSSIER[0] }}
                                    </span>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>OBSERVATION</label>
                                    <select v-model="morf.OBSERVATION" class='form-control' >
                                        <option value='NON TRANSMIS' >NON TRANSMIS</option>
                                        <option value='TRANSMIS' >TRANSMIS</option>
                                    </select>
                                    <span class="text-danger" v-if="getErrors.OBSERVATION">
                                        {{ getErrors.OBSERVATION[0] }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="row clearfix" v-show="morf.CERTIFIE=='1'" >
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>PAF ORDRE PAYEMENT</label>                                    
                                    <select v-model="morf.PAF_ORDRE_PAYEMENT" class='form-control' >
                                        <option  :value='0' >NON</option>
                                        <option  :value='1' >OUI</option>
                                    </select>
                                    <span class="text-danger" v-if="getErrors.PAF_ORDRE_PAYEMENT">
                                        {{ getErrors.PAF_ORDRE_PAYEMENT[0] }}
                                    </span>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>PAYEMENT EFFECTIF</label>
                                    <select v-model="morf.PAYMENT_EFFECTIF" class='form-control' >
                                        <option  :value='0' >NON</option>
                                        <option  :value='1' >OUI</option>
                                    </select>
                                    <span class="text-danger" v-if="getErrors.PAYMENT_EFFECTIF">
                                        {{ getErrors.PAYMENT_EFFECTIF[0] }}
                                    </span>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>MODE PAYEMENT</label>                                    
                                    <select v-model="morf.MODE_PAYMENT" class='form-control' >
                                        <option  value="CASH MCA" >CASH MCA</option>
                                        <option  value='CASH BAGRI' >CASH BAGRI</option>
                                        <option  value="VIREMENT" >VIREMENT</option>
                                    </select>
                                    <span class="text-danger" v-if="getErrors.MODE_PAYMENT">
                                        {{ getErrors.MODE_PAYMENT[0] }}
                                    </span>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>PAF PAIEMENT</label>
                                    <input type="text" v-model="morf.DATE_PAYMENT" class="form-control" placeholder="Paf paiement" >
                                    <span class="text-danger" v-if="getErrors.DATE_PAYMENT">
                                        {{ getErrors.DATE_PAYMENT[0] }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="row clearfix" v-show="morf.CERTIFIE=='1'" >
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>MODE PAIEMENT PRMS</label>                                    
                                    <select v-model="morf.MODE_PAYMENT_PRMS" class='form-control' >
                                        <option  value="CASH" >CASH</option>
                                        <option  value="VIREMENT" >VIREMENT</option>
                                    </select>
                                    <span class="text-danger" v-if="getErrors.MODE_PAYMENT_PRMS">
                                        {{ getErrors.MODE_PAYMENT_PRMS[0] }}
                                    </span>
                                </div>
                            </div>
                            
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>OBSERVATION</label>
                                    <textarea v-model="morf.OBS" cols="30" rows="2" class="form-control" placeholder="Observations"></textarea>
                                    <span class="text-danger" v-if="getErrors.OBS">
                                        {{ getErrors.OBS[0] }}
                                    </span>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>PLAINTE</label>
                                    <select v-model="morf.PlAINTE" class='form-control' >
                                        <option  :value='0' >NON</option>
                                        <option  :value='1' >OUI</option>
                                    </select>
                                    <span class="text-danger" v-if="getErrors.PlAINTE">
                                        {{ getErrors.PlAINTE[0] }}
                                    </span>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>COMP_REVISEE</label>
                                    <input type="text" v-model="morf.COMP_REVISEE" class="form-control" placeholder="Compensation révisée" />
                                    <span class="text-danger" v-if="getErrors.COMP_REVISEE">
                                        {{ getErrors.COMP_REVISEE[0] }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button v-show="$can('update')" type="button" class="btn btn-success btn-round btn-default" @click.prevent="save" :disabled="loading">
                            <template v-if="!loading">METTRE À JOUR</template>
                            <template v-if="loading"><i class='fa fa-spin fa-spinner'></i> EN COURS DE MISE À JOUR...</template>
                        </button>
                        <button type="button" class="btn btn-warning btn-simple btn-round" @click.prevent="resetForm" :disabled="loading">ANNULER</button>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</template>

<script>
    import axios from 'axios';
    export default {
        props:['geo'],
        data () {
            return {
                errors: [],
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
                loading:false,
                loading_generation:false,
                form:{},
                morf: {NUM_DOSSIER_PAP: '',ETAT_DOSSIER:'NON CONSTITUE',OBSERVATION:'NON TRANSMIS',PAF_ORDRE_PAYEMENT:'0',PAYMENT_EFFECTIF:'0',MODE_PAYMENT:'CASH',DATE_PAYMENT:'',MODE_PAYMENT_PRMS:'',OBS:'',PlAINTE:'0',COMP_REVISEE:''},
                image: '',
                showPreview: false,
                imagePreview: '',
                link:'',
                showhide: false,
                current_pap:{}
            }
        },
        mounted() {
        },
        methods: {
            showHide(){
                if(this.showhide){
                    this.showhide = false;
                }else{
                    this.showhide = true;
                }
            },
            list(){
                axios.get('/list/paps').then(response => {
                    this.paps = response.data;
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
            
           generateSingleComp(idpap){
                window.open(`/file/compensation/${'occupant.ID_PAP'}/${idpap}/generate`, "_blank");
            },
            resetDownload(){
                this.link = '';
                $('#download-link').appendTo('body').modal('hide');
            },

            excell(){
                if(this.where !== '' && this.search !== ''){
                    window.open(`/excell/compensation/${this.where}/${this.search}/generate`, "_blank");

                }
            },        
            refresh(){
                if (this.search_idpap !== ''){this.searchByIdPap()}
                if (this.search_localite !== ''){this.searchByLocalite()}
                if (this.search_section !== ''){this.searchBySection()}
                if (this.search_commune !== ''){this.searchByCommune()}
                if (this.search_axe !== ''){this.searchByAxe()}
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
            edit(pap){
                this.form = pap;
                this.selected_axe = pap.NUM_AXE;
                this.selected_localite = pap.NOM_LOCALITE+'.'+pap.ID_LOC;
                this.selected_section = pap.SECTION;
                this.selected_commune = pap.NUM_COMMUNE;
                axios.get('/current/papcomp/'+this.form.ID_PAP).then(response => {
                    this.morf = response.data[0];
                    $('#pap-store').appendTo('body').modal('show');
                });
                
            },
            save(){
                this.loading = true;
                this.errors = [];
                let formData = new FormData();
                formData.append('ID_PAP', this.form.ID_PAP);
                formData.append('NUM_DOSSIER_PAP', this.morf.NUM_DOSSIER_PAP);
                formData.append('ETAT_DOSSIER', this.morf.ETAT_DOSSIER);
                formData.append('OBSERVATION', this.morf.OBSERVATION);
                formData.append('PAF_ORDRE_PAYEMENT', this.morf.PAF_ORDRE_PAYEMENT);
                formData.append('PAYMENT_EFFECTIF', this.morf.PAYMENT_EFFECTIF);
                formData.append('MODE_PAYMENT', this.morf.MODE_PAYMENT);
                formData.append('DATE_PAYMENT', this.morf.DATE_PAYMENT);
                formData.append('MODE_PAYMENT_PRMS', this.morf.MODE_PAYMENT_PRMS);
                formData.append('OBS', this.morf.OBS);
                formData.append('PlAINTE', String(this.morf.PlAINTE));
                formData.append('COMP_REVISEE', this.morf.COMP_REVISEE);
                axios.post("/pap/update/compensation", formData, {headers: {'Content-Type': 'multipart/form-data'}}).then(response => {
                    this.resetForm();
                    this.refresh();
                    toastr['success']("La compensation a bien été mis à jour", '', {timeOut: 5000, closeButton: true});
                    this.loading = false;
                }).catch(error => {
                    this.loading = false;
                    this.toast(error);
                });
            },
            
            resetForm(){
                this.form = {};
                this.morf = {NUM_DOSSIER_PAP: '',ETAT_DOSSIER:'NON CONSTITUE',OBSERVATION:'NON TRANSMIS',PAF_ORDRE_PAYEMENT:'0',PAYEMENT_EFFECTIF:'0',MODE_PAYEMENT:'CASH',DATE_PAYEMNT:'',OBS:'',PlAINTE:'0',COMP_REVISEE:''};
                $('#pap-store').modal('hide');
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
    .alert{
        border-radius: 0px;
    }
    .borderless td, .borderless th {
        border: none;
    }
</style>
