<?php
/**
 * Step 1: Require the Slim Framework
 *
 * If you are not using Composer, you need to require the
 * Slim Framework and register its PSR-0 autoloader.
 *
 * If you are using Composer, you can skip this step.
 */
require 'vendor/autoload.php';
require_once 'rb.php';
require_once 'config.php';
require_once 'classes/UserTool.php';
require_once 'classes/MessageManager.php';
require_once 'client/install.php';
require_once './classes/ErrorMessage.php';

\Slim\Slim::registerAutoloader();

/**
 * Step 2: Instantiate a Slim application
 *
 * This example instantiates a Slim application using
 * its default settings. However, you will usually configure
 * your Slim application now by passing an associative array
 * of setting names and values into the application constructor.
 */

$app = new \Slim\Slim();
$app->config(array('templates.path' => './client'));
/**
 * Step 3: Define the Slim application routes
 *
 * Here we define several Slim application routes that respond
 * to appropriate HTTP request methods. In this example, the second
 * argument for `Slim::get`, `Slim::post`, `Slim::put`, `Slim::patch`, and `Slim::delete`
 * is an anonymous function.
 */

// GET route
$app->get('/', function () use ($app)
{
    //get start page
    $app->render('client.html');
}
);

$app->get('/install', function () use ($app)
{
    if (file_exists(dirname(__FILE__).'/config.php')) 
    {
       echo "Configuration file has already been created!";
    } 
    else 
    {
       $host = $app->request()->get('host');
       $dbname = $app->request()->get('dbname');
       $dbuser = $app->request()->get('dbuser');
       $dbpass = $app->request()->get('dbpass');
       echo $host.$dbname.$dbuser.$dbpass;

       installer($host, $dbname, $dbuser, $dbpass);
       //$app->render('install.php');
    }
}
);

// section for registration
$app->post('/api/register', function () use ($app)
{
    $body = json_decode($app->request->getBody(), true);

    $usertool = new UserTool();
    echo $usertool->registerUser($body["username"], $body["password"], $body["invite_code"], $body["salt"], $body["public_key"], $body["private_key"]);
}
);


$app->get('/api/newsalt', function ()
{
    $usertool = new UserTool();
    echo $usertool->getNewPasswordSalt();
}
);


// section for login
$app->post('/api/login', function () use ($app)
{
    $body = json_decode($app->request->getBody(), true);
    $usertool = new UserTool();
    echo $usertool->loginUser($body["username"], $body["password"]);
}
);


$app->get('/api/salt', function () use ($app)
{
    $usertool = new UserTool();
    echo $usertool->getSaltForUser($app->request()->get('username'));
}
);



//section for compose message
$app->post('/api/message', function () use ($app)
{
    $body = json_decode($app->request->getBody(), true);
    $auth = $app->request->headers->get('Authorization');
    $token = substr($auth, 6);
    $usertool = new UserTool();
    $user = $usertool->getUserByToken($token);
    if($user != NULL)
    {
        $messagemanager = new MessageManager();
        echo $messagemanager->sendMessage($user->id, $body["recipient"], $body["content"]);
    }
    else
    {
        $error = new ErrorMessage("The access token is wrong!");
        echo json_encode($error);
    }
}
);



$app->get('/api/user', function () use ($app)
{
    $auth = $app->request->headers->get('Authorization');
    $token = substr($auth, 6);
    $usertool = new UserTool();
    $user = $usertool->getUserByToken($token);
    if($user != NULL)
    {
        echo $usertool->getUsernames();
    }
    else
    {
        $error = new ErrorMessage("The access token is wrong!");
        echo json_encode($error);
    }
}
);

$app->get('/api/user/:username/public_key', function ($username) use ($app)
{
    $auth = $app->request->headers->get('Authorization');
    $token = substr($auth, 6);
    $usertool = new UserTool();
    $messagemanager = new MessageManager();
    $user = $usertool->getUserByToken($token);
    if($user != NULL)
    {
        echo $messagemanager->getPublicKeyForUser($username);
    }
    else
    {
        $error = new ErrorMessage("The access token is wrong!");
        echo json_encode($error);
    }
}
);



//section for retrieve inbox messages
$app->get('/api/inbox', function () use ($app)
{
    $auth = $app->request->headers->get('Authorization');
    $token = substr($auth, 6);
    $usertool = new UserTool();
    $messagemanager = new MessageManager();
    $user = $usertool->getUserByToken($token);
    if($user != NULL)
    {
        echo $messagemanager->getMessagesForUser($user->id);
    }
    else
    {
        $error = new ErrorMessage("The access token is wrong!");
        echo json_encode($error);
    }
}
);


//section for deleting messages
$app->delete('/api/message/:m_id', function ($m_id) use ($app)
{
    $auth = $app->request->headers->get('Authorization');
    $token = substr($auth, 6);
    $usertool = new UserTool();
    $messagemanager = new MessageManager();
    $user = $usertool->getUserByToken($token);
    if($user != NULL)
    {
        echo $messagemanager->deleteMessage($user->id, $m_id);
    }
    else
    {
        $error = new ErrorMessage("The access token is wrong!");
        echo json_encode($error);
    }
}
);


//section for retrieving dashboard
$app->get('/api/dashboard', function () use ($app)
{
    $auth = $app->request->headers->get('Authorization');
    $token = substr($auth, 6);
    $usertool = new UserTool();
    $messagemanager = new MessageManager();
    $user = $usertool->getUserByToken($token);
    if($user != NULL)
    {
        echo $usertool->getDashboard($user->id);
    }
    else
    {
        $error = new ErrorMessage("The access token is wrong!");
        echo json_encode($error);
    }
}
);

// POST route
$app->post(
    '/post',
    function () {
        echo 'This is a POST route';
    }
);

// PUT route
$app->put(
    '/put',
    function () {
        echo 'This is a PUT route';
    }
);

// PATCH route
$app->patch('/patch', function () {
    echo 'This is a PATCH route';
});

// DELETE route
$app->delete(
    '/delete',
    function () {
        echo 'This is a DELETE route';
    }
);

/**
 * Step 4: Run the Slim application
 *
 * This method should be called last. This executes the Slim application
 * and returns the HTTP response to the HTTP client.
 */
$app->run();
