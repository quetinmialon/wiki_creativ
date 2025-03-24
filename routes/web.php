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
    Route::prefix('documents')->name('documents.')->group(function () {
        Route::get('/', [DocumentController::class, 'index'])->name('index'); // Liste des document
        Route::post('/', [DocumentController::class,'store'])->name('store'); // Création d'un document
        Route::get('/byCategory/{id}/', [DocumentController::class, 'byCategory'])->name('byCategory'); // Formulaire de modification
        Route::get('/documents/{document}', [DocumentController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [DocumentController::class, 'edit'])->name('edit'); // Formulaire d'édition
        Route::put('/{id}', [DocumentController::class, 'update'])->name('update'); // Mise à jour d'un document
        Route::delete('/{id}', [DocumentController::class, 'destroy'])->name('destroy'); // Suppression d'un document
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

    // PERMISSIONS
    Route::post('/admin/permissions/handle/{id}', [PermissionController::class, 'handleRequest'])->name('permissions.handle');
    Route::delete('/admin/permissions/cancel/{id}', [PermissionController::class, 'cancelRequest'])->name('permissions.cancel');
    Route::delete('/admin/permissions/delete/{id}', [PermissionController::class, 'destroy'])->name('permissions.destroy');
    Route::get('/admin/permissions/user/{id}', [PermissionController::class, 'userRequest'])->name('permissions.user');
    Route::get('/admin/permissions/document/{id}', [PermissionController::class, 'documentRequest'])->name('permissions.document');
    Route::get('/admin/permissions', [PermissionController::class, 'index'])->name('admin.permissions');
    Route::get('/admin/permissions/pending', [PermissionController::class, 'pendingPermissions'])->name('admin.permissions.pendings');

    //opened logs route
    Route::get('/logs', [DocumentController::class, 'everyLogs'])->name('everyLogs');// récrupération de tout les logs d'ouvertures
    Route::get('/logs/{document}/logs', [DocumentController::class, 'logs'])->name('logs'); // Affichage des logs d'ouverture du document
    Route::get('/logs/{user}/userLogs', [DocumentController::class, 'userLogs'])->name('userLogs');

    //documents
    Route::get('/admin/documents', [DocumentController::class, 'getAllDocuments'])->name('admin.documents.index');

    //users
    Route::get('/admin/users', [AdminController::class, 'UserList'])->name('admin.users');
    Route::get('/admin/users/{id}/edit', [AdminController::class, 'EditUsersRole'])->name('admin.edit-users-role');
    Route::delete('/admin/users/{id}', [AdminController::class,'revokeUser'])->name('admin.delete-user');
    Route::put('/admin/users/{id}', [AdminController::class, 'updateUserRole'])->name('admin.update-user-roles');

    //roles
    Route::get('/admin/roles', [AdminController::class, 'RoleList'])->name('admin.roles');
    Route::get(uri: '/admin/roles/{id}/edit', action: [RoleController::class, 'edit'])->name(name: 'roles.edit');
    Route::delete(uri: '/admin/roles/{id}', action: [RoleController::class, 'destroy'])->name(name: 'roles.destroy');
    Route::post(uri: '/admin/roles',action: [RoleController::class,'insert'])->name(name: 'roles.insert');
    Route::put(uri: '/admin/roles/{id}', action: [RoleController::class, 'update'])->name(name: 'roles.update');
    Route::get(uri: '/admin/roles/create',action: [RoleController::class,'create'])->name(name: 'roles.create');

    //usersRequests
    Route::get('/admin/requests', [AdminController::class, 'UserRequests'])->name('admin.users-requests');
});
