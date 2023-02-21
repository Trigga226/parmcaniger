<?php

use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('permissions')->delete();
        DB::table('permissions')->insert([
            [
                'name' => 'Editer son profil',
                'slug' => 'edit-profile',
            ],
            [
                'name' => 'Mettre à jour son profil',
                'slug' => 'update-profile',
            ],// Profile permissions ---------------------
            [
                'name' => 'Lister les users',
                'slug' => 'index-user',
            ],
            [
                'name' => 'Rechercher un user',
                'slug' => 'search-user',
            ],
            [
                'name' => 'Créer un user',
                'slug' => 'create-user',
            ],
            [
                'name' => 'Enrregistrer un user',
                'slug' => 'store-user',
            ],
            [
                'name' => 'Editer un user',
                'slug' => 'edit-user',
            ],
            [
                'name' => 'Changer le statut d\'un user',
                'slug' => 'status-user',
            ],
            [
                'name' => 'Mettre à jour un user',
                'slug' => 'update-user',
            ],
            [
                'name' => 'Supprimer un user',
                'slug' => 'destroy-user',
            ],// Profiles
            [
                'name' => 'Lister les profils',
                'slug' => 'index-prof',
            ],
            [
                'name' => 'Rechercher un profile',
                'slug' => 'search-prof',
            ],
            [
                'name' => 'Créer un profile',
                'slug' => 'create-prof',
            ],
            [
                'name' => 'Enrregistrer un profile',
                'slug' => 'store-prof',
            ],
            [
                'name' => 'Editer un profile',
                'slug' => 'edit-prof',
            ],
            [
                'name' => 'Changer le statut d\'un profile',
                'slug' => 'status-prof',
            ],
            [
                'name' => 'Mettre à jour un profile',
                'slug' => 'update-prof',
            ],
            [
                'name' => 'Supprimer un profile',
                'slug' => 'destroy-prof',
            ],// components permissions ---------------------
            [
                'name' => 'Lister les permissions',
                'slug' => 'index-permission',
            ],
            [
                'name' => 'Rechercher une permission',
                'slug' => 'search-permission',
            ],
            [
                'name' => 'Créer une permission',
                'slug' => 'create-permission',
            ],
            [
                'name' => 'Enrregistrer une permission',
                'slug' => 'store-permission',
            ],
            [
                'name' => 'Editer une permission',
                'slug' => 'edit-permission',
            ],
            [
                'name' => 'Mettre à jour une permission',
                'slug' => 'update-permission',
            ],
            [
                'name' => 'Supprimer une permission',
                'slug' => 'destroy-permission',
            ],// Permission permissions ---------------------
            [
                'name' => 'Visualiser tableau de bord',
                'slug' => 'list-dashboard',
            ],// Dashboard permission ---------------------œ
        ]);
    }
}
