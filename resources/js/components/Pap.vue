<template>
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Gestion des PAPs</h1>
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
                <button v-show="$can('generate')" type="button" class="btn btn-primary float-right ml-3" @click.prevent="generateMultiple" :disabled="loading_generation">
                    <template v-if="!loading_generation"> GENERER UN PDF POUR TOUS LE RESULTAT</template>
                    <template v-if="loading_generation"><i class='fa fa-spin fa-spinner'></i> EN COURS DE GENERATION...</template>
                </button>
                <button v-show="$can('generate')" type="button" class="btn btn-success float-left" @click.prevent="excell">
                    GENERER UN EXCELL POUR TOUS LE RESULTAT
                </button>
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
                            <th>ACTIONS</th>
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
                            <th>ACTIONS</th>
                        </tr>
                        </tfoot>
                        <tbody>
                        <tr class="text-center" v-show="paps.length" v-for="(pap, index) in paps" :key="pap.id">
                            <td class="align-middle">{{pap.ID_PAP}}</td>
                            <td class="align-middle">
                                <img width="60" :src="pap.PHOTO" alt="">
                                <button v-show="$can('delete')" class="btn btn-sm btn-danger text-center" @click.prevent="deletePhoto(pap.ID_PAP)"><i class="fa fa-trash"></i></button>
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
                            <td class="align-middle">
                                <table class='table borderless text-center'>
                                    <tr>
                                        <td><button class="btn btn-sm btn-warning" @click.prevent="edit(pap)"><i class="fa fa-edit"></i></button></td>
                                        <td><button v-show="$can('generate')" class="btn btn-sm btn-primary" @click.prevent="generateSingle(pap.ID_PAP)"><i class="fa fa-download"></i></button></td>
                                    </tr>
                                    <tr>
                                        <td><button class="btn btn-sm btn-success" @click.prevent="openFolder(pap.ID_PAP)"><i class="fa fa-folder-open"></i></button></td>
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
                        <h4  class="title largeModalLabel">Modifier une PAP</h4>
                        <button type="button" class="close" @click.prevent="resetForm" aria-label="Fermer" :disabled="loading">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row clearfix">
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>PRENOM</label>
                                    <input type="text" v-model="form.PRENOM_OCCUP" class="form-control" placeholder="Entrer le prenom" />
                                    <span class="text-danger" v-if="getErrors.PRENOM_OCCUP">
                                        {{ getErrors.PRENOM_OCCUP[0] }}
                                    </span>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>NOM</label>
                                    <input type="text" v-model="form.NOM_OCCUP" class="form-control" placeholder="Entrer le nom" />
                                    <span class="text-danger" v-if="getErrors.NOM_OCCUP">
                                        {{ getErrors.NOM_OCCUP[0] }}
                                    </span>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>SURNOM</label>
                                    <input type="text" v-model="form.SURNOM_OCCUP" class="form-control" placeholder="Entrer le surnom" />
                                    <span class="text-danger" v-if="getErrors.SURNOM_OCCUP">
                                        {{ getErrors.SURNOM_OCCUP[0] }}
                                    </span>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>AGE</label>
                                    <input type="number" v-model="form.AGE" class="form-control" placeholder="Entrer l'âge" />
                                    <span class="text-danger" v-if="getErrors.AGE">
                                        {{ getErrors.AGE[0] }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="row clearfix">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <div class="upload-btn-wrapper">
                                        <label>Charger une image</label>
                                        <button class="btn-upload"><i class="fa fa-image">  Choisir</i></button>
                                        <input type="file" ref="image" id="file" accept="image/*" @change="handleImageUpload"><br>
                                        <span class="text-danger" v-if="getErrors.image">
                                            {{ getErrors.image[0] }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <template>
                                <div class="col-sm-4">
                                    <label>Image actuelle</label>
                                    <img height="100" :src="'photopap/'+form.ID_PAP+'.jpg'" alt="">
                                </div>
                                <div class="col-sm-4">
                                    <label v-show="showPreview">Nouvelle Image</label>
                                    <img height="100" :src="imagePreview" v-show="showPreview" style="border:dashed 2px gray;padding:5px;"/>
                                </div>
                            </template>
                        </div>

                        <div class="row clearfix">
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>Choisir l'axe</label>
                                    <select v-model="selected_axe" class='form-control' >
                                        <option v-for='(a,key) in geo.axes'  :value='a.NUM_AXE' >{{ a.NOM_AXE }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>Choisir la localité</label>
                                    <select v-model="selected_localite"  class='form-control'>
                                        <option v-for='(l,index) in geo.localites' :selected="l.NOM_LOCALITE === form.NOM_LOCALITE ? true : false" :value="l.NOM_LOCALITE+'.'+l.ID_LOC" :key="index">{{ l.NOM_LOCALITE }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <label>Choisir la section</label>
                                <select v-model="selected_section" class='form-control'>
                                    <option v-for='s in geo.sections' :selected="s.SECTION === form.SECTION ? true : false" :value='s.SECTION'>{{ s.SECTION }}</option>
                                </select>
                            </div>
                            <div class="col-sm-3">
                                <label>Choisir la commune</label>
                                <select class='form-control' v-model="selected_commune" >
                                    <option v-for='c in geo.communes' :selected="c.NOM_COMMUNE === form.NOM_COMMUNE ? true : false" :value='c.NUM_COMMUNE'>{{ c.NOM_COMMUNE }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="row clearfix">
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>Sexe</label>
                                    <select v-model="form.SEXE" class='form-control' >
                                        <option value="F">Femme</option>
                                        <option value="M">Homme</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>N° PIECE IDENTITE</label>
                                    <input type="text" v-model="form.NUM_PIECE_OCCUP" class="form-control" placeholder="Entrer le numéro de la pièce" />
                                    <span class="text-danger" v-if="getErrors.NUM_PIECE_OCCUP">
                                        {{ getErrors.NUM_PIECE_OCCUP[0] }}
                                    </span>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>CONTACT 1</label>
                                    <input type="text" v-model="form.CONTACT_1" class="form-control" placeholder="Entrer le contact 1" />
                                    <span class="text-danger" v-if="getErrors.CONTACT_1">
                                        {{ getErrors.CONTACT_1[0] }}
                                    </span>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>CONTACT 2</label>
                                    <input type="text" v-model="form.CONTACT_OCUPANT_1" class="form-control" placeholder="Entrer le contact 2" />
                                    <span class="text-danger" v-if="getErrors.CONTACT_OCUPANT_1">
                                        {{ getErrors.CONTACT_OCUPANT_1[0] }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="row clearfix">
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>POINT X</label>
                                    <input type="text" v-model="form.POINT_X" class="form-control" placeholder="Entrer le pont x" />
                                    <span class="text-danger" v-if="getErrors.POINT_X">
                                        {{ getErrors.POINT_X[0] }}
                                    </span>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>POINT Y</label>
                                    <input type="text" v-model="form.POINT_Y" class="form-control" placeholder="Entrer le point y" />
                                    <span class="text-danger" v-if="getErrors.POINT_Y">
                                        {{ getErrors.POINT_Y[0] }}
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
        <div class="modal fade" style="margin-top: 5px;" id="download-link" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog modal-md" role="document">
                <div class="modal-content">
                    <div class="modal-header mb-4">
                        <h4  class="title largeModalLabel">TELECHARGER LE DOCUMENT</h4>
                        <button type="button" class="close" @click.prevent="resetDownload" aria-label="Fermer" :disabled="loading">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body text-center">
                        <a v-show="$can('download')"
                            class="btn btn-success btn-round btn-default"
                            :href="link"
                            @click.prevent="download" >TELECHARGER</a>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-warning btn-simple btn-round" @click.prevent="resetDownload">ANNULER</button>
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
                image: '',
                showPreview: false,
                imagePreview: '',
                link:'',
                showhide: false
            }
        },
        mounted() {
            //this.list();
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
            excell(){
                if(this.where !== '' && this.search !== ''){
                    window.open(`/excell/${this.where}/${this.search}/generate`, "_blank");

                }
            },
            openFolder(idpap){
                window.open(`/dossier-pap/${idpap}`, "_blank");
            },
            generateSingle(idpap){
                window.open(`/file/${'occupant.ID_PAP'}/${idpap}/generate`, "_blank");
            },
            generateMultiple(){
                if(this.where !== '' && this.search !== ''){
                    this.loading_generation = true;
                    axios.get(`/file/${this.where}/${this.search}/generate-multiple`,{params: {where: this.where,search: this.search}}).then(response => {
                        this.link = response.data;
                        this.loading_generation = false;
                        $('#download-link').appendTo('body').modal('show');
                    });
                }
            },
            resetDownload(){
                this.link = '';
                $('#download-link').appendTo('body').modal('hide');
            },
            download(){
                axios.get(this.link, { responseType: 'blob' })
                    .then(response => {
                        const blob = new Blob([response.data], { type: 'application/pdf' })
                        const link = document.createElement('a')
                        link.href = URL.createObjectURL(blob)
                        link.click()
                        URL.revokeObjectURL(link.href)
                    }).catch()
            },
            deletePhoto(id){
                swal({
                    title: "Êtes-vous sûr?",
                    text: "Vous ne pourrez pas récupérer cette image après supression!",
                    type: "error",
                    showCancelButton: true,
                    confirmButtonText: 'Confirmer',
                    cancelButtonText: 'Annuler',
                    showCloseButton: true,
                    showLoaderOnConfirm: true
                }, () => {
                    axios.get('/pap/'+id+'/image').then(response => {
                        this.refresh();
                        toastr['success']("L'image a bien été supprimé", '', {timeOut: 5000, closeButton: true});
                    }).catch(error => {
                        this.toast(error);
                    });
                });
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
                $('#pap-store').appendTo('body').modal('show');
            },
            save(){
                this.loading = true;
                this.errors = [];
                let formData = new FormData();
                formData.append('image', this.$refs.image.files[0] ? this.$refs.image.files[0] : '');
                formData.append('NOM_OCCUP', this.form.NOM_OCCUP);
                formData.append('PRENOM_OCCUP', this.form.PRENOM_OCCUP);
                formData.append('SURNOM_OCCUP', this.form.SURNOM_OCCUP);
                formData.append('AGE', this.form.AGE);
                formData.append('SEXE', this.form.SEXE);
                formData.append('NUM_PIECE_OCCUP', this.form.NUM_PIECE_OCCUP);
                formData.append('CONTACT_1', this.form.CONTACT_1);
                formData.append('CONTACT_OCUPANT_1', this.form.CONTACT_OCUPANT_1);
                formData.append('NUM_AXE', this.selected_axe);
                formData.append('NOM_LOCALITE', this.selected_localite);
                formData.append('SECTION', this.selected_section);
                formData.append('NUM_COMMUNE', this.selected_commune);
                formData.append('ID_PAP', this.form.ID_PAP);
                formData.append('POINT_X', this.form.POINT_X);
                formData.append('POINT_Y', this.form.POINT_Y);
                axios.post("/pap/update", formData, {headers: {'Content-Type': 'multipart/form-data'}}).then(response => {
                    this.resetForm();
                    this.refresh();
                    toastr['success']("La PAP a bien été mis à jour", '', {timeOut: 5000, closeButton: true});
                    this.loading = false;
                }).catch(error => {
                    this.loading = false;
                    this.toast(error);
                });
            },
            handleImageUpload(){
                this.image = this.$refs.image.files[0];
                let reader  = new FileReader();
                reader.addEventListener("load", function () {
                    this.showPreview = true;
                    this.imagePreview = reader.result;
                }.bind(this), false);
                if(this.image){
                    if ( /\.(jpe?g|png|gif)$/i.test( this.image.name ) ) {
                        reader.readAsDataURL( this.image );
                    }
                }
            },
            resetForm(){
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
