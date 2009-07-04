<?
include 'utils/restserver/RestClient.class.php';
include 'utils/zend_json/Json.php';


$base = "http://localhost:8080";

// Authorized access should be forbidden
$http = RestClient::get($base."/ideas.json");
if($http->getResponseCode() != 401) {
    echo "Unauthorized access: Wrong response code\n";
    echo $http->getResponseCode()."\n";
    return false;
}

if(trim($http->getResponse()) != "Unauthorized") {
    echo "Unauthorized access: Wrong response\n";
    echo $http->getResponse()."\n";
    return false;
}

// Will try to create a null user , should fail
$http = RestClient::post($base."/users.json",array("login"=>"","password"=>""));
if($http->getResponseCode() != 400) {
    echo "Create null user1: Wrong code\n";
    echo $http->getResponseCode()."\n";
    return false;
}

// Will try to create a null user , should fail
$http = RestClient::post($base."/users.json",array("login"=>"asdh;kasdh.asd_asd","password"=>"123"));
if($http->getResponseCode() != 400) {
    echo "Create null user2: Wrong code\n";
    echo $http->getResponseCode()."\n";
    return false;
}

// Will try to create a user that already exists, should fail
$http = RestClient::post($base."/users.json",array("login"=>'diogok',"password"=>"123"));
if($http->getResponseCode() != 400) {
    echo "Create null user3: Wrong code\n";
    echo $http->getResponseCode()."\n";
    return false;
}


$http  = RestClient::delete($base."/users/test.json",null,"test","test");

// will create a user, should go
$http = RestClient::post($base."/users.json",array("login"=>'test',"password"=>"test"));
if($http->getResponseCode() != 201) {
    echo "Create user ok: Wrong code\n";
    echo $http->getResponseCode()."\n";
    echo $http->getResponse()."\n";
    return false;
}

// will authenticate user
$http = RestClient::get($base."/users/test.json",null,"test","test");
if($http->getResponseCode() != 200) {
    echo "Fail authr test: Wrong code\n";
    echo $http->getResponseCode()."\n";
    echo $http->getResponse()."\n";
    return false;
}

// delete test user
$http  = RestClient::delete($base."/users/test.json",null,"test","test");
if(trim($http->getResponse()) != "DELETED") {
    echo "Fail deleting user\n";
    echo $http->getResponse()."\n";
    return false;

}


// Will now create an idea with some null stuff, should fail
$http = RestClient::post($base."/ideas.json",array("idea"=>"I Rock"),"diogok","123");
if($http->getResponseCode() != 400) {
    echo "Create null idea: Wrong code\n";
    echo $http->getResponseCode()."\n";
    return false;
}

// Will now create an idea , should create
$http = RestClient::post($base."/ideas.json",array("idea"=>"I Rock","tags"=>"test","priori"=>"1"),"diogok","123");
if($http->getResponseCode() != 201) {
    echo "Create idea: not created.n";
    echo $http->getResponseCode()."\n";
    echo $http->getResponse()."\n";
    return false;
}
$ideaJson = $http->getResponse(); // This is the idea created;
$idea = Zend_JSon::decode($ideaJson,Zend_Json::TYPE_OBJECT);
if($idea->idea != "I Rock") {
    echo "Idea not created properly: Wrong idea\n";
    var_dump($idea);
    return false;
}
if($idea->status != "1") {
    echo "Idea not created properly: Wrong status\n";
    var_dump($idea);
    return false;
}
if($idea->tags[0]->tag != "test") {
    echo "Idea not created properly: Wrong tags\n";
    var_dump($idea);
    return false;
}
if($idea->priori != "1") {
    echo "Idea not created properly: Wrong priori\n";
    var_dump($idea);
    return false;
}

$id = $idea->id ;

// Will recover this idea to make sure its ok
$http = RestClient::get($base."/ideas/".$id.".json",null,"diogok","123");
if($http->getResponseCode() != 200) {
    echo "Get idea: not OK.n\n";
    echo $http->getResponseCode()."\n\n";
    echo $http->getResponse()."\n\n";
    return false;
}
$ideaJson = $http->getResponse(); // This is the idea created;
$idea = Zend_JSon::decode($ideaJson,Zend_Json::TYPE_OBJECT);
if($idea->idea != "I Rock") {
    echo "Idea not GOT properly: Wrong idea\n";
    var_dump($idea);
    return false;
}
if($idea->status != "1") {
    echo "Idea not GOT properly: Wrong status\n";
    var_dump($idea);
    return false;
}
if($idea->tags[0]->tag != "test") {
    echo "Idea not GOT properly: Wrong tags\n";
    var_dump($idea);
    return false;
}
if($idea->priori != "1") {
    echo "Idea not GOT properly: Wrong priori\n";
    var_dump($idea);
    return false;
}


// Will recover this idea from list to make sure its ok
$http = RestClient::get($base."/ideas.json",null,"diogok","123");
if($http->getResponseCode() != 200) {
    echo "Get idea: not OK.n\n";
    echo $http->getResponseCode()."\n\n";
    echo $http->getResponse()."\n\n";
    return false;
}
$ideaJson = $http->getResponse(); // This is the idea created;
$ideas = Zend_JSon::decode($ideaJson,Zend_JSon::TYPE_OBJECT);
$idea = null ;
foreach($ideas as $i) {
    if($i->id === $id) {
        $idea = clone $i;
        break;
    } 
}

if($idea == null) {
    echo "Idea not found on main list!";
    var_dump($ideas);
}

