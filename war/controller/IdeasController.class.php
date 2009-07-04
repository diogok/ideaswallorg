<?
// Statuses
// 1 = created
// 2 = ongoing
// 3 = done
// 4 = 
// 5 = trash

// Priori
// 0 = normal
// 1= medium
// 2 = high
//
function ideaSort($i1, $i2) {
    if($i->status < $i2->status) return $i1;
    if($i->status > $i2->status) return $i2;
    if($i->priori < $i2->priori) return $i2;
    if($i->priori > $i2->priori) return $i1;
    if($i->date < $i2->date) return $i1;
    if($i->date > $i2->date) return $i2;
    return $i1 ;
}

class IdeasController implements RestController {


    public function execute(RestServer $rest){
        $ideas = $rest->getParameter("db")->getUser($rest->getRequest()->getUser())->ideas ;
        foreach($ideas as $id) {
            $id->idea = utf8_encode($id->idea);
        }
        $json = Zend_JSon::encode($ideas);
        $rest->getResponse()->setResponse($json);
        return $rest;
    }

    public function idea(RestServer $rest){
        $id1 = $rest->getRequest()->getURIPart(2);
        $id = str_replace(".json","",$id1);//Request id

        $idea = $rest->getParameter("db")->getIdea($id);

        if($idea === null or $idea === false){
            $idea = new StdClass;
        }

        $json = Zend_JSon::encode($idea);
        $rest->getResponse()->setResponse($json);
        return $rest ;
    }

    public function sync(RestServer $rest) {
        $post = $rest->getRequest()->getPost();
        $user = $rest->getParameter("db")->getUser($rest->getRequest()->getUser());
        for($i =0;($json = $post["j".$i]);$i++) {
            $json = str_replace("'",'"',$json);
            $idea = Zend_JSon::Decode($json,Zend_JSon::TYPE_OBJECT);
            if(!isset($idea->id) or $idea->id == null or  $idea->id == "0") {
                if(!isset($idea->priori) or $idea->priori == null or !isset($idea->idea) or $idea->idea == null 
                    or !isset($idea->tags) or $idea->tags == null) {
                        $rest->getResponse()->addHeader("HTTP/1.1 400 Bad Request");
                        $rest->getResponse()->setResponse("Not all expected parameter were sent, or invalid parameters were sent.");
                        return $rest;
                        }
                $idea->status = 1; 
                $idea->id = md5(time().count($user->ideas).$user->login);
                $idea->date = time();
                $user->ideas[] = $idea ;
            } else {
                if(!isset($idea->priori) or $idea->priori == null or !isset($idea->idea) or $idea->idea == null 
                    or !isset($idea->tags) or $idea->tags == null or !isset($idea->status) or $idea->status == null) {
                        $rest->getResponse()->addHeader("HTTP/1.1 400 Bad Request");
                        $rest->getResponse()->setResponse("Not all expected parameter were sent, or invalid parameters were sent.");
                        return $rest;
                        }
                $ideaIn = $this->getIdea($rest,$idea->id);
                $ideaIn->status = $idea->status ;
                $ideaIn->priori = $idea->priori;
                $ideaIn->idea = $idea->idea;
            }
        }
        $rest->getParameter("db")->put($user->login,$user);
        return $this->execute($rest);
    }

    public function tags(RestServer $rest){
        $ideas = $rest->getParameter("db")->getUser($rest->getRequest()->getUser())->ideas ;

        $tags = array();
        $iHave = array();

        if(count($ideas) >= 1) {
           foreach($ideas as $idea) {
                if(count($idea->tags) >= 1) {
                    foreach($idea->tags as $tag) {
                        if(!isset($iHave[$tag->tag])) {
                            $iHave[$tag->tag] = true;
                            $tags[] = trim($tag->tag) ;
                        }
                    }
                }
            }
        }

        $json = Zend_JSon::encode($tags);
        $rest->getResponse()->setResponse($json);
        return $rest ;
    }

    public function map(RestServer $rest){
        $ideas = $rest->getParameter("db")->getUser($rest->getRequest()->getUser())->ideas ;

        $maps = array();

        if(count($ideas) >= 1) {
            foreach($ideas as $idea) {
                if(isset($idea->map) and $idea->map != null) {
                    $idea->map->id_ideas = $idea->id ;
                    $maps[] = $idea->map ;
                }
            }
        }

        $json = Zend_JSon::encode($maps);
        $rest->getResponse()->setResponse($json);
        return $rest ;
    }

