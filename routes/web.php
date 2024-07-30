<?php

use App\Http\Controllers\NoteControler;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;
Route::redirect('/', '/note')->name('dashboard');


Route::middleware(['auth', 'verified']) ->group(function(){
    Route::resource('note', NoteControler::class);
});
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::controller(PaymentController::class)
->prefix('payments')
->as('payments')
->group(function(){
    Route::get('/initiatepush', 'initiaateStkPush')->name('initiatepush');
    Route::post('/stkcallback', 'stkCallBack')->name('stkcallback');
});

require __DIR__.'/auth.php';
