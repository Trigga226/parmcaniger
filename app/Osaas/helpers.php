<?php

/**
 * Helper function to toast message (errors, warnings, info and success)
 */
if(!function_exists('toast')){
    /**
     * @param null $text
     * @return \App\Osaas\Toast|void
     */
    function toast($text = null)
    {
        $toast = new App\Osaas\Toast();
        if(func_num_args() == 0){
            return $toast;
        }
        return $toast->info($text);
    }
}

/**
 * Helper function to get status.
 */
if(!function_exists('status')){
    function status($status){
        if($status == 'confirmed')
            return ['Confirmé','success'];
        if($status == 'pending')
            return ['Traitement...','warning'];
        if($status == 'banned')
            return ['Bloqué','danger'];
    }
}

/**
 * Helper function to active parent li menu
 */
if(!function_exists('activeParent')){
    function activeParent($routes){
        if(in_array(Route::currentRouteName(), $routes)){
            return 'show';
        }else{
            return '';
        }
    }
}

/**
 * Helper function to active child li menu
 */
if(!function_exists('activeChild')){
    function activeChild($routes){
        if(in_array(Route::currentRouteName(), $routes)){
            return 'active';
        }else{
            return '';
        }
    }
}

/**
 * Helper function to active child li menu
 */
if(!function_exists('activeShow')){
    function activeShow($routes){
        if(in_array(Route::currentRouteName(), $routes)){
            return 'show';
        }else{
            return '';
        }
    }
}

/**
 * Helper function to active child li menu
 */
if(!function_exists('activeCollapsed')){
    function activeCollapsed($routes){
        if(in_array(Route::currentRouteName(), $routes)){
            return '';
        }else{
            return 'collapsed';
        }
    }
}

/**
 * Get current  storage full url.
 */
if (!function_exists('storage_url')){
    function storage_url($url){
        return \Illuminate\Support\Facades\Storage::url($url);
    }
}

if(!function_exists('utf8')){
    function utf8($str)
    {
        return iconv('UTF-8', 'windows-1252', $str);
    }
}
if(!function_exists('floaty')){
    function floaty($str)
    {
        return (float) str_replace(['.', ','], ['', '.'], $str);
    }
}

