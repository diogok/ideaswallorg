<?

class UserController implements  RestController {
    public function execute(RestServer $rest) {
        $login = $rest->getRequest()->getUser();
        $login = strtolower($login);
        $password = $rest->getRequest()->getPassword();
        $user = $rest->getParameter("db")->get($login);
        $json = Zend_Json::encode($user);
        $rest->getResponse()->setResponse($json);
        return $rest ;
    }

    public function delete(RestServer $rest) {
        $rest->getParameter("db")->deleteUser($rest->getRequest()->getUser());
        $rest->getResponse()->setResponse("DELETED");
        return $rest;
    }

    public function insert(RestServer $rest) {
        $rest->requireAuth(false);

        $login = $rest->getRequest()->getPOST("login");
        $login = strtolower($login);
        $password = $rest->getRequest()->getPOST("password");

        if(!preg_match("@^[\w]+$@",$login)){
            $rest->getResponse()->addHeader("HTTP/1.1 400 Bad Request");
            $rest->getResponse()->setResponse("Invalid Login, use letters only.");
            return $rest;
        }

        $user = $rest->getParameter("db")->get($login);
        if($user->password != null) {
            $rest->getResponse()->addHeader("HTTP/1.1 400 Bad Request");
            $rest->getResponse()->setResponse("Login already exists");
            return $rest;
        } else {
            $user = new StdClass ;
            $user->login = $login;
            $user->password = $password;
            $user->ideas = array();
            $users[] = $user;
            $rest->getParameter("db")->putUser($login,$user);
            $rest->getResponse()->addHeader("HTTP/1.1 201 Created");
            $rest->getResponse()->setResponse("HTTP/1.1 201 Created");
        }

        return $rest;
    }
}

?>
