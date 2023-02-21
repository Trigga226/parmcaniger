<template>
    <div class="container-fluid">
        <div class="header">
            <h4><strong>Gestion des profils</strong></h4>
        </div>
        <div class="row bg-white mt-4 mb-4">
            <div class="col-sm-12">
                <button type="button" class="btn  btn-secondary mb-4 float-left" @click="back">Retourner aux utilisateurs <i class="fa fa-reply"></i></button>
                <button type="button" class="btn  btn-primary mb-4 float-right" @click="create">Ajouter un profil <i class="fa fa-user"></i></button>
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                        <tr>
                            <th>NOM</th>
                            <th>ACTIONS</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="text-center" v-show="profiles.length" v-for="(profile, index) in profiles" :key="profile.id">
                            <td class="align-middle">{{ profile.name}}</td>
                            <td class="align-middle">
                                <button @click.prevent="edit(profile)" class="btn btn-warning btn-sm" style="width:30px;"><i class="fa fa-edit"></i></button>
                                <button @click.prevent="destroy(profile)" class="btn btn-danger btn-sm" style="width:30px;"><i class="fa fa-trash-alt"></i></button>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="modal fade" style="margin-top: 5px;" id="profile-store" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header mb-4">
                        <h4 v-if="!isEditing" class="title largeModalLabel">Création d'un profil</h4>
                        <h4 v-if="isEditing" class="title largeModalLabel">Modifier un profil</h4>
                        <button type="button" class="close" @click.prevent="resetForm" aria-label="Fermer" :disabled="loading">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row clearfix">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>Nom </label>
                                    <input type="text" v-model="form.name" class="form-control" placeholder="Entrer le nom" />
                                    <span class="text-danger" v-if="getErrors.name">
                                        {{ getErrors.name[0] }}
                                    </span>
                                </div>
                            </div>
                            <div class="col-sm-8 text-center">
                                <label> </label>
                                <button class="btn btn-primary mt-4" type="button" data-toggle="collapse" data-target="#collapsePermissions" aria-expanded="false" aria-controls="collapsePermissions">
                                    Attribuer des permissions
                                </button>
                            </div>
                        </div>
                        <div class="row clearfix">
                            <div class="col-sm-12">
                                <div class="collapse" id="collapsePermissions" style="max-height:300px; margin-top-20px;overflow-y: scroll">
                                    <div class="card card-body" style="">
                                        <label v-for="(p, key) in permissions" style="margin-left: 15px;">
                                            {{ p.name }}
                                            <input type="checkbox" class="checkbox" v-model="form.permissions" :key="key" :value="p.id">
                                        </label>
                                    </div>
                                </div>
                                <span class="text-danger" v-if="getErrors.permissions">
                                    {{ getErrors.permissions[0] }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button v-if="isEditing" type="button" class="btn btn-success btn-round btn-default" @click.prevent="save" :disabled="loading">
                            <template v-if="!loading">METTRE À JOUR</template>
                            <template v-if="loading"><i class='fa fa-spin fa-spinner'></i> EN COURS DE MISE À JOUR...</template>
                        </button>
                        <button v-if="!isEditing" type="button" class="btn btn-default btn-round btn-success" @click.prevent="save" :disabled="loading">
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
        props:['user_permissions'],
        data () {
            return {
                errors: [],
                permissions: [],
                profiles: [],
                form:{name: '',permissions: []},
                pagination:{current_page: 1},
                loading: false,
                isEditing:false,
            }
        },
        mounted() {
            this.fetchProfiles();
            this.permissions = this.user_permissions;
        },
        methods: {
            fetchProfiles(){
                axios.get('/profils/?page='+this.pagination.current_page).then(response => {
                    this.profiles = response.data.data;
                    this.pagination = response.data.meta;
                }).catch(error => {
                    this.toast(error);
                })
            },
            save(){
                let url = '';
                if(!this.isEditing){
                    url = '/profils/nouveau';
                }else {
                    url = '/profils/'+this.form.id+'/edition';
                }
                this.loading = true;
                this.errors = [];
                let formData = new FormData();
                formData.append('name', this.form.name);
                formData.append('permissions', JSON.stringify(this.form.permissions));
                axios.post(url, formData, {headers: {'Content-Type': 'multipart/form-data'}}).then(response => {
                    this.resetForm();
                    toastr['success'](this.isEditing ? "Le profil a bien été créé" : "Le profil a bien été mis à jour", '', {timeOut: 5000, closeButton: true});
                    this.loading = false;
                    this.isEditing = false;
                    this.fetchProfiles();
                }).catch(error => {
                    this.loading = false;
                    this.toast(error);
                });
            },
            edit(profile){
                this.isEditing = true;
                this.form = profile;
                $('#profile-store').appendTo('body').modal('show');
            },
            reload() {
                this.$Progress.start();
                this.fetchProfiles();
                this.$Progress.finish();
            },
            create(){
                $('#profile-store').appendTo('body').modal('show');
            },
            destroy(profile){
                swal({
                    title: "Êtes-vous sûr?",
                    text: "Vous ne pourrez pas récupérer cet profil après supression!",
                    type: "error",
                    showCancelButton: true,
                    confirmButtonText: 'Confirmer',
                    cancelButtonText: 'Annuler',
                    showCloseButton: true,
                    showLoaderOnConfirm: true
                }, () => {
                    axios.get('/profils/'+profile.id+'/supression').then(response => {
                        this.fetchProfiles();
                        toastr['success']("Le profil a bien été supprimé", '', {timeOut: 5000, closeButton: true});
                    }).catch(error => {
                        this.toast(error);
                    });
                });
            },
            toast(error){
                if(error.response.status === 422){
                    this.errors = error.response.data.errors;
                }else if(error.response.status === 403){
                    toastr['warning'](error.response.data.message, 'Vous n\'êtes pas autorisé!', {timeOut: 5000, closeButton: true});
                }else{
                    toastr['error']('Une érreur est survenue!', 'Réessayez plus tard...', {timeOut: 5000, closeButton: true});
                }
            },
            resetForm(){
                this.isEditing = false;
                this.errors = [];
                this.form.name = '';
                this.form.permissions = [];
                this.fetchProfiles();
                $('#profile-store').modal('hide');
            },
            updatePermissions () {
                //this.$emit('input', this.form.permissions);
            },
            back(){
                window.location.replace(`/utilisateurs`);
            },
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
