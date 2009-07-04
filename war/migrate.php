<?
// Don't use this!!!
include 'StorageWrapper.php';
include 'StorageWrapper.new.php';
include 'utils/zend_json/Json.php';

$new = new StorageWrapperNew ;
$new->deleteAll();
$old = new StorageWrapper ;

$logins = $old->getLogins();

foreach($logins as $login) {
    $ideasOld = $old->get($login);
    $new->put($login,$ideasOld);
    $ideasNew = $new->get($login);
}

?>
