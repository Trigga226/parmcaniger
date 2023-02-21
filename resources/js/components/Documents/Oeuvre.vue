<template>
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Gestion des RAPPORTS DE MISE EN OEUVRE</h1>
        </div>
        <div class="row clearfix mb-4">
            <div class="col-sm-12">
                <button v-show="$can('create')" class="btn btn-success" @click.prevent="store()"><i class="fa fa-upload"></i> Ajouter un document</button>
            </div>
        </div>
        <div class="row clearfix">
            <div class="col-sm-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">RAPPORTS DE MISE EN OEUVRE</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr class="table-yellow">
                                        <th style="width: 20%">NOM</th>
                                        <th style="width: 55%">DESCRIPTION</th>
                                        <th style="width: 15%">DATE</th>
                                        <th style="width: 10%">ACTION</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="table-green" v-show="docs4_data.length" v-for="(doc, index) in docs4_data" :key="doc.id">
                                        <td>{{doc.name}}</td>
                                        <td>{{doc.about}}</td>
                                        <td>{{doc.date}}</td>
                                        <td>
                                            <table>
                                                <tr>
                                                    <td><a v-show="$can('download')" :href="doc.file" download class="btn btn-secondary btn-sm"> <i class="fa fa-download"></i></a></td>
                                                    <td><button v-show="$can('delete')" @click.prevent="destroy(doc)" class="btn btn-danger btn-sm"><i class="fa fa-trash-alt"></i></button></td>
                                                </tr>
                                                <tr>
                                                    <td><button v-show="$can('update')" @click.prevent="edit(doc)" class="btn btn-warning btn-sm" ><i class="fa fa-edit"></i></button></td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" style="margin-top: 5px;" id="file-store" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header mb-4">
                        <h4 v-if="!isEditing" class="title largeModalLabel">Ajouter un document</h4>
                        <h4 v-if="isEditing" class="title largeModalLabel">Modifier un document</h4>
                        <button type="button" class="close" @click.prevent="resetForm" aria-label="Fermer" :disabled="loading">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row clearfix">
                           <div class="col-sm-3">
                            <div class="form-group">
                                <label>NON DU DOCUMENT</label>
                                <input type="text" v-model="form.name" class="form-control" placeholder="Entrer le nom du document" />
                                <span class="text-danger" v-if="getErrors.name">
                                    {{ getErrors.name[0] }}
                                </span>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label>DATE DE PRODUCTION</label>
                                <input type="date" v-model="form.date" class="form-control" placeholder="Entrer la date de production" />
                                <span class="text-danger" v-if="getErrors.date">
                                    {{ getErrors.date[0] }}
                                </span>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label>CHOISIR UNE PIECE JOINTE</label>
                                <input type="file" ref="file" @change="handleFileUpload">
                                <span class="text-danger" v-if="getErrors.file">
                                {{ getErrors.file[0] }}
                            </span>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label>CHOISIR UNE CATEGORIE</label>
                                <select v-model="form.category" ref="select" class="form-control">
                                    <option v-for="c in categories" :value="c.category">{{c.value}}</option>
                                </select>
                                <span class="text-danger" v-if="getErrors.category">
                                {{ getErrors.category[0] }}
                            </span>
                            </div>
                        </div>
                    </div>
                    <div class="row clearfix">
                        <div class="col-sm-12">
                            <div class="form-group">
                            <label>DESCRIPTION</label>
                            <textarea name="" id="" v-model="form.about" rows="2" cols="150" placeholder="Description" class="form-control"></textarea>
                             <span class="text-danger" v-if="getErrors.about">
                                {{ getErrors.about[0] }}
                            </span>
                        </div>
                        </div>
                    </div>
                    </div>
                    <div class="modal-footer">
                        <button v-if="isEditing" type="button" class="btn btn-success btn-round btn-default" @click.prevent="update" :disabled="loading">
                            <template v-if="!loading">METTRE À JOUR</template>
                            <template v-if="loading"><i class='fa fa-spin fa-spinner'></i> EN COURS DE MISE À JOUR...</template>
                        </button>
                        <button v-if="!isEditing" type="button" class="btn btn-default btn-round btn-success" @click.prevent="upload" :disabled="loading">
                            <template v-if="!loading">SAUVEGARDER</template>
                            <template v-if="loading"><i class='fa fa-spin fa-spinner'></i> EN COURS DE SAUVEGARDE...</template>
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
        props:['docs4_data'],
        data () {
            return {
                errors: [],
                loading:false,
                form:{name:'',date:'',category:'',about:''},
                categories: [{category:'RAPPORTS DEFINITIFS DES PAR',value:'RAPPORTS DEFINITIFS DES PAR'},{category:'RAPPORTS DES LIVRABLES',value:'RAPPORTS DES LIVRABLES'},{category:'REFRENTIELS SIG ET CARTOGRAPHIE',value:'REFRENTIELS SIG ET CARTOGRAPHIE'},{category:'RAPPORTS DE MISE EN OEUVRE',value:'RAPPORTS DE MISE EN OEUVRE'},{category:'ARRETES ET DECRETS',value:'ARRETES ET DECRETS'}],
                loading:false,
                pap:null,
                file:'',
                isEditing:false
            }
        },
        mounted() {

        },
        methods: {
            store(){
                $('#file-store').appendTo('body').modal('show');
            },
            upload(){
                this.loading = true;
                this.errors = [];
                let formData = new FormData();
                formData.append('name', this.form.name);
                formData.append('date', this.form.date);
                formData.append('about', this.form.about);
                formData.append('category', this.form.category);
                formData.append('file', this.file);
                axios.post("/documentation/creation", formData, {headers: {'Content-Type': 'multipart/form-data'}}).then(response => {
                    this.resetForm();
                    location.reload();
                    toastr['success']("Le document a bien été ajouté", '', {timeOut: 5000, closeButton: true});
                    this.loading = false;
                    location.reload();
                }).catch(error => {
                    this.errors = error.response.data.errors;
                    this.loading = false;
                    this.toast(error);
                });
            },
            update(){
                this.loading = true;
                this.errors = [];
                let formData = new FormData();
                formData.append('name', this.form.name);
                formData.append('date', this.form.date);
                formData.append('about', this.form.about);
                formData.append('category', this.form.category);
                formData.append('file', this.file);
                axios.post("/documentation/"+this.form.id+"/update", formData, {headers: {'Content-Type': 'multipart/form-data'}}).then(response => {
                    this.resetForm();
                    location.reload();
                    toastr['success']("Le document a bien été mis à jour", '', {timeOut: 5000, closeButton: true});
                    this.loading = false;
                    location.reload();
                }).catch(error => {
                    this.errors = error.response.data.errors;
                    this.loading = false;
                    this.toast(error);
                });
            },
            edit(doc){
                this.isEditing = true;
                this.form = doc;
                $('#file-store').appendTo('body').modal('show');
            },
            destroy(doc){
                swal({
                    title: "Êtes-vous sûr?",
                    text: "Vous ne pourrez pas récupérer le document après supression!",
                    type: "error",
                    showCancelButton: true,
                    confirmButtonText: 'Confirmer',
                    cancelButtonText: 'Annuler',
                    showCloseButton: true,
                    showLoaderOnConfirm: true
                }, () => {
                    axios.get('/documentation/'+doc.id+'/supression' ).then(response => {
                        location.reload();
                    }).catch(error => {
                        this.toast(error);
                    });
                });
            },
            resetForm(){
                this.form = {name:'',date:'',category:'',about:''};
                this.file = '';
                $('#file-store').modal('hide');;
            },
            handleFileUpload(){
                this.file = this.$refs.file.files[0];
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