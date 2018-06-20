<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'App\AppController@index')->name('home');
Route::get('/login', 'App\LoginController@login')->name('login');
Route::get('/authorize', 'App\LoginController@get_token');
Route::get('/process', 'App\LoginController@auth_login')->name('process_login');
Route::get('/logout', 'App\LoginController@logout')->name('logout');
Route::get('/test', 'App\AppController@process');

Route::prefix('portal')->middleware('auth')->group(function(){
    Route::get('/', 'Portal\DashboardController@index')->middleware('permission:dashboard')->name('portal');

    Route::group(['prefix' => 'departments-and-units', 'middleware' => 'permission:settings'], function () {
		$con = 'Portal\DepartmentController@';
		$rkey = 'portal.depts';
		Route::get('/', $con.'index')->name($rkey);
		Route::post('/add', $con.'storeDept')->middleware('permission:create-department')->name($rkey.'.add');
		Route::post('/edit', $con.'updateDept')->middleware('permission:update-department')->name($rkey.'.update');
		Route::post('/delete', $con.'deleteDept')->middleware('permission:delete-department')->name($rkey.'.delete');
		Route::get('/view/department/{id}', $con.'showDept')->middleware('permission:read-department')->name($rkey.'.show');
		Route::post('/add-unit', $con.'storeUnit')->middleware('permission:create-unit')->name($rkey.'.add.unit');
		Route::post('/update-unit', $con.'updateUnit')->middleware('permission:update-unit')->name($rkey.'.update.unit');
		Route::post('/delete-unit', $con.'deleteUnit')->middleware('permission:delete-unit')->name($rkey.'.delete.unit');
		Route::get('/view/unit/{id}', $con.'showUnit')->middleware('permission:read-unit')->name($rkey.'.show.unit');
    });

    Route::group(['prefix' => 'users'], function () {
		$con = 'Portal\UserController@';
		$rkey = 'portal.users';
		Route::get('/', $con.'index')->middleware('permission:view-users')->name($rkey);
		Route::post('/add', $con.'store')->middleware('permission:create-user')->name($rkey.'.add');
		Route::post('/update', $con.'update')->middleware('permission:update-user')->name($rkey.'.update');
		Route::post('/delete', $con.'delete')->middleware('permission:delete-user')->name($rkey.'.delete');
		Route::get('/view/{id}', $con.'show')->middleware('permission:read-user')->name($rkey.'.show');
    });

    Route::resource('roles','Portal\RoleController')->except(['edit','create'])->middleware([
        'index' => 'permission:create-permission',
        'create' => 'permission:create-permission'
    ]);

    Route::group(['prefix' => 'roles'], function () {
		$con = 'Portal\RoleController@';
        $rkey = 'roles';
		Route::put('{name}/edit-description/', $con.'edit_description')->name($rkey.'.ed');
		Route::get('{name}/add-to/all-users/', $con.'to_users')->name($rkey.'.tousers');
		Route::get('{name}/remove-from/all-users/', $con.'from_users')->name($rkey.'.fromusers');
    });

    Route::resource('permissions','Portal\PermissionsController')->except(['edit','create']);

    Route::group(['prefix' => 'permissions'], function () {
		$con = 'Portal\PermissionsController@';
        $rkey = 'permissions';
		Route::put('{name}/edit-description/', $con.'edit_description')->name($rkey.'.ed');
        Route::get('{name}/add-to/all-users/', $con.'to_users')->name($rkey.'.tousers');
        Route::get('{name}/remove-from/all-users/', $con.'from_users')->name($rkey.'.fromusers');
		Route::get('{name}/add-to/all-roles/', $con.'to_roles')->name($rkey.'.toroles');
        Route::get('{name}/remove-from/all-roles/', $con.'from_roles')->name($rkey.'.fromroles');
	});

});
