<template>
    <form class="user">
        <div class="form-group">
            <input type="email" v-model="form.email" class="form-control form-control-user" id="exampleInputEmail" aria-describedby="emailHelp" placeholder="Email">
            <span class="text-danger" v-if="getErrors.email">
                {{ getErrors.email[0] }}
            </span>
        </div>
        <div class="form-group">
            <input type="password" v-model="form.password" class="form-control form-control-user" id="exampleInputPassword" placeholder="Mode de passe">
            <span class="text-danger" v-if="getErrors.password">
                {{ getErrors.password[0] }}
            </span>
        </div>
        <button @click.prevent="login" type="submit" :disabled="loading" class="btn btn-primary btn-user btn-block">
            <template v-if="!loading">CONNEXION</template>
            <template v-if="loading"><i class='fa fa-spin fa-spinner'></i> EN COURS DE CONNEXION...</template>
        </button>
    </form>
</template>

<script>
    import axios from 'axios';
    export default {
        props: ['auth','redirect'],
        data() {
            return {
                loading: false,
                form: {email: '', password: ''},
                errors: []
            };
        },
        methods: {
            login() {
                this.errors = [];
                this.loading = true;
                axios.post(this.auth, this.form).then(response => {
                    this.loading = false;
                    location.replace(this.redirect)
                }).catch(error => {
                    this.loading = false;
                    if(error.response.status === 422){
                        this.errors = error.response.data.errors;
                    }else if(error.response.status === 403){
                        toastr['warning'](error.response.data.message, 'Vous n\'êtes pas autorisé!', {timeOut: 5000, closeButton: true});
                    }else{
                        toastr['error']('Une érreur est survenue!', 'Réessayez plus tard...', {timeOut: 5000, closeButton: true});
                    }
                });
            }
        },
        computed: {
            getErrors() {
                return this.errors;
            }
        }
    }
</script>