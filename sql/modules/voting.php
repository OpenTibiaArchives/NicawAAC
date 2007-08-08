<?
include ("../include.inc.php");

$account = new Account($_SESSION['account']);
($account->load()) or die('You need to login first. '.$account->getError());
$_SESSION['last_activity']=time();

if (isset($_POST['polls_submit'])){

if ($account->canVote($_POST['poll_id'])){
if (!$account->doVote($_POST['poll_id'],(int) $_POST['voting'])) $error = 'Voting failed';
else setcookie('poll'.$_POST['poll_id'],'true',time()+2592000);
}else{$error = 'You cannot vote in this poll';}
$_SESSION['_error'] = $error;
header('location: '.$_SERVER['HTTP_REFERER']);
}else{
	$polls = new Polls();
	if (empty($_POST['poll']) || ($_POST['poll'] < 0)) $_POST['poll'] = 0;
	$poll = $polls->getPoll($_POST['poll']);
	if ($poll === false) die ('Poll not found');
	$canVote = $account->canVote($poll['id']);
?>
<input <?if ($_POST['poll']-1 < 0) echo 'disabled = "disabled"';?> type="button" onclick="ajax('ajax','<?=$_SERVER['PHP_SELF']?>','redirect=<?=$_POST['redirect']?>&poll=<?=($_POST['poll']-1)?>',true)" value="&lt;&lt;"/>
<b>Poll No: <?=$poll['id']?></b>
<input <?if ($_POST['poll']+1 > $polls->getMax()) echo 'disabled = "disabled"';?> type="button" onclick="ajax('ajax','<?=$_SERVER['PHP_SELF']?>','redirect=<?=$_POST['redirect']?>&poll=<?=($_POST['poll']+1)?>',true)" value="&gt;&gt;"/>
Level <?=$poll['minlevel']?> is required to vote in this poll.
<h3><?=$poll['question']?></h3>
<form action="<?=$_POST['redirect']?>" method="post">
<table>
<?
	if ($canVote) echo '<input type="hidden" name="poll_id" value="'.$poll['id'].'">';
	for ($i=0; $i < count($poll['options']); $i++){
		echo '<tr><td>';
		if ($canVote) echo '<input type="radio" name="voting" value="'.$i.'"></td><td>';
		echo '<b>'.$poll['options'][$i].'</b></td><td><i>'.$poll['results'][$i].'</i></td></tr>'."\n";
	}
?>
</table><br/>
<?if ($canVote) echo '<input type="submit" name="polls_submit" value="Vote"/>'."\n";?>
</form>
<?}?>