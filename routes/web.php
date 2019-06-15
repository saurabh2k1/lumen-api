<?php

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

/**
 * Includes a set of security-focused middeware
 *
 * You can see more info on each of them in the Http/Middleware folder
 *
 * Feel free to add/edit/remove Middlewares
 */

 

$router->group(
    [
        'prefix' => '',
        'middleware' => ['nocache', 'hideserver', 'security', 'csp', 'cors']
    ],
    function () use ($router) {

    /**
     * Routes that do not require a JWT
     *
     * Different routes have different combinations based on use case.
     */

    // $router->get('site/{id}/studies', 'SiteController@getSiteStudies');
    $router->get('/site/{siteId}/{studyId}/dashboard', 'SiteController@getDashboard');
    $router->get('/patients', 'PatientController@getAll');
    $router->get('/patient/{id}', 'PatientController@getPatient');
    $router->post('/patient/update/{id}', 'PatientController@update');
    $router->get('/patients/{siteID}/{studyID}', 'PatientController@getPatients');
    $router->post('/patient/new', 'PatientController@new');
    $router->get('/visits', 'VisitController@getAll');
    $router->get('/visits/{studyID}', 'VisitController@getVisits');
    $router->post('visit/new', 'VisitController@new');
    $router->get('/visit/{id}', 'VisitController@getVisit');
    $router->post('/visit/update/{id}', 'VisitController@updateVisit');
   
    $router->get('form/crf/{id}', 'FormController@getForm');
    $router->post('form/crf', 'FormController@saveCRF');
    
    $router->get('forms/{id}', 'FormController@getAllForms');
    $router->post('/site/form/exclusion', 'SiteFormController@saveExclusion');
    $router->get('/site/form/exclusion/{id}', 'SiteFormController@getExclusion');
    $router->get('/site/patient/{id}/visits', 'VisitController@getVisitByPatient');
    $router->get('/allstudies', 'StudyController@getAllStudies');
    $router->post('/patient/addvisit', 'PatientVisitController@addVisit');
    $router->get('/patient/getvisit/{patientID}/{visitID}', 'PatientVisitController@getVisit');
    $router->get('/getmedicalhistory/{patient_id}', 'MedicalhistoryController@getMedicalHistory');
    $router->post('/getmedicalhistory', 'MedicalhistoryController@save');
    $router->get('/patient/aerecords/{patID}', 'AerecordController@getByPatient');
    $router->get('/site/aerecords/{siteID}', 'AerecordController@getBySite');
    $router->get('aerecords', 'AerecordController@getAll');
    $router->get('aerecords/{id}', 'AerecordController@getById');
    $router->post('aerecords', 'AerecordController@new');
    $router->post('crfchange', 'CrfChangeController@create');
    $router->post('upload', 'FileController@saveFile');
    $router->get('upload/{patient_id}/{VisitID}', 'FileController@getFile');
    $router->post('site/conco', 'ConcoController@new');
    $router->get('site/conco/{patient_id}', 'ConcoController@get');
    $router->get('dashboard/crfcount/{studyID}/{siteID}', 'DashboardController@crfCount');


        /**
         *  Ensures that retrieving config is allowed with the correct app id
         *
         *  Ensure APP_ID in your .env
         *  Request with `App: your-key-here`
         */
        $router->group(['middleware' => ['throttle:10,1', 'appid']], function () use ($router) {
            $router->get('/config/app', 'ConfigController@getAppConfig');
        });

        /**
         *  Ensures that registration is only possible if you know the token.
         *
         *  Ensure REGISTRATION_ACCESS_KEY in your .env
         *  Request with `Registration-Access-Key: your-key-here`
         */
        $router->group(
            [
                'prefix' => 'register',
                'middleware' => ['register', 'throttle:3,1']
            ],
            function () use ($router) {
                $router->post('/email', 'RegistrationController@registerEmail');
            }
        );
 
        /**
         * 10 Login and Logouts per minute
         */
        $router->group(['middleware' => 'throttle:10,1'], function () use ($router) {
            $router->post('/login', 'AuthController@postLogin');
            $router->post('/logout', 'AuthController@logout');
        });

        /**
         * Only allow x of these requests per minute.
         *
         * Production should be a low number
         */
        $router->group(['middleware' => 'throttle:10,1'], function () use ($router) {
            $router->get('/confirm/{token}', 'RegistrationController@confirmEmail');
            $router->post('/reset', 'ResetController@postEmail');
            $router->post('/reset/{token}', 'ResetController@postReset');
        });

        $router->group(['prefix' => 'roles', 'middleware' => ['role:admin']], function () use ($router) {
            $router->get('/{roleId}/users', 'RolesController@getUsersForRole');
            $router->get('/{roleId}', 'RolesController@getRole');
            $router->get('/', 'RolesController@getRoles');
            $router->post('/{roleId}', 'RolesController@createRole');
            $router->delete('/{roleId}', 'RolesController@deleteRole');
            $router->post('/{roleId}/activate', 'RolesController@activateRole');
            $router->post('/{roleId}/deactivate', 'RolesController@deactivateRole');
        });

        $router->group(['prefix' => 'users', 'middleware' => ['role:admin']], function () use ($router) {
            $router->get('/{id}/roles', 'UserController@getUserRoles');
            $router->post('/{id}/roles/assign/{roleId}', 'UserController@assignRole');
            $router->post('/{id}/roles/revoke/{roleId}', 'UserController@revokeRole');
            $router->post('/update/{userId}', 'UserController@updateUser');
            $router->get('/', 'UserController@getAllUsers');
            $router->post('/resetpassword', 'UserController@resetPassword');

            
        });

        $router->group(['prefix' => 'sites', 'middleware' => ['role:admin']], function () use ($router) {
            // $router->get('/', 'SiteController@getSites');
            $router->get('/', 'SiteController@getSiteWithUsers');
            $router->post('/new', 'SiteController@createSite');
            $router->get('/get/{siteId}', 'SiteController@getSite');
            $router->post('/update/{siteID}', 'SiteController@updateSite');
            
        });

        $router->group(['prefix' => 'form', 'middleware' => ['role:admin']], function () use ($router) {
            $router->post('/new', 'FormController@new');
            $router->get('/{id}', 'FormController@get');
            $router->post('/{id}', 'FormController@update');
            $router->post('/field/{id}', 'CrfFormController@new');
            
        });

        $router->group(['prefix' => 'studies', 'middleware' => ['role:admin'] ], function () use ($router) {
            
            $router->get('/', 'StudyController@getStudies');
            $router->post('/new', 'StudyController@createStudy');
            $router->get('/get/{studyId}', 'StudyController@getStudy');
            $router->post('/update/{studyId}', 'StudyController@updateStudy');

        });

        /**
         * What you set this throttle to depends on your use case.
         * JWT refresh
         */
        $router->group(['middleware' => ['jwt.refresh', 'throttle:10,1']], function () use ($router) {
            $router->post('/refresh', 'AuthController@refresh');
        });

        /**
         * Authenticated Routes
         */
        $router->group(['prefix' => 'api', 'middleware' => ['auth']], function () use ($router) {
            $router->get('/', function () use ($router) {
                return $router->app->version();
            });

            /**
             * Users
             */
            $router->get('/me', 'UserController@getSelf');
            $router->post('/users/change-password', 'UserController@changePassword');
            $router->get('/users/{userId}', 'UserController@getUserById');
            $router->post('/users/{userId}', 'UserController@updateUserByUUID');
            
            // $router->get('/site', 'UserController@getUserSite');
            $router->get('/site', 'SiteController@getMySite');
            $router->get('/site/studies', 'SiteController@getSiteStudy');
            
        });
    }
);
