<?php

use App\Http\Controllers\DocumentController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

//home page
Route::get('/home', [DocumentController::class, 'index'])->middleware(['auth', 'verified'])->name('home');

//search
Route::get('/search', [DocumentController::class, 'search'])->name('search');

Route::get('admin/dashboard', function () {
    return view('admin.dashboard',
        [
            'users' => \App\Models\User::all(),
            'roles' => \App\Models\roles::all(),
        ]

    );
})->middleware(['auth', 'verified'])->name('dashboard')->middleware('role:admin');

Route::middleware('auth')->group(function () {
//    profile routes
//    show
    Route::get('/profile/show', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

//change role post with auth and must be admin
Route::post('admin/change-role', [ProfileController::class, 'changeRole'])->name('change.role') ->middleware('auth') ->middleware('role:admin');


//Document routes
//group routes
Route::group(['prefix' => 'document/'], function () {
//download
    Route::post('download/{document}', [DocumentController::class, 'download'])->name('download');
//update
    Route::patch('update/{document}', [DocumentController::class, 'update'])->name('edit');
//delete
    Route::post('delete', [DocumentController::class, 'delete'])->name('delete');
//create
    Route::post('create', [DocumentController::class, 'create'])->name('create')->middleware('role:uploader');
//approve for reviewer or finalizer only
    Route::post('approve', [DocumentController::class, 'approve'])->name('approve')->middleware('role:finalizer,reviewer');
//reject
    Route::post('reject', [DocumentController::class, 'reject'])->name('reject')->middleware('role:finalizer,reviewer');
})->middleware('auth,verified');




require __DIR__.'/auth.php';
