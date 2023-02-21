<script>
    export default {
        methods: {
            $can(permissionName) {
                return userPermissions.indexOf(permissionName) !== -1;
            },
            $role(roleName) {
                return userRoles.indexOf(roleName) !== -1;
            },
        },
    };
</script>
