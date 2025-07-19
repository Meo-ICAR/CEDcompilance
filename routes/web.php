<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::resource('organizzazioni', App\Http\Controllers\OrganizzazioneController::class);
Route::resource('assets', App\Http\Controllers\AssetController::class);
Route::resource('rischi', App\Http\Controllers\RischioController::class);
Route::resource('incidenti', App\Http\Controllers\IncidenteController::class);
Route::resource('punti-nis2', App\Http\Controllers\PuntoNis2Controller::class);
Route::resource('documentazioni-nis2', App\Http\Controllers\DocumentazioneNis2Controller::class);
Route::resource('nis2-dettagli', App\Http\Controllers\Nis2DettaglioController::class);
