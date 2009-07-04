<?
import ideas.NewStorage ;
import ideas.NewUser ;
import ideas.NewIdea ;

class StorageWrapperNew {
    private $db ;

    function deleteAll() {
        $this->db->deleteAll();
    }

    function deleteUser($login) {
        $this->db->deleteUser($login);
    }

    function __construct() {
        $this->db = new NewStorage ;
    }

    function phpIdeaToJava($idea) {
        $i = new NewIdea();
        $i->status = (int) $idea->status;
        $i->priori = (int) $idea->priori;
        $i->id = $idea->id ;
        $i->login = $idea->login ;
        $i->setText($idea->idea) ;
        $i->x = (int) $idea->map->x ;
        $i->y = (int) $idea->map->y ;
        $i->date = (int) $idea->date ;
        foreach($idea->tags as $tag) {
            $i->tags->add($tag->tag);
        }
        return $i;
    }

    function phpUserToJava($user) {
        $u = new NewUser ;
        $u->login = $user->login ;
        $u->password  = $user->password;
        foreach($user->ideas as $idea) {
            $i = $this->phpIdeaToJava($idea);
            $i->login = $u->login ; 
            $u->ideas->add($i);
        }
        return $u;
    }

    function javaIdeaToPhp($idea) {
        $i = new StdClass ;
        $i->status ="". $idea->status;
        $i->id = $idea->id ;
        $i->priori = "". $idea->priori ;
        if($idea->idea != null) {
            $i->idea = $idea->idea->getValue();
        } else {
            $i->idea = "";
        }
        $i->date = "". $idea->date ;
        $i->login = $idea->login;
        $i->map = new StdClass ;
        $i->map->x = "". $idea->x ;
        $i->map->y = "". $idea->y ;
        $i->map->id_ideas = $idea->id ;
        $i->tags = array();
        if($idea->tags != null and !$idea->tags->isEmpty()) {
            for($c=0;$c<$idea->tags->size();$c++) {
                $t = new StdClass ;
                $t->tag = $idea->tags->get($c);
                $i->tags[] = $t ;
            }
        }
        return $i ;
    }

    function javaUserToPHP($user) {
        $u = new StdClass;
        $u->login = $user->login ;
        $u->password=  $user->password;
        $u->ideas = array();
        for($o=0; $o < $user->ideas->size();$o++) {
            $idea = $user->ideas->get($o);
            if($idea->status == 5) continue; 
            $i = $this->javaIdeaToPhp($idea);
            $u->ideas[] = $i ;
        }
        return $u;
    }

    function get($login) {
        return $this->getUser($login);
    }

    function getUser($login){
        $user = $this->db->get($login);
        if($user == null) {
            return null;
        }
        $user = $this->javaUsertoPHP($user);
        return $user;
    }

    function getIdea($id) {
        $idea = $this->db->getIdea("_".$id);
        $obj = $this->javaIdeaToPhp($idea);
        return $obj;
    }

    function put($login,$user) {
        $user = $this->phpUserToJava($user);
        $r = null;
        foreach($user->ideas as $idea) {
            $r .= $idea->idea->getValue();
        }
        $this->db->put($login,$user);
    }

    function putUser($login,$user) {
        return $this->put($login,$user);
    }


    function putIdea($login,$idea) {
        $obj = $this->phpIdeaToJava($idea);
        $r = $obj->idea->getValue();
        $this->db->putIdea($login,$obj);
    }

    function close() {
        $this->db->close();
    }
}
?>
