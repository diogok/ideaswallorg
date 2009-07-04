<?

include 'utils/zend_json/Json.php';
include 'StorageWrapper.new.php';
$db = new StorageWrapperNew() ;

if(!isset($_POST["login"]) || !preg_match("@^[\w]+$@",$_POST["login"]) ) {
    echo "<script>alert('Invalid Login, use letters only.');location.href='static/register.html'</script>";
    exit;
}

$user = $db->get($_POST["login"]);
if($user != null && $user->login === $_POST["login"]) {
    echo "<script>alert('Invalid Login, user already exists.');location.href='static/register.html'</script>";
    exit;
}

$user = new StdClass ;
$user->login = $_POST["login"];
$user->password = $_POST["password"];
$user->ideas = array();

$db->put($user->login,$user);

echo "<script>alert('Congratulations, your account is ready.');location.href='static/index.html'</script>";
?>
