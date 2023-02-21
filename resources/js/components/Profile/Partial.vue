<template>
    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <span class="mr-2 d-none d-lg-inline text-gray-600 small">{{ user.name }}</span>
        <img class="img-profile rounded-circle" :src="user.avatar">
    </a>
</template>

<script>
    export default {
        props:['user_data'],
        data(){
            return {
                user: {},
            };
        },
        mounted() {
            this.user = this.user_data;
            window.EventBus.$on('fresh_user',(data) => {
                this.user = data;
            });
        },
        methods:{
        }
    }
</script>