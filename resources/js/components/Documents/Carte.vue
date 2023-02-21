<template>
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Gestion des documents</h1>
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
                        <button type="button" class="btn btn-warning btn-simple btn-round" @click.prevent="resetShowImage">FREMER</button>
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
                errors: [],
                loading:false,
                selectedAxe : 'rn7',
                search_idpap:'',
                loading:false,
                pap:null,
                currentAxe:'',
                currentSection:'',
                currentImage:'',
                currentSrc:'',
                file:''
            }
        },
        mounted() {

        },
        methods: {
            changeAxe(axe){
                this.selectedAxe = axe;
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