if($idea->idea != "I Rock") {
    echo "Idea not GOT on main list properly: Wrong idea\n";
    var_dump($idea);
    return false;
}
if($idea->status != "1") {
    echo "Idea not GOT on main list properly: Wrong status\n";
    var_dump($idea);
    return false;
}
if($idea->tags[0]->tag != "test") {
    echo "Idea not GOT on main list properly: Wrong tags\n";
    var_dump($idea);
    return false;
}
if($idea->priori != "1") {
    echo "Idea not GOT on main list properly: Wrong priori\n";
    var_dump($idea);
    return false;
}

// Will test tags now
$http = RestClient::get($base."/tags.json",null,"diogok","123");
if($http->getResponseCode() != 200) {
    echo "Get tags response code not ok\n";
    var_dump($http->getResponseCode());
    var_dump($http->getResponse());
    return false;
}

$tagsJson = $http->getResponse();
$tags = Zend_JSon::decode($tagsJson);
$ok = false;
foreach($tags as $tag) {
    if($tag == "test") {
        $ok = true;
        break;
    }
}
if(!$ok) {
    echo "Inserted tag not found on tags.json";
    var_dump($tags);
    return false;
}


// Will update my idea
$http = RestClient::post($base."/ideas/".$id.".json",
        array("idea"=>"I really rock","priori"=>"2","status"=>"2","tags"=>"test2"),
        "diogok","123");
if($http->getResponseCode() != 200 ){
    echo "Something is wrong at update idea\n";
    echo $http->getResponseCode()."\n";
    echo $http->getResponse()."\n";
    return false;
}

$ideaJson = $http->getResponse(); // This is the idea updated;
$idea = Zend_JSon::decode($ideaJson,Zend_Json::TYPE_OBJECT);
if($idea->idea != "I really rock") {
    echo "Idea not updated properly: Wrong idea\n";
    var_dump($idea);
    return false;
}
if($idea->status != "2") {
    echo "Idea not updated properly: Wrong status\n";
    var_dump($idea);
    return false;
}
if($idea->tags[0]->tag != "test2") {
    echo "Idea not updated properly: Wrong tags\n";
    var_dump($idea);
    return false;
}
if($idea->priori != "2") {
    echo "Idea not updated properly: Wrong priori\n";
    var_dump($idea);
    return false;
}


// Will recover this idea from list to make sure its ok
$http = RestClient::get($base."/ideas.json",null,"diogok","123");
if($http->getResponseCode() != 200) {
    echo "Get idea: not OK.n\n";
    echo $http->getResponseCode()."\n\n";
    echo $http->getResponse()."\n\n";
    return false;
}
$ideaJson = $http->getResponse(); // This is the idea created;
$ideas = Zend_JSon::decode($ideaJson,Zend_JSon::TYPE_OBJECT);
$idea = null ;
foreach($ideas as $i) {
    if($i->id === $id) {
        $idea = clone $i;
        break;
    } 
}

if($idea == null) {
    echo "Idea not found on main list!";
    var_dump($ideas);
}

if($idea->idea != "I really rock") {
    echo "Idea not GOT updated on main list properly: Wrong idea\n";
    var_dump($idea);
    return false;
}
if($idea->status != "2") {
    echo "Idea not GOT updated on main list properly: Wrong status\n";
    var_dump($idea);
    return false;
}
if($idea->tags[0]->tag != "test2") {
    echo "Idea not GOT updated on main list properly: Wrong tags\n";
    var_dump($idea);
    return false;
}
if($idea->priori != "2") {
    echo "Idea not GOT updated on main list properly: Wrong priori\n";
    var_dump($idea);
    return false;
}

// save map
$http = RestClient::post($base."/ideas/".$id."/map.json",array("x"=>100,"y"=>200),"diogok","123");
if($http->getResponseCode() != 200) {
    echo "Erro at save map\m";
    echo $http->getResponseCode()."\n";
    echo $http->getResponse()."\n";
    return false;
}

$ideaJson = $http->getResponse(); // This is the idea updated;
$idea = Zend_JSon::decode($ideaJson,Zend_Json::TYPE_OBJECT);

if($idea->map->x != "100" || $idea->map->y != "200") {
    echo "Error in return idea map\n";
    var_dump($idea);
    return false;
}


// And now we recover the maps
$http = RestClient::get($base."/map.json",null,"diogok","123");
if($http->getResponseCode() != 200) {
    echo "Erro at get map\m";
    echo $http->getResponseCode()."\n";
    echo $http->getResponse()."\n";
    return false;
}

$mapJson = $http->getResponse(); // This is the idea updated;
$maps = Zend_JSon::decode($mapJson,Zend_Json::TYPE_OBJECT);
$ok = false;
foreach($maps as $m) {
    if($m->id_ideas == $id) {
        if($m->x == "100" and $m->y == "200") {
            $ok = true;
        }
    }
}

if(!$ok) {
    echo "Sorry, the map came wrong\n";
    var_dump($maps);
    return false;
}


// now i clean the mess on the database
$http = RestClient::get($base."/ideas.json",null,"diogok","123");
$ideasJson = $http->getResponse();
$ideas = Zend_JSon::decode($ideasJson,Zend_JSon::TYPE_OBJECT);
$toDelete  = array();
foreach($ideas as $idea) {
    if($idea->tags[0]->tag == "test" or $idea->tags[0]->tag == "test2") {
        $toDelete[] = $idea->id ;
    }
}

foreach($toDelete as $id) {
    $http  = RestClient::delete($base."/ideas/".$id.".json",null,"diogok","123");
}


echo "Success\n";

return true;


?>
