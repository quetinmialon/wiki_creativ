<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CredentialController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SubscriptionController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ImageUploadController;

Route::get(uri: '/', action: function () {
    return view(view: 'welcome');
})->name('home');

//Role CRUD (may add some middlewares later)
Route::get(uri: '/roles',action: [RoleController::class,'create'])->name(name: 'roles.create');
Route::get(uri: '/roles/{id}/edit', action: [RoleController::class, 'edit'])->name(name: 'roles.edit');
Route::delete(uri: '/roles/{id}', action: [RoleController::class, 'destroy'])->name(name: 'roles.destroy');
Route::post(uri: '/roles',action: [RoleController::class,'insert'])->name(name: 'roles.insert');
Route::put(uri: '/roles/{id}', action: [RoleController::class, 'update'])->name(name: 'roles.update');


//subscription routes
Route::get('/subscribe', [SubscriptionController::class,'subscribe'])->name(name: 'subscribe');
Route::post('/subscribe', [SubscriptionController::class,'store'])->name(name: 'subscribe.store');
Route::post('/admin/subscribe/{id}',[SubscriptionController::class,'process'])->name('subscribe.process');
Route::get('/register/{token}',[SubscriptionController::class,'choosePassword'])->name('register.complete');
Route::post('/register',[SubscriptionController::class,'completeRegistration'])->name('register.finalization');


// admin routes
Route::get('/admin', [AdminController::class,'index'])->name(name:'admin');
Route::get('/admin/register', [SubscriptionController::class,'createUserInvitationForm'])->name('admin.register');
Route::post('/admin/register', [SubscriptionController::class, 'createUserInvitation'])->name('admin.create-user');


// Auth routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

//forgotten password routes
Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'reset'])->name('password.update');

//credentials mmanager routes
Route::get('/credentials', [CredentialController::class, 'index'])->name('credentials.index');
Route::get('/credentials/create', [CredentialController::class, 'create'])->name('credentials.create');
Route::post('/credentials', [CredentialController::class, 'store'])->name('credentials.store');
Route::get('/credentials/{id}/edit', [CredentialController::class, 'edit'])->name('credentials.edit');
Route::put('/credentials/{id}', [CredentialController::class, 'update'])->name('credentials.update');
Route::delete('/credentials/{id}', [CredentialController::class, 'destroy'])->name('credentials.destroy');



// categories routes
Route::prefix('categories')->name('categories.')->group(function () {
    Route::get('/', [CategoryController::class, 'index'])->name('index'); // Liste des catégories
    Route::get('/create', [CategoryController::class, 'create'])->name('create'); // Formulaire de création
    Route::post('/', [CategoryController::class, 'store'])->name('store'); // Création d'une catégorie
    Route::get('/{id}/edit', [CategoryController::class, 'edit'])->name('edit'); // Formulaire de modification
    Route::put('/{id}', [CategoryController::class, 'update'])->name('update'); // Mise à jour d'une catégorie
    Route::delete('/{id}', [CategoryController::class, 'destroy'])->name('destroy'); // Suppression d'une catégorie
});


// documents routes

Route::prefix('documents')->name('documents.')->group(function () {

    Route::get('/', [DocumentController::class, 'index'])->name('index'); // Liste des document
    Route::get('/create', [DocumentController::class, 'create'])->name('create'); // Formulaire de création
    Route::post('/', [DocumentController::class,'store'])->name('store'); // Création d'un document
    Route::get('/byCategory/{id}/', [DocumentController::class, 'byCategory'])->name('byCategory'); // Formulaire de modification
    Route::get('/documents/{document}', [DocumentController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [DocumentController::class, 'edit'])->name('edit'); // Formulaire d'édition
    Route::put('/{id}', [DocumentController::class, 'update'])->name('update'); // Mise à jour d'un document
    Route::delete('/{id}', [DocumentController::class, 'destroy'])->name('destroy'); // Suppression d'un document

    //favorites
    Route::post('/{document}/favorite', [DocumentController::class, 'addToFavorite'])->name('favorite'); //TODO: use this route asynchronously with a DOM update to avoir refresh page
    Route::get('/favorites', [DocumentController::class, 'favorites'])->name('favorites'); // Affichage de la liste des favoris
    Route::delete('/{document}/favorite', [DocumentController::class, 'removeFromFavorite'])->name('removeFavorite'); //TODO: use this route asynchronously with a DOM update to avoir refresh page

    //opened logs route
    Route::get('/{document}/logs', [DocumentController::class, 'logs'])->name('logs'); // Affichage des logs d'ouverture du document
    Route::post('/{document}/logs', [DocumentController::class, 'addLog'])->name('newLog'); // Création d'un log d'ouverture du document //TODO : change this route into an event that happen when a document is open.
    Route::get('/logs', [DocumentController::class, 'everyLogs'])->name('everyLogs');// récrupération de tout les logs d'ouvertures
    Route::get('/{user}/userLogs', [DocumentController::class, 'userLogs'])->name('userLogs');
    Route::get('/lastOpened', [DocumentController::class, 'lastOpenedDocuments'])->name('lastOpened');
});

//permissions routes

Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions.index');
Route::get('/permissions/pending', [PermissionController::class, 'pendingPermissions'])->name('pending-permissions');
Route::get('/permissions/request/{documentId}', [PermissionController::class, 'requestForm'])->name('permissions.requestForm');
Route::post('/permissions/create', [PermissionController::class, 'createRequest'])->name('permissions.create');
Route::post('/permissions/handle/{id}', [PermissionController::class, 'handleRequest'])->name('permissions.handle');
Route::delete('/permissions/cancel/{id}', [PermissionController::class, 'cancelRequest'])->name('permissions.cancel');
Route::delete('/permissions/delete/{id}', [PermissionController::class, 'destroy'])->name('permissions.destroy');
Route::get('/permissions/user/{id}', [PermissionController::class, 'userRequest'])->name('permissions.user');
Route::get('/permissions/document/{id}', [PermissionController::class, 'documentRequest'])->name('permissions.document');

// storage image route
Route::post('/upload-image', [ImageUploadController::class, 'store']);
