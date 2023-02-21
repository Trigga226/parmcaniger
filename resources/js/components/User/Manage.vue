<template>
    <div class="container-fluid">
        <div class="header">
            <h4><strong>Gestion des utilisateurs</strong></h4>
        </div>
        <div class="row bg-white">
            <div class="col-sm-2 mt-2">
                <strong class="">Recherche : </strong>
            </div>
            <div class="col-sm-3">
                <select v-model="queryFiled" class="form-control">
                    <option v-for="q in queryOptions" :value="q.field">{{q.value}}</option>
                </select>
            </div>
            <div class="col-sm-7">
                <div class="form-group">
                    <div class="input-group mb-0">
                        <input v-model="query" @keyup.enter="searchUsers" type="text" class="form-control" placeholder="Rechercher...">
                        <div class="input-group-append">
                            <span class="input-group-text" @click.prevent="searchUsers"><i class="fa fa-search"></i></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row bg-white mt-4 mb-4">
            <div class="col-sm-12">
                <button type="button" class="btn  btn-info mb-4 float-left" @click="goProfile">Gérer les profiles <i class="fa fa-user-cog"></i></button>
                <button type="button" class="btn  btn-primary mb-4 float-right" @click="create">Ajouter un utilisateur <i class="fa fa-user"></i></button>
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                        <tr>

                            <th>IMAGE</th>
                            <th>NOM COMPLET</th>
                            <th>EMAIL</th>
                            <th>TÉLÉPHONE</th>
                            <th>ACTIONS</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="text-center" v-show="users.length" v-for="(user, index) in users" :key="user.id">
                            <td class="align-middle"><img width="60" :src="user.avatar" alt=""></td>
                            <td class="align-middle">{{ user.name}}</td>
                            <td class="align-middle">{{user.email}}</td>
                            <td class="align-middle">{{user.phone}}</td>
                            <td class="align-middle">
                                <button @click.prevent="edit(user)" class="btn btn-warning btn-sm" style="width:30px;"><i class="fa fa-edit"></i></button>
                                <button @click.prevent="destroy(user)" class="btn btn-danger btn-sm" style="width:30px;"><i class="fa fa-trash-alt"></i></button>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="modal fade" style="margin-top: 5px;" id="user-store" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header mb-4">
                        <h4 v-if="!isEditing" class="title largeModalLabel">Création d'un utilisateur</h4>
                        <h4 v-if="isEditing" class="title largeModalLabel">Modifier un utilisateur</h4>
                        <button type="button" class="close" @click.prevent="resetForm" aria-label="Fermer" :disabled="loading">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div v-if="!isEditing" class="alert alert-info" role="alert">
                        <div class="container text-center">
                            <div class="alert-icon">
                                <i class="fa fa-info-circle"></i>
                            </div>
                            <strong>Un mot de passe lui sera envoyé par email!</strong>
                        </div>
                    </div>
                    <div v-if="isEditing" class="alert alert-info" role="alert">
                        <div class="container text-center">
                            <div class="alert-icon">
                                <i class="fa fa-question-circle"></i>
                            </div>
                            <strong>Réinitialiser et envoyer un nouveau mot de passe par email!</strong>
                            <div class="checkbox">
                                <input id="change" type="checkbox" v-model="password">
                                <label for="change">
                                    Réinitialiser
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-body">
                        <div class="row clearfix">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>Nom complet</label>
                                    <input type="text" v-model="form.name" class="form-control" placeholder="Entrer le nom" />
                                    <span class="text-danger" v-if="getErrors.name">
                                        {{ getErrors.name[0] }}
                                    </span>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" v-model="form.email" class="form-control" placeholder="Entrer l'email" />
                                    <span class="text-danger" v-if="getErrors.email">
                                        {{ getErrors.email[0] }}
                                    </span>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>Téléphone</label>
                                    <input type="text" v-model="form.phone" class="form-control" placeholder="Entrer le téléphone" />
                                    <span class="text-danger" v-if="getErrors.phone">
                                        {{ getErrors.phone[0] }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="row clearfix mt-4">
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>Statut du compte</label>
                                    <select v-model="form.status" ref="select" class="form-control">
                                        <option v-for="s in statusOptions" :value="s.status">{{s.value}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-3 ">
                                <div class="form-group">
                                    <div class="upload-btn-wrapper">
                                        <label>Charger une image</label>
                                        <button class="btn-upload"><i class="fa fa-image">  Profil</i></button>
                                        <input type="file" ref="avatar" id="file" accept="image/*" @change="handleAvatarUpload"><br>
                                        <span class="text-danger" v-if="getErrors.avatar">
                                            {{ getErrors.avatar[0] }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div v-if="!isEditing" class="col-sm-6 text-center">
                                <img height="100" :src="avatarPreview" v-show="showPreview" style="border:dashed 2px gray;padding:5px;"/>
                            </div>
                            <template v-if="isEditing">
                                <div class="col-sm-3">
                                    <label>Image actuelle</label>
                                    <img height="100" :src="form.avatar" alt="">
                                </div>
                                <div class="col-sm-3">
                                    <label v-show="showPreview">Nouvelle Image</label>
                                    <img height="100" :src="avatarPreview" v-show="showPreview" style="border:dashed 2px gray;padding:5px;"/>
                                </div>
                            </template>
                        </div>
                        <div class="row clearfix">
                            <div class="col-sm-6 text-center">
                                <label> </label>
                                <button class="btn btn-primary mb-2" type="button" data-toggle="collapse" data-target="#collapsePermissions" aria-expanded="false" aria-controls="collapsePermissions">
                                    Attribuer des permissions
                                </button>
                                <div class="collapse" id="collapsePermissions" style="max-height:300px; max-width:300px; margin-top-20px;overflow-y: scroll">
                                    <div class="card card-body" style="">
                                        <label v-for="(p, key ) in userPermissions" style="margin-left: 15px;">
                                            {{ p.name }}
                                            <input type="checkbox" class="checkbox" v-model="form.permissions" :key="key" :value="p.id" @change='updatePermissions'>
                                        </label>
                                    </div>
                                </div>
                                <span class="text-danger" v-if="getErrors.permissions">
                                    {{ getErrors.permissions[0] }}
                                </span>
                            </div>
                            <div class="col-sm-6 text-center">
                                <label> </label>
                                <button class="btn btn-primary mb-2" type="button" data-toggle="collapse" data-target="#collapseProfiles" aria-expanded="false" aria-controls="collapseProfiles">
                                    Attribuer des profils
                                </button>
                                <div class="collapse" id="collapseProfiles" style="max-height:300px; max-width:300px; margin-top-20px;overflow-y: scroll">
                                    <div class="card card-body" style="">
                                        <label v-for="(p, key ) in userProfiles" style="margin-left: 15px;">
                                            {{ p.name }}
                                            <input type="checkbox" class="checkbox" v-model="form.profiles" :key="key" :value="p.id" @change='updateProfiles'>
                                        </label>
                                    </div>
                                </div>
                                <span class="text-danger" v-if="getErrors.profiles">
                                    {{ getErrors.profiles[0] }}
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
        props:['user_permissions','user_profiles'],
        data () {
            return {
                errors: [],
                queryOptions: [{field: 'name', value: 'Nom complet'},{field: 'email' ,value: 'Email'},{field: 'phone', value: 'Téléphone'}],
                statusOptions: [{status: 'confirmed', value: 'Confirmé'},{status: 'pending' ,value: 'Traitements...'},{status: 'banned', value: 'Bloqué'}],
                queryFiled: 'name',
                query:'',
                users: [],
                permissions: [],
                profiles: [],
                userPermissions:[],
                userProfiles:[],
                form:{name: '',email: '',phone: '',permissions: [], profiles: [], status: 'confirmed'},
                avatar: '',
                password: '',
                showPreview: false,
                avatarPreview: '',
                pagination:{current_page: 1},
                loading: false,
                isEditing:false,
            }
        },
        updated: function(){
            //if(window.$)window.$(this.$refs.multiselect).multiselect('refresh');
        },
        mounted() {
            this.fetchUsers();
            this.userPermissions = this.user_permissions;
            this.userProfiles = this.user_profiles;
        },
        methods: {
            fetchUsers(){
                axios.get('/utilisateurs/?page='+this.pagination.current_page).then(response => {
                    this.users = response.data.data;
                    this.permissions = response.data.permissions;
                }).catch(error => {
                    this.toast(error);
                })
            },
            searchUsers(){
                if (this.query == ''){
                    toastr['warning']('Le champ rechercher est requit!', 'Oops', {timeOut: 5000, closeButton: true});
                } else {
                    this.$Progress.start();
                    axios.get('/utilisateurs/search/'+this.queryFiled+'/'+this.query+'?page='+this.pagination.current_page).then(response => {
                        this.users = response.data.data;
                        this.pagination = response.data.meta;
                        this.$Progress.finish();
                    }).catch(error => {
                        this.$Progress.finish();
                        this.toast(error);
                    })
                }
            },
            save(){
                let url = '';
                if(!this.isEditing){
                    url = '/utilisateurs/nouveau';
                }else {
                    url = '/utilisateurs/'+this.form.id+'/edition';
                }
                this.loading = true;
                this.errors = [];
                let formData = new FormData();
                formData.append('avatar', this.$refs.avatar.files[0] ? this.$refs.avatar.files[0] : '');
                formData.append('name', this.form.name);
                formData.append('email', this.form.email);
                formData.append('phone', this.form.phone);
                formData.append('status', this.form.status);
                formData.append('password', this.password);
                formData.append('permissions', JSON.stringify(this.form.permissions));
                formData.append('profiles', JSON.stringify(this.form.profiles));
                axios.post(url, formData, {headers: {'Content-Type': 'multipart/form-data'}}).then(response => {
                    this.resetForm();
                    toastr['success'](this.isEditing ? "L'utilisateur a bien été créé" : "L'utilisateur a bien été mis à jour", '', {timeOut: 5000, closeButton: true});
                    this.loading = false;
                    this.isEditing = false;
                    this.fetchUsers();
                }).catch(error => {
                    this.loading = false;
                    this.toast(error);
                });
            },
            edit(user){
                this.isEditing = true;
                this.form = user;
                this.permissions = user.permissions;
                this.profiles = user.profiles;
                $('#user-store').appendTo('body').modal('show');
            },
            reload() {
                this.query = '';
                this.queryFiled = 'name';
                this.$Progress.start();
                this.fetchUsers();
                this.$Progress.finish();
            },
            create(){
                $('#user-store').appendTo('body').modal('show');
            },
            destroy(user){
                swal({
                    title: "Êtes-vous sûr?",
                    text: "Vous ne pourrez pas récupérer cet utilisateur après supression!",
                    type: "error",
                    showCancelButton: true,
                    confirmButtonText: 'Confirmer',
                    cancelButtonText: 'Annuler',
                    showCloseButton: true,
                    showLoaderOnConfirm: true
                }, () => {
                    axios.get('/utilisateurs/'+user.id+'/supression').then(response => {
                        this.fetchUsers();
                        toastr['success']("L'utilisateur a bien été supprimé", '', {timeOut: 5000, closeButton: true});
                    }).catch(error => {
                        this.toast(error);
                    });
                });
            },
            handleAvatarUpload(){
                this.avatar = this.$refs.avatar.files[0];
                let reader  = new FileReader();
                reader.addEventListener("load", function () {
                    this.showPreview = true;
                    this.avatarPreview = reader.result;
                }.bind(this), false);
                if(this.avatar){
                    if ( /\.(jpe?g|png|gif)$/i.test( this.avatar.name ) ) {
                        reader.readAsDataURL( this.avatar );
                    }
                }
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
                this.$refs.avatar = null;
                this.password = '';
                this.form.name = '';
                this.form.email = '';
                this.form.phone = '';
                this.form.permissions = [];
                this.showPreview = false;
                this.avatarPreview = '';
                this.fetchUsers();
                $('#user-store').modal('hide');
            },
            updatePermissions () {
                //this.$emit('input', this.form.permissions);
            },
            updateProfiles () {
                //this.$emit('input', this.form.profiles);
            },
            goProfile(){
                window.location.replace(`/profils`);
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
