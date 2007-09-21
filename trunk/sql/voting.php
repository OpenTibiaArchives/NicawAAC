<?
/*
    Copyright (C) 2007  Nicaw

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License along
    with this program; if not, write to the Free Software Foundation, Inc.,
    51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
*/
include ("include.inc.php");
$ptitle= "Polls - $cfg[server_name]";
include ("header.inc.php");
?>
<div id="content">
<div class="top">Polls</div>
<div class="mid">
<?
//receives all polls, options and vote count =)
$query = 'SELECT a1.poll_id, a1.option_id, a1.question, a1.option, COUNT( nicaw_poll_votes.option_id ) AS votes
FROM (
SELECT nicaw_poll_questions.id AS poll_id, nicaw_poll_options.id AS option_id, nicaw_poll_questions.startdate, nicaw_poll_questions.question, nicaw_poll_options.option
FROM nicaw_poll_questions, nicaw_poll_options
WHERE nicaw_poll_options.poll_id = nicaw_poll_questions.id
AND nicaw_poll_questions.startdate < '.time().'
AND nicaw_poll_questions.enddate > '.time().'
ORDER BY nicaw_poll_questions.startdate DESC
) AS a1
LEFT OUTER JOIN nicaw_poll_votes ON a1.option_id = nicaw_poll_votes.option_id
GROUP BY nicaw_poll_votes.option_id';
$sql = new SQL();
$sql->myQuery($query);
//sort the data by poll_id
while ($a = $sql->fetch_array()){
	$polls[$a['poll_id']]['question'] = $a['question'];
	$polls[$a['poll_id']]['votes_total'] += $a['votes'];
	$polls[$a['poll_id']]['options'][$a['option_id']]['id'] = $a['option_id'];
	$polls[$a['poll_id']]['options'][$a['option_id']]['option'] = $a['option'];
	$polls[$a['poll_id']]['options'][$a['option_id']]['votes'] = $a['votes'];
}
print_r($polls);
foreach ($polls as $poll){
	echo '<h2>'.$poll['question'].'</h2><table style="width: 100%">';
	foreach ($poll['options'] as $option){
		$percent = round(($option['votes']/$poll['votes_total'])*100);
		if ($percent <= 0) $width = 1;
		else $width = $percent;
		echo '<tr><td style="width: 50%">'.$option['option'].'</td><td style="width: 50%"><div style="width: '.$width.'px; height: 20px; background-color:red;"></div></td></tr>';
	}
	echo '</table>';
}
?>
</div>
<div class="bot"></div>
</div>
<?include ("footer.inc.php");?>