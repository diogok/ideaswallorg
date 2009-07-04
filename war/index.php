<?

include 'utils/restserver/RestServer.class.php';
include 'utils/zend_json/Json.php';
include 'controller/UserController.class.php';
include 'controller/IdeasController.class.php';
include 'StorageWrapper.new.php';

// RestServer is responsable for URL handling 
$q = $_GET["q"];
$rest = new RestServer($q) ;

// Make the Repository available on the RestServer, that will be present everywhere
$db = new StorageWrapperNew ;
$rest->setParameter("db",$db);

// Authentication part
$rest->requireAuth(true);
$login = $rest->getRequest()->getUser();
$login = strtolower($login);
$password = $rest->getRequest()->getPassword();

$user = $rest->getParameter("db")->get($login);

if($user == false or $user == null ){
    $rest->setAuth(false);
} else if($user->login === $login and $user->password === $password) {
    $rest->setAuth(true);
} else { // Just in case
    $rest->setAuth(false);
}

// URL mapping to Controllers
$rest->addMap("GET","/ideas.json","IdeasController");
$rest->addMap("GET","/tags.json","IdeasController::tags");
$rest->addMap("GET","/map.json","IdeasController::map");
$rest->addMap("GET","/ideas/[a-zA-Z0-9]+.json","IdeasController::idea");

$rest->addMap("POST","/ideas.json","IdeasController::insert");
$rest->addMap("POST","/sync.json","IdeasController::sync");
$rest->addMap("POST","/ideas/[a-zA-Z0-9]+.json","IdeasController::update");
$rest->addMap("POST","/ideas/[a-zA-Z0-9]+/map.json","IdeasController::saveMap");

if($rest->getRequest()->getURI() == "/users.json" ) {
    $rest->requireAuth(false);
    $rest->setAuth(true);
}

$rest->addMap("POST","/users.json","UserController::insert"); // This will not need auth,
$rest->addMap("GET","/users/any.json","UserController"); // This will just test auth,
$rest->addMap("GET","/users/[a-zA-Z0-9]+.json","UserController"); // This will just test auth,
$rest->addMap("DELETE","/users/[\w]+.json","UserController::delete"); 

// This will giveme trouble, i know
$rest->addMap("DELETE","/ideas/[a-zA-Z0-9]+.json","IdeasController::delete");

//Run the Server
echo $rest->execute()."\n";

$db->close();
?>
