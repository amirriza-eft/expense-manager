<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/userguide3/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'home';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

$route['login'] = 'Auth/login';
$route['signup'] = 'Auth/register';
$route['profile'] = 'Auth/profile';


//=== Auth ===//
$route['api/auth/login']['POST'] =
    'api/Auth_API_Controller/login';

$route['api/auth/logout']['POST'] =
    'api/Auth_API_Controller/logout';

$route['api/auth/restore']['POST'] =
    'api/Auth_API_Controller/restore';

$route['api/auth/register']['POST'] =
    'api/Auth_API_Controller/register';

$route['api/user/delete'] =
    'api/Auth_API_Controller/delete_account';


//=== Profile ===//
$route['api/profile']['GET'] =
    'api/Profile_API_Controller/get';

$route['api/profile/update'] =
    'api/Profile_API_Controller/update';

$route['api/profile/password'] =
    'api/Profile_API_Controller/change_password';



//=== Dashboard ===//
$route['api/dashboard']['GET'] =
    'api/Dashboard_API_Controller/summary';



//=== Chart ===//
$route['api/chart']['GET'] =
    'api/Chart_API_Controller/chart';



//=== Transactions ===//
$route['api/transaction']['GET'] =
    'api/Transaction_API_Controller/index';

$route['api/transaction/create'] ['POST'] =
    'api/Transaction_API_Controller/create';

$route['api/transaction/update/(:num)'] =
    'api/Transaction_API_Controller/update/$1';

$route['api/transaction/delete/(:num)'] =
    'api/Transaction_API_Controller/delete/$1';

$route['api/transaction/deleted']['GET'] =
    'api/Transaction_API_Controller/deleted';

$route['api/transaction/restore']['POST'] =
    'api/Transaction_API_Controller/restore';



//=== Categories ===//
$route['api/categories']['GET'] =
    'api/Category_API_Controller/index';

$route['api/categories/create']['POST'] =
    'api/Category_API_Controller/create';

$route['api/categories/update/(:num)'] =
    'api/Category_API_Controller/update/$1';

$route['api/categories/delete/(:num)'] =
    'api/Category_API_Controller/delete/$1';

$route['api/categories/deleted']['GET'] =
    'api/Category_API_Controller/deleted';

$route['api/categories/restore/(:num)']['POST'] =
    'api/Category_API_Controller/restore/$1';
