<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
// Users auth
Route::get('/','Auth\LoginController@showLoginForm')->name('create-login');
Route::post('/','Auth\LoginController@login')->name('store-login');

// Email verify routes
Route::get('/email/verification/{id}','Auth\VerificationController@verify')->name('verification.verify');
Route::middleware(['auth'])->group(function () {
    Route::group(['middleware' => ['second-login']], function() {
        Route::middleware(['verified'])->group(function () {
            // Dashboard route
            Route::get('/tableau-de-bord','DashboardController@index')->name('index-dashboard')->middleware('can:show-dashboard');
            Route::get('/telecharger/{ficher}','DashboardController@download')->middleware('can:download');
            Route::get('/downloadscan/{id}/{ficher}','DashboardController@downloadScan')->middleware('can:download');
           
            Route::get('/indicateurs/realisations','DashboardController@indicator')->name('index-indicator')->middleware('can:show-indicator'); 
            Route::get('/indicateurs/effets-impacts','DashboardController@indicatore')->name('index-indicatore')->middleware('can:show-indicator');
            Route::get('/indicateurs/plaintes','DashboardController@plainte')->name('index-plainte')->middleware('can:show-indicator');

            Route::post('/indicateurs/creation','DashboardController@indicatorStore')->middleware('can:create');
            Route::post('/indicateurs/{id}/update','DashboardController@indicatorUpdate')->middleware('can:update');
            Route::get('/indicateurs/{id}/supression','DashboardController@indicatorDestroy')->middleware('can:delete');
            
            Route::get('/indicateurs/compensations','DashboardController@compensation')->name('index-compensation')->middleware('can:show-database');
            
            
            Route::get('/compensations','DashboardController@compensation')->name('index-compensation')->middleware('can:show-database');
            Route::get('/cartographie','MapController@index')->name('index-map')->middleware('can:show-map');
            Route::get('/dossier-pap/{idpap?}','DashboardController@folder')->name('index-folder')->middleware('can:show-indicator');
            
            Route::get('/documentation/cartes','DashboardController@carte')->name('index-carte')->middleware('can:show-document');
            Route::get('/documentation/rapports/par','DashboardController@par')->name('index-par')->middleware('can:show-document');
            Route::get('/documentation/livrables','DashboardController@livrable')->name('index-livrable')->middleware('can:show-document');
            Route::get('/documentation/sig','DashboardController@sig')->name('index-sig')->middleware('can:show-document');
            Route::get('/documentation/rapports/mise-en-oeuvre','DashboardController@oeuvre')->name('index-oeuvre')->middleware('can:show-document');
            Route::get('/documentation/decrets','DashboardController@decret')->name('index-decret')->middleware('can:show-document');
            
            Route::post('/documentation/creation','DashboardController@documentStore')->name('create-document')->middleware('can:create');
            Route::post('/documentation/{id}/update','DashboardController@documentUpdate')->name('create-document')->middleware('can:update');
            Route::get('/documentation/{id}/supression/','DashboardController@documentDestroy')->name('create-document')->middleware('can:delete');
            Route::get('/gestion-paps','PapController@index')->name('index-pap')->middleware('can:show-database');
            Route::get('/search/localite','PapController@searchLocalites')->name('search-pap');
            Route::get('/search/axe','PapController@searchAxes')->name('search-pap');
            Route::get('/search/section','PapController@searchSections')->name('search-pap');
            Route::get('/search/commune','PapController@searchCommunes')->name('search-pap');
            Route::get('/current/papcomp/{search}','PapController@searchComp');
            Route::get('/search','PapController@searchPaps')->name('search-pap');
            Route::get('/file/{where}/{search}/generate','PapController@generateSingle')->name('generate-file')->middleware('can:generate');
            Route::get('/file/compensation/{where}/{search}/generate','PapController@generateSingleComp')->name('generate-file')->middleware('can:generate');
            Route::get('/file/{where}/{search}/generate-multiple','PapController@generateMultiple')->name('generate-file')->middleware('can:generate');
            Route::post('/pap/generate','PapController@generate')->middleware('can:generate');
            Route::get('/pap/{id}/image','PapController@deletePhoto')->middleware('can:delete');
            Route::post('/pap/update','PapController@updatePap')->middleware('can:update');
            Route::post('/pap/update/compensation','PapController@updateCompensation')->middleware('can:update');
            Route::get('/excell/{where}/{search}/generate','PapController@excell')->name('search-pap')->middleware('can:generate');
            Route::get('/excell/compensation/{where}/{search}/generate','PapController@excellComp')->name('search-pap')->middleware('can:generate');



            // Users
            Route::get('/utilisateurs','UserController@index')->name('index-user')->middleware('can:index-user');
            Route::get('/utilisateurs/search/{field?}/{query?}','UserController@search')->name('search-user')->middleware('can:search-user');
            Route::post('/utilisateurs/nouveau','UserController@store')->name('store-user')->middleware('can:store-user');
            Route::post('/utilisateurs/{id}/edition','UserController@update')->name('update-user')->middleware('can:update-user');
            Route::get('/utilisateurs/{id}/supression','UserController@destroy')->name('destroy-user')->middleware('can:destroy-user');

            // Profiles
            Route::get('/profils','ProfilesController@index')->name('index-prof')->middleware('can:index-prof');
            Route::get('/profils/search/{field?}/{query?}','ProfilesController@search')->name('search-prof')->middleware('can:search-prof');
            Route::post('/profils/nouveau','ProfilesController@store')->name('store-prof')->middleware('can:store-prof');
            Route::post('/profils/{id}/edition','ProfilesController@update')->name('update-prof')->middleware('can:update-prof');
            Route::get('/profils/{id}/supression','ProfilesController@destroy')->name('destroy-prof')->middleware('can:destroy-prof');

            // Profile
            Route::get('/profil','ProfileController@edit')->name('edit-profile')->middleware('can:show-profil');
            Route::post('/profil','ProfileController@update')->name('update-profile')->middleware('can:show-profil');
        });

        // Email verify routes
        Route::get('/email/verification','Auth\VerificationController@show')->name('show-verification');
        Route::get('/email/renvoyer','Auth\VerificationController@resend')->name('resend-verification');

        // Auth route
        
    });
    Route::get('/deconnexion','Auth\LoginController@logout')->name('destroy-login');
    Route::group(['middleware' => ['first-login']], function (){
        Route::get('/mot-de-passe','FirstLoginController@create')->name('first-login-create');
        Route::post('/mot-de-passe','FirstLoginController@store')->name('first-login-store');
    });

});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
