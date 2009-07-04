<?
// Don't use this!!!
import ideas.Storage ;

class StorageWrapper {
    private $db ;
    private $cache ;

    function __construct() {
        $this->db = new Storage ;
    }

    function getLogins(){
        return $this->db->getLogins();
    }

    function get($login){
        if(isset($this->cache[$login]) and $this->cache[$login] != null) {
            return $this->cache[$login] ;
        }
        $jsonPDO = $this->db->get($login);
        if($jsonPDO == null) {
            return null;
        } else {
            $jsonText = $jsonPDO->getNewJson() ;
            if($jsonText == null) {
                return null;
            }
            $json= $jsonText->getValue();
            $obj = Zend_JSon::Decode($json,Zend_Json::TYPE_OBJECT);
            $this->cache[$login] = $obj ;
            return $obj;
        }
    }

    function put($login,$obj) {
        $this->cache[$login] = $obj ;
        $json = Zend_JSon::Encode($obj);
        $this->db->put($login,$json);
    }

    function close() {
        $this->db->close();
    }
}
?>
