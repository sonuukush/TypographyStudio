<?php

use App\Http\Controllers\AppController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VoiceoverController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Landing / Home
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Public: Main app page (live preview visible to all, downloads require auth)
Route::get('/app', [AppController::class, 'index'])->name('app');

// Editor mode for per-phrase customization
Route::get('/app/editor/{id}', [AppController::class, 'editor'])->name('editor');

// Free Voiceover Generator Endpoints
Route::get('/media/voiceover/{filename}', [VoiceoverController::class, 'streamAudio'])->name('voiceover.stream');
Route::post('/api/generate-voiceover', [VoiceoverController::class, 'generate'])->name('api.generate_voiceover');
Route::post('/api/save-voiceover', [VoiceoverController::class, 'saveBlob'])->name('api.save_voiceover');

// Auth-required: render generation, image uploads + downloads
Route::middleware('auth')->group(function () {
    Route::post('/generate', [AppController::class, 'generate'])->name('generate');
    Route::post('/editor/upload-image', [AppController::class, 'uploadImage'])->name('editor.upload_image');
    Route::get('/renders/{id}/status', [AppController::class, 'status'])->name('renders.status');
    Route::get('/renders/{id}/download', [AppController::class, 'download'])->name('renders.download');
    Route::get('/downloads', [AppController::class, 'downloads'])->name('downloads');
});

// Profile (Breeze default)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
