<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Base\ImageController;

Route::prefix('image')->group(function () {
    // Route to encrypt image path
    Route::get('encrypt/{imagePath}', [ImageController::class, 'encryptImagePath'])->name('image.encrypt');
    
    // Route to show encrypted image (accessible via public URL)
    Route::get('show/{encryptedPath}', [ImageController::class, 'showImage'])->name('image.show');
});