    public function insert(RestServer $rest){
        $post = $rest->getRequest()->getPOST();

        $valid = true;
        if(!isset($post["idea"]) or !isset($post["priori"]) or !isset($post["tags"]) 
                    or strlen(trim($post["idea"])) < 1 or strlen(trim($post["tags"])) < 1) {
            $valid = false;
        }
        if(!$valid) {
            $rest->getResponse()->addHeader("HTTP/1.1 400 Bad Request");
            $rest->getResponse()->setResponse("Not all expected parameter were sent, or invalid parameters were sent.");
            return $rest;
        }

        $idea = new StdClass;
        $idea->idea = $post["idea"] ;
        $idea->date = time();
        $idea->priori = $post["priori"];
        $idea->status = 1 ;
        $idea->tags = array();

        $tags = explode(",",$post["tags"]);
        foreach($tags as $t) {
            $tag = new StdClass;
            $tag->tag = trim($t);
            $idea->tags[] = clone $tag;
        }

        $user = $rest->getParameter("db")->getUser($rest->getRequest()->getUser());
        $idea->id = md5(time().count($user->ideas).$user->login);

        $user->ideas[] = $idea ;
        //$rest->getParameter("db")->put($user->login,$user);
        $rest->getParameter("db")->putIdea($user->login,$idea);

        $idea->idea = utf8_encode($idea->idea);
        $json = Zend_Json::encode($idea);

        $rest->getResponse()->addHeader("HTTP/1.1 201 Created");
        $rest->getResponse()->setResponse($json);

        return $rest;
    }

    public function update(RestServer $rest){
        $id1 = $rest->getRequest()->getURIPart(2);
        $id = str_replace(".json","",$id1);
        $post = $rest->getRequest()->getPOST();
    
        $idea = $rest->getParameter("db")->getIdea($id);

        if($idea == null or $idea == false) {
            $rest->getResponse()->addHeader("HTTP/1.1 404 Not Found");
            return $rest;
        }

        $valid = true ;
        if(!isset($post["priori"]) or !isset($post["status"]) or !isset($post["idea"]) or !isset($post["tags"])) {
            $valid = false ;
        }

        if($post["priori"] != 1 and $post["priori"] != 2 and $post["priori"] != 0) {
            $valid = false ;
        }

        if($post["status"] != 1 and $post["status"] != 2 and $post["status"] != 3 and $post["status"] != 4 and $post["status"] != 5) {
            $valid = false;
        }
        
        if(strlen(trim($post["tags"])) < 1) {
            $valid = false;
        }

        if(!$valid) {
            $rest->getResponse()->addHeader("HTTP/1.1 400 Bad Request");
            $rest->getResponse()->setResponse("Not all expected parameter were sent, or invalid parameters were sent.");
            return $rest;
        }

        $idea->priori = $post["priori"];
        $idea->status = $post["status"];
        $idea->idea  = $post["idea"];
        $idea->tags = array();

        $tags = explode(",",$post["tags"]);
        foreach($tags as $t) {
            $tag = new StdClass;
            $tag->tag = trim($t);
            $idea->tags[] = $tag;
        }

        /*
        $user = $this->getUser($rest);
        foreach($user->ideas as $k=>$i) {
            if($i->id == $idea->id) {
                $user->ideas[$k] = $idea ;
            }
        }

        $rest->getParameter("db")->put($user->login,$user,true);
        */
        $login = $rest->getRequest()->getUser();
        $rest->getParameter("db")->putIdea($login,$idea);
        $idea->idea = utf8_encode($idea->idea);
        $json = Zend_Json::encode($idea);
        $rest->getResponse()->setResponse($json);
        return $rest;
    }

    public function saveMap(RestServer $rest){
        $post = $rest->getRequest()->getPOST();
        $id = $rest->getRequest()->getURIPart(2);

        $idea = $rest->getParameter("db")->getIdea($id);

        if($idea == null or $idea == false) {
            $rest->getResponse()->addHeader("HTTP/1.1 404 Not Found");
            return $rest;
        }

        if(!isset($post["x"]) or !isset($post["y"])){
            $rest->getResponse()->addHeader("HTTP/1.1 400 Bad Request");
            return $rest;
        }

        if(!isset($idea->map) or $idea->map == null) {
            $idea->map = new StdClass;
            $idea->map->id_ideas = $id;
        }

        $idea->map->x = $post["x"];
        $idea->map->y = $post["y"];

        $login = $rest->getRequest()->getUser();
        $rest->getParameter("db")->putIdea($login,$idea);
        $idea->idea = utf8_encode($idea->idea);
        $json = Zend_Json::encode($idea);
        $rest->getResponse()->setResponse($json);
        return $rest;
    }

    public function delete(RestServer $rest){
        $id1 = $rest->getRequest()->getURIPart(2);
        $id = str_replace(".json","",$id1);

        $idea = $rest->getParameter("db")->getIdea($id);

        if($idea == null or $idea == false) {
            $rest->getResponse()->addHeader("HTTP/1.1 404 Not Found");
            return $rest;
        }

        $idea->status = "5";

        $login = $rest->getRequest()->getUser();
        $rest->getParameter("db")->putIdea($login,$idea);

        $idea->idea = utf8_encode($idea->idea);
        $json = Zend_Json::encode($idea);
        $rest->getResponse()->setResponse($json);
        return $rest;
    }
}

?>
