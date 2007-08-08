<?
include ("../include.inc.php");
$_SESSION['last_activity']=time();

//retrieve post data
$form = new Form('comments');
//check if any data was submited
if ($form->exists()){
	$gid = (int) $_GET['gid'];
	//get guild owner acc
	$SQL = new MySQL();
	$query = 'SELECT players.account_id, guilds.name FROM players, guilds WHERE guilds.ownerid = players.id AND guilds.id = '.mysql_escape_string($gid);
	$result = $SQL->myQuery($query);
	$result = mysql_fetch_array($result);
	$owner = (int) $result['account_id'];
	//check if user is guild owner
	if ($owner == $_SESSION['account'] && !empty($_SESSION['account']))
		file_put_contents('../guilds/'.$gid.'.txt',htmlspecialchars($form->attrs['comment']));
}else{
	$gid = (int) $_POST['gid'];
	//create new form
	$form = new IOBox('comments');
	$form->target = $_SERVER['PHP_SELF'].'?gid='.$gid;
	$form->addLabel('Edit Description');
	$form->addTextbox('comment',@file_get_contents('../guilds/'.$gid.'.txt'));
	$form->addClose('Cancel');
	$form->addSubmit('Save');
	$form->show();
}
?>
