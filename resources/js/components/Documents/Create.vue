<template>
    <div class="container-fluid">
        <div class="row clearfix">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                   <!-- Page Heading -->
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Ajouter des documents</h1>
                    </div>
                </div>
                <div class="card-body">
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label>NON DU DOCUMENT</label>
                            <input type="text" v-model="form.name" class="form-control" placeholder="Entrer le numéro de la pièce" />
                            <span class="text-danger" v-if="getErrors.name">
                                {{ getErrors.name[0] }}
                            </span>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label>DATE DE PRODUCTION</label>
                            <input type="text" v-model="form.date" class="form-control" placeholder="Entrer le numéro de la pièce" />
                            <span class="text-danger" v-if="getErrors.date">
                                {{ getErrors.date[0] }}
                            </span>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label>CHOISIR LES PIECES JOINTES</label>
                            <input type="file" ref="image" id="file">
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
        props:[''],
        data () {
            return {
                errors: [],
                loading:false,
                form:{name:'',date:''}
               
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
