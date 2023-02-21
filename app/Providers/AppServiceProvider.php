<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Support\Facades\{Schema,URL};
use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Enable cors
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Origin, Content-Type, Accept, Authorization, X-Request-With');
        header('Access-Control-Allow-Credentials: true');

        // Change to the default database character set
        Schema::defaultStringLength(191);

        // Customise verification email
        VerifyEmail::toMailUsing(function($notifiable){
            $verifyUrl = URL::temporarySignedRoute(
                'verification.verify', Carbon::now()->addMinutes(60), ['id' => $notifiable->getKey()]
            );
            return (new MailMessage)
                ->subject('Vérifier l\'adresse e-mail')
                ->line('S\'il vous plaît cliquez sur le bouton ci-dessous pour vérifier votre adresse e-mail.')
                ->action(
                    'Vérifier l\'adresse e-mail',
                    $verifyUrl
                )
                ->line('Si vous n\'avez pas créé de compte, aucune action supplémentaire n\'est requise.');
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
?>
