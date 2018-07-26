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
Route::get('/test', 'App\AppController@test');

Route::prefix('portal')->middleware(['auth','is-user'])->group(function(){
    Route::get('/', 'Portal\DashboardController@index')->middleware('permission:dashboard')->name('portal');

    Route::post('/', 'Portal\DashboardController@read_notif')->name('read.notif');

    Route::resource('roles','Portal\RoleController')->except(['edit','create']);

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

    Route::group(['prefix' => 'departments-and-units'], function () {
		$con = 'Portal\DepartmentController@';
		$rkey = 'portal.depts';
		Route::get('/', $con.'index')->name($rkey);
		Route::post('/add', $con.'storeDept')->name($rkey.'.add');
		Route::post('/edit', $con.'updateDept')->name($rkey.'.update');
		Route::post('/delete', $con.'deleteDept')->name($rkey.'.delete');
		Route::get('/view/department/{id}', $con.'showDept')->name($rkey.'.show');
		Route::post('/add-unit', $con.'storeUnit')->name($rkey.'.add.unit');
		Route::post('/update-unit', $con.'updateUnit')->name($rkey.'.update.unit');
		Route::post('/delete-unit', $con.'deleteUnit')->name($rkey.'.delete.unit');
		Route::get('/view/unit/{id}', $con.'showUnit')->name($rkey.'.show.unit');
    });

    Route::group(['prefix' => 'users'], function () {
		$con = 'Portal\UserController@';
		$rkey = 'portal.users';
		Route::get('/', $con.'index')->name($rkey);
		Route::post('/add', $con.'store')->name($rkey.'.add');
		Route::post('/update', $con.'update')->name($rkey.'.update');
		Route::post('/delete', $con.'delete')->name($rkey.'.delete');
        Route::get('/view/{id}', $con.'show')->name($rkey.'.show');
        Route::resource('managers','Portal\ManagerController')->except(['edit','create']);
    });

    Route::group(['prefix' => 'leave'], function () {
		Route::resource('leave-record','Portal\Leave\LeaveTypeController');

        Route::resource('leave-type','Portal\Leave\LeaveTypeController')->except(['edit','create']);

        Route::resource('leave-allocation','Portal\Leave\LeaveAllocationController');

        Route::resource('holiday','Portal\Leave\HolidayController')->except(['edit','create','show']);

        Route::post('/allocation/assign-to-all','Portal\Leave\LeaveAllocationController@to_users')->name('leave-allocation.toall');

        Route::group(['prefix' => 'my-leave'], function () {
            $con = 'Portal\Leave\LeaveController@';
            $rkey = 'portal.leave';
            Route::get('/', $con.'index')->name($rkey);
            Route::get('/apply', $con.'create')->name($rkey.'.apply');
            Route::post('/store', $con.'store')->name($rkey.'.store');
            Route::get('/{id}/edit', $con.'edit')->name($rkey.'.edit');
            Route::post('/update', $con.'update')->name($rkey.'.update');
            Route::get('/{id}/delete', $con.'destroy')->name($rkey.'.delete');
            Route::get('/{id}/cancel', $con.'cancel')->name($rkey.'.cancel');
            Route::post('/get-date', $con.'get_cdate');
        });

        Route::group(['prefix' => 'requests'], function () {
            $con = 'Portal\Leave\LeaveRequestController@';
            $rkey = 'portal.leave.request';
            Route::get('/', $con.'index')->name($rkey);
            Route::get('/{code}', $con.'show')->name($rkey.'.show');
            Route::post('/manager-decision', $con.'manager_action')->name($rkey.'.ma');
            Route::post('/hr-decision', $con.'hr_action')->name($rkey.'.ma');
        });

        // Route::resource('my-leave','Portal\Leave\LeaveController')->except(['create']);
    });

    Route::group(['prefix' => 'kpi'], function () {
        Route::group(['prefix' => 'settings'], function () {
            $con = 'Portal\KPI\KpisController@';
            $rkey = 'portal.kpi.settings';
            Route::get('/', $con.'index')->name($rkey);
            Route::get('/apply', $con.'create')->name($rkey.'.apply');
            Route::post('/store', $con.'store')->name($rkey.'.store');
            Route::get('/{id}/edit', $con.'edit')->name($rkey.'.edit');
            Route::post('/update', $con.'update')->name($rkey.'.update');
            Route::get('/{title}/delete', $con.'destroy')->name($rkey.'.delete');
        });
    });

});
