<template>
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <input type="text" placeholder="Choisir une localité...." v-model="search" v-on:keyup="getSearchData" class="form-control col-sm-3 mb-3">
                <div class="row">
                    <div class="col-sm-2">
                        <button type="button" class="btn btn-info btn-round btn-default mb-3" @click.prevent="generate" :disabled="loading">
                            <template v-if="!loading">RECHERCHE</template>
                            <template v-if="loading"><i class='fa fa-spin fa-spinner'></i>EN COURS DE GENERATION...</template>
                        </button>
                    </div>
                    <div class="col-sm-2">
                        <button type="button" class="btn btn-success btn-round btn-default mb-3" @click.prevent="generate" :disabled="loading">
                            <template v-if="!loading">GENERER</template>
                            <template v-if="loading"><i class='fa fa-spin fa-spinner'></i>EN COURS DE GENERATION...</template>
                        </button>
                    </div>
                </div>
                <div class="panel-footer" v-if="results.length">
                    <ul class="list-group">
                        <li class="list-group-item"  v-for="(result,index) in results" v-on:click="select(result)"  :key="index">@{{ result.NOM_COMMUNE }}</li>
                    </ul>

                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <h3 class="text-primary">Résultat de la génération</h3>
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                        <tr>
                            <th>ID PAP</th>
                            <th>PHOTO</th>
                            <th>NOM</th>
                            <th>PRENOM</th>
                            <th>AXE</th>
                            <th>LOCALITE</th>
                            <th>COMMUNE</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="text-center" v-show="searches.length" v-for="(search, index) in searches" :key="search.id">
                            <td class="align-middle">{{search.ID_PAP}}</td>
                            <td class="align-middle"><img width="60" :src="'photopap/'+search.ID_PAP+'.jpg'" alt=""></td>
                            <td class="align-middle">{{ search.NOM_OCCUP}}</td>
                            <td class="align-middle">{{search.PRENOM_OCCUP}}</td>
                            <td class="align-middle">{{search.NOM_AXE}}</td>
                            <td class="align-middle">{{search.NOM_LOCALITE}}</td>
                            <td class="align-middle">{{search.NOM_COMMUNE}}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data: function () {
        return {
          search: '',
          results: [],
          searches:[],
          selected: '',
            loading: false
        }
      },
      methods: {
        getSearchData(){
        this.results = [];
        if(this.search.length > 0){
         axios.get('/search/location',{params: {search: this.search}}).then(response => {
          this.results = response.data;
         });
        }
       },
          generate(){
              this.loading = true;
              let formData = new FormData();
              formData.append('search', this.search);
              axios.post('/etat/generate', formData, {headers: {'Content-Type': 'multipart/form-data'}}).then(response => {
                  this.loading = false;
                  this.searches = response.data;
              }).catch(error => {
                  this.loading = false;
                  this.toast(error);
              });
          },
       select(current){
           this.search = current.NOM_COMMUNE;
           this.results = [];
       }
    }
    }
</script>
