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
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\AuthMiddleware;
use App\Http\Middleware\GuestMiddleware;

//----------------------------------------------------------------/
// --------------------------Guest routes -----------------------/
//----------------------------------------------------------------/
Route::middleware(GuestMiddleware::class)->group(function(){
    Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'reset'])->name('password.update');
    Route::get('/subscribe', [SubscriptionController::class,'subscribe'])->name(name: 'subscribe');
    Route::post('/subscribe', [SubscriptionController::class,'store'])->name(name: 'subscribe.store');
    Route::get('/register/{token}',[SubscriptionController::class,'choosePassword'])->name('register.complete');
    Route::post('/register',[SubscriptionController::class,'completeRegistration'])->name('register.finalization');
    // Auth routes
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});
//----------------------------------------------------------------/
// --------------------------Authentified routes -----------------/
//----------------------------------------------------------------/
Route::middleware(AuthMiddleware::class)->group(function(){
    Route::get(uri: '/', action: function () {return view(view: 'welcome');})->name('home');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    //credentials mmanager routes
    Route::get('/credentials', [CredentialController::class, 'index'])->name('credentials.index');
    Route::get('/credentials/create', [CredentialController::class, 'create'])->name('credentials.create');
    Route::post('/credentials', [CredentialController::class, 'store'])->name('credentials.store');
    Route::get('/credentials/{id}/edit', [CredentialController::class, 'edit'])->name('credentials.edit');
    Route::put('/credentials/{id}', [CredentialController::class, 'update'])->name('credentials.update');
    Route::delete('/credentials/{id}', [CredentialController::class, 'destroy'])->name('credentials.destroy');
    // categories routes
    Route::prefix('categories')->name('categories.')->group(function () {
        Route::get('/myCategories', [CategoryController::class,'getUserCategories'])->name('myCategories');
    });
    Route::get('/create-documents', [DocumentController::class, 'create'])->name('create-documents'); // Formulaire de création
    //permissions routes
    Route::get('/permissions/request/{documentId}', [PermissionController::class, 'requestForm'])->name('permissions.requestForm');
    Route::post('/permissions/create', [PermissionController::class, 'createRequest'])->name('permissions.create');
    // storage image route
    Route::post('/upload-image', [ImageUploadController::class, 'store']);

    Route::prefix('documents')->name('documents.')->group(function () {
        Route::get('/', [DocumentController::class, 'index'])->name('index'); // Liste des document
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
    });



});

/*************************************************************************************************/
/*************************************************************************************************/
// -----------------------------------------admin routes ----------------------------------------/
/*************************************************************************************************/
/*************************************************************************************************/
Route::middleware(AdminMiddleware::class)->group(function () {
    //INDEX AND USER REGISTRATION
    Route::get('/admin', [AdminController::class, 'index'])->name('admin');
    Route::get('/admin/register', [SubscriptionController::class,'createUserInvitationForm'])->name('admin.register');
    Route::post('/admin/register', [SubscriptionController::class, 'createUserInvitation'])->name('admin.create-user');
    Route::post('/admin/subscribe/{id}',[SubscriptionController::class,'process'])->name('subscribe.process');
    // CATEGORIES
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index'); // Liste des catégories
    Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create'); // Formulaire de création
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store'); // Création d'une catégorie
    Route::get('/categories/{id}/edit', [CategoryController::class, 'edit'])->name('categories.edit'); // Formulaire de modification
    Route::put('/categories/{id}', [CategoryController::class, 'update'])->name('categories.update'); // Mise à jour d'une catégorie
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])->name('categories.destroy'); // Suppression d'une catégorie
    //ROLES
    Route::get(uri: '/roles',action: [RoleController::class,'create'])->name(name: 'roles.create');
    Route::get(uri: '/roles/{id}/edit', action: [RoleController::class, 'edit'])->name(name: 'roles.edit');
    Route::delete(uri: '/roles/{id}', action: [RoleController::class, 'destroy'])->name(name: 'roles.destroy');
    Route::post(uri: '/roles',action: [RoleController::class,'insert'])->name(name: 'roles.insert');
    Route::put(uri: '/roles/{id}', action: [RoleController::class, 'update'])->name(name: 'roles.update');
    // PERMISSIONS
    Route::post('/permissions/handle/{id}', [PermissionController::class, 'handleRequest'])->name('permissions.handle');
    Route::delete('/permissions/cancel/{id}', [PermissionController::class, 'cancelRequest'])->name('permissions.cancel');
    Route::delete('/permissions/delete/{id}', [PermissionController::class, 'destroy'])->name('permissions.destroy');
    Route::get('/permissions/user/{id}', [PermissionController::class, 'userRequest'])->name('permissions.user');
    Route::get('/permissions/document/{id}', [PermissionController::class, 'documentRequest'])->name('permissions.document');
    Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions.index');
    Route::get('/permissions/pending', [PermissionController::class, 'pendingPermissions'])->name('pending-permissions');

    //opened logs route
    Route::get('/{document}/logs', [DocumentController::class, 'logs'])->name('logs'); // Affichage des logs d'ouverture du document
    Route::get('/logs', [DocumentController::class, 'everyLogs'])->name('everyLogs');// récrupération de tout les logs d'ouvertures
    Route::get('/{user}/userLogs', [DocumentController::class, 'userLogs'])->name('userLogs');
    Route::get('/lastOpened', [DocumentController::class, 'lastOpenedDocuments'])->name('lastOpened');
});
