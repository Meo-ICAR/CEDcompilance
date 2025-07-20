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

// Google Drive routes
Route::prefix('google-drive')->group(function () {
    Route::get('/', [App\Http\Controllers\GoogleDriveController::class, 'index'])->name('google-drive.index');
    Route::get('/files', [App\Http\Controllers\GoogleDriveController::class, 'listFiles'])->name('google-drive.files');
    Route::post('/upload', [App\Http\Controllers\GoogleDriveController::class, 'uploadFile'])->name('google-drive.upload');
    Route::get('/download', [App\Http\Controllers\GoogleDriveController::class, 'downloadFile'])->name('google-drive.download');
    Route::get('/info', [App\Http\Controllers\GoogleDriveController::class, 'getFileInfo'])->name('google-drive.info');
    Route::delete('/delete', [App\Http\Controllers\GoogleDriveController::class, 'deleteFile'])->name('google-drive.delete');
});

// NIS2 Google Drive Folders routes
Route::prefix('nis2-folders')->group(function () {
    Route::get('/', [App\Http\Controllers\Nis2FolderController::class, 'index'])->name('nis2-folders.index');
    Route::post('/create-all', [App\Http\Controllers\Nis2FolderController::class, 'createAll'])->name('nis2-folders.create-all');
    Route::get('/status', [App\Http\Controllers\Nis2FolderController::class, 'status'])->name('nis2-folders.status');
    Route::delete('/delete', [App\Http\Controllers\Nis2FolderController::class, 'deleteFolder'])->name('nis2-folders.delete');
});
