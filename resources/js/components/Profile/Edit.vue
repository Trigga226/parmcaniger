<template>
    <div class="row">
        <div class="col-sm-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Profil utilisateur</h6>
                </div>
                <div class="card-body">
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="home-tab" data-toggle="tab" href="#information" role="tab" aria-controls="home" aria-selected="true">INFORMATIONS</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="profile-tab" data-toggle="tab" href="#avatar" role="tab" aria-controls="profile" aria-selected="false">AVATAR</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="messages-tab" data-toggle="tab" href="#email" role="tab" aria-controls="messages" aria-selected="false">EMAIL</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="settings-tab" data-toggle="tab" href="#password" role="tab" aria-controls="settings" aria-selected="false">MOT DE PASSE</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="information" role="tabpanel" aria-labelledby="home-tab">
                            <div class="header mt-4 mb-4">
                                <h4><strong>Mettre à jour vos</strong> informations</h4>
                            </div>
                            <div class="row">
                                <div class="form-group col-sm-6">
                                    <input type="text" v-model="profile.name" class="form-control form-control-user" placeholder="Nom et Prénom">
                                    <span class="text-danger" v-if="getErrors.name">
                                        {{ getErrors.name[0] }}
                                    </span>
                                </div>
                                <div class="form-group col-sm-6">
                                    <input type="text" v-model="profile.phone" class="form-control" placeholder="Numéro de téléphone">
                                    <span class="text-danger" v-if="getErrors.phone">
                                        {{ getErrors.phone[0] }}
                                    </span>
                                </div>
                            </div>
                            <button style="cursor: pointer" @click.prevent="updateInformation" class="btn btn-warning btn-round"><i class="fa fa-save"></i>
                                <template v-if="!loading">Mettre à jour</template>
                                <template v-if="loading"><i class='fa fa-spin fa-spinner'></i> En cours de mise à jour...</template>
                            </button>
                        </div>
                        <div class="tab-pane" id="avatar" role="tabpanel" aria-labelledby="profile-tab">
                            <div class="header mt-4 mb-4">
                                <h4><strong>Mettre à jour votre</strong> avatar</h4>
                            </div>
                            <div class="body">
                                <div class="row clearfix">
                                    <div class="form-group col-sm-4">
                                        <input type="file" ref="file" id="file" accept="image/*" class="form-control" placeholder="Choisir un nouvel avatar">
                                        <span class="text-danger" v-if="getErrors.file">
                                        {{ getErrors.file[0] }}
                                    </span>
                                    </div>
                                </div>
                                <button style="cursor: pointer" @click.prevent="uploadAvatar" class="btn btn-warning btn-round"><i class="fa fa-save"></i>
                                    <template v-if="!loading">Mettre à jour</template>
                                    <template v-if="loading"><i class='fa fa-spin fa-spinner'></i> En cours de mise à jour...</template>
                                </button>
                            </div>
                        </div>
                        <div class="tab-pane" id="email" role="tabpanel" aria-labelledby="messages-tab">
                            <div class="header mt-4 mb-4">
                                <h4><strong>Mettre à jour votre</strong> adresse E-mail</h4>
                            </div>
                            <div class="body">
                                <div class="row clearfix">
                                    <div class="row clearfix">
                                        <div class="col-sm-12">
                                            <p class="text-danger text-center">Attention, après modification de votre adresse email, un mail confirmation vous sera envoyé pour confirmer votre identité.</p>
                                            <p class="text-muted italic">PS: Vous n'aurez accès qu'après confirmation!</p>
                                            <hr>
                                        </div>
                                    </div>
                                    <div class="form-group col-sm-4">
                                        <input type="email" v-model="profile.email" class="form-control" placeholder="Email">
                                        <span class="text-danger" v-if="getErrors.email">
                                        {{ getErrors.email[0] }}
                                    </span>
                                    </div>
                                </div>
                                <button style="cursor: pointer" @click.prevent="updateEmail" class="btn btn-warning btn-round"><i class="fa fa-save"></i>
                                    <template v-if="!loading">Mettre à jour</template>
                                    <template v-if="loading"><i class='fa fa-spin fa-spinner'></i> En cours de mise à jour...</template>
                                </button>
                            </div>
                        </div>
                        <div class="tab-pane" id="password" role="tabpanel" aria-labelledby="settings-tab">
                            <div class="header mt-4 mb-4">
                                <h4><strong>Mettre à jour votre</strong> mot de passe</h4>
                            </div>
                            <div class="body">
                                <div class="row clearfix">
                                    <div class="form-group col-sm-4">
                                        <input type="password" v-model="profile.current_password" class="form-control" placeholder="Mot de passe actuel">
                                        <span class="text-danger" v-if="getErrors.current_password">
                                            {{ getErrors.current_password[0] }}
                                        </span>
                                    </div>
                                    <div class="form-group col-sm-4">
                                        <input type="password" v-model="profile.password" class="form-control" placeholder="Nouveau mot de passe">
                                        <span class="text-danger" v-if="getErrors.password">
                                            {{ getErrors.password[0] }}
                                        </span>
                                    </div>
                                    <div class="form-group col-sm-4">
                                        <input type="password" v-model="profile.password_confirmation" class="form-control" placeholder="Confirmation">
                                        <span class="text-danger" v-if="getErrors.password_confirmation">
                                           {{ getErrors.password_confirmation[0] }}
                                        </span>
                                    </div>
                                </div>
                                <button style="cursor: pointer" @click.prevent="updatePassword" class="btn btn-warning btn-round"><i class="fa fa-save"></i>
                                    <template v-if="!loading">Mettre à jour</template>
                                    <template v-if="loading"><i class='fa fa-spin fa-spinner'></i> En cours de mise à jour...</template>
                                </button>
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
        props: ['save','user'],
        data() {
            return {
                step:1,
                loading: false,
                errors: [],
                profile: {name: this.user.name, phone: this.user.phone, email: this.user.email ,avatar: this.user.avatar ? this.user.avatar : '/img/profile_av.jpg', current_password: '',password: '',password_confirmation: ''}
            };
        },
        methods:{
            updateInformation(){
                this.errors = [];
                this.loading = true;
                this.profile.step = 1;
                axios.post(this.save, this.profile).then(response => {
                    this.loading = false;
                    toastr['success']("Les information ont bien été mis à jour", '', {timeOut: 6000, closeButton: true});
                    this.profile.name = response.data.user.name;
                    this.profile.phone = response.data.user.phone;
                    window.EventBus.$emit('fresh_user', response.data.user);
                }).catch(error => {
                    this.loading = false;
                    if(error.response.status === 422){
                        this.errors = error.response.data.errors;
                    }else{
                        toastr['error']('Une érreur est survenue!', 'Réessayez plus tard...', {timeOut: 5000, closeButton: true});
                    }
                });
            },
            uploadAvatar(){
                this.errors = [];
                this.loading = true;
                let formData = new FormData();
                formData.append('file',  this.$refs.file.files[0]);
                formData.append('step', 2);
                axios.post(this.save, formData, {headers: {'Content-Type': 'multipart/form-data'}}).then(response => {
                    this.loading = false;
                    this.profile.avatar = response.data.user.avatar;
                    this.$refs.file.value = '';
                    window.EventBus.$emit('fresh_user', response.data.user);
                    toastr['success']("L'avatar a bien été modifié", '', {timeOut: 6000, closeButton: true});
                }).catch(error => {
                    this.loading = false;
                    if(error.response.status === 422){
                        this.errors = error.response.data.errors;
                    }else{
                        toastr['error']('Une érreur est survenue!', 'Réessayez plus tard...', {timeOut: 5000, closeButton: true});
                    }
                });
            },
            updateEmail(){
                this.errors = [];
                this.loading = true;
                this.profile.step = 3;
                axios.post(this.save, this.profile).then(response => {
                    this.loading = false;
                    if(response.data.verify == true){
                        location.replace(this.save);
                    }
                    toastr['success']("L'email n'a pas changé", '', {timeOut: 6000, closeButton: true});
                }).catch(error => {
                    this.loading = false;
                    if(error.response.status === 422){
                        this.errors = error.response.data.errors;
                    }else{
                        toastr['error']('Une érreur est survenue!', 'Réessayez plus tard...', {timeOut: 5000, closeButton: true});
                    }
                });
            },
            updatePassword(){
                this.errors = [];
                this.loading = true;
                this.profile.step = 4;
                axios.post(this.save, this.profile).then(response => {
                    this.loading = false;
                    if (response.data.status == 'ok'){
                        this.profile.current_password = '';
                        this.profile.password = '';
                        this.profile.password_confirmation = '';
                        toastr['success']("Le mot de passe a bien été mis à jour", '', {timeOut: 6000, closeButton: true});
                    } else {
                        toastr['warning']("Votre mot de passe actuel ne correspond pas!", '', {timeOut: 6000, closeButton: true});
                    }
                }).catch(error => {
                    this.loading = false;
                    if(error.response.status === 422){web
                        this.errors = error.response.data.errors;
                    }else{
                        toastr['error']('Une érreur est survenue!', 'Réessayez plus tard...', {timeOut: 5000, closeButton: true});
                    }
                });
            },
        },
        computed: {
            getErrors() {
                return this.errors;
            }
        }
    }
</script>
