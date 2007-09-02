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
include ("../include.inc.php");
require ('check.php');
$_SESSION['last_activity']=time();
$SQL = new SQL();

//retrieve post data
$form = new Form('groups');
if ($form->exists()){
  $d['id'] = NULL;
  $d['name'] = $form->attrs['name'];
  $d['access'] = $form->attrs['access'];
  $d['flags'] = $form->attrs['flags'];
  $d['maxdepotitems'] = $form->attrs['depot_size'];
  $d['maxviplist'] = $form->attrs['vip_size'];
  if ($SQL->myInsert('groups',$d)){
    $msg = new IOBox('message');
		$msg->addMsg('Group created!');
		$msg->addClose('Finish');
		$msg->show();
	}else{
    $msg = new IOBox('message');
		$msg->addMsg('Cannot save group.');
		$msg->addClose('OK');
		$msg->show();
	}
}else{
$msg = new IOBox('groups');
$msg->target = $_SERVER['PHP_SELF'];
$msg->addLabel('Create Group');
$msg->addCode('<table id=flagtable cellspacing=0 cellpadding=1 width=400 border=1>
  <tr>
    <td>Privileges</td>

    <td>Limitations</td>
  </tr>
  <tr>
    <td valign="top" width="200">
		<input type="checkbox" name="groupflags" value="8"  id="CannotBeAttacked" onclick="calcFlags()"> Can not be attacked<br>
		<input type="checkbox" name="groupflags" value="16"  id="CanConvinceAll" onclick="calcFlags()"> Can convince all monsters<br>

		<input type="checkbox" name="groupflags" value="32"  id="CanSummonAll" onclick="calcFlags()"> Can summon all monsters<br>
		<input type="checkbox" name="groupflags" value="64"  id="CanIllusionAll" onclick="calcFlags()"> Can illusion all monsters<br>
		<input type="checkbox" name="groupflags" value="128"  id="CanSenseInvisibility" onclick="calcFlags()"> Can sense invisibility<br>
		<input type="checkbox" name="groupflags" value="256"  id="IgnoredByMonsters" onclick="calcFlags()"> Ignored by monsters<br>
		<input type="checkbox" name="groupflags" value="512"  id="NotGainInFight" onclick="calcFlags()"> Do not gain infight<br>

		<input type="checkbox" name="groupflags" value="1024"  id="HasInfiniteMana" onclick="calcFlags()"> Has unlimited mana<br>
		<input type="checkbox" name="groupflags" value="2048"  id="HasInfiniteSoul" onclick="calcFlags()"> Has unlimited soul<br>
		<input type="checkbox" name="groupflags" value="4096"  id="HasNoExhaustion" onclick="calcFlags()"> Do no gain exhaustion<br>
		<input type="checkbox" name="groupflags" value="32768"  id="CanAlwaysLogin" onclick="calcFlags()"> Can always login<br>
		<input type="checkbox" name="groupflags" value="65536"  id="CanBroadcast" onclick="calcFlags()"> Can broadcast<br>

		<input type="checkbox" name="groupflags" value="131072"  id="CanEditHouses" onclick="calcFlags()"> Can edit all house rights<br>
		<input type="checkbox" name="groupflags" value="262144"  id="CannotBeBanned" onclick="calcFlags()"> Can not be banned<br>
		<input type="checkbox" name="groupflags" value="524288"  id="CannotBePushed" onclick="calcFlags()"> Can not be pushed<br>
		<input type="checkbox" name="groupflags" value="1048576"  id="HasInfinateCapacity" onclick="calcFlags()"> Has unlimited capacity<br>
		<input type="checkbox" name="groupflags" value="2097152"  id="CanPushAllCreatures" onclick="calcFlags()"> Can push all creatures<br>

		<input type="checkbox" name="groupflags" value="4194304"  id="CanTalkRedPrivate" onclick="calcFlags()"> Talk red in private<br>
		<input type="checkbox" name="groupflags" value="8388608"  id="CanTalkRedChannel" onclick="calcFlags()"> Talk red in channel<br>
		<input type="checkbox" name="groupflags" value="16777216"  id="TalkOrangeHelpChannel" onclick="calcFlags()"> Talk orange in help-channel<br>
		<input type="checkbox" name="groupflags" value="17179869184"  id="IgnoreSpellCheck" onclick="calcFlags()"> Skip spell usage checks<br>
		<input type="checkbox" name="groupflags" value="34359738368"  id="IgnoreWeaponCheck" onclick="calcFlags()"> Skip weapon usage checks<br>

		<input type="checkbox" name="groupflags" value="536870912"  id="SetMaxSpeed" onclick="calcFlags()"> Gain max speed<br>
		<input type="checkbox" name="groupflags" value="1073741824"  id="SpecialVIP" onclick="calcFlags()"> Cannot be added to VIP<br>
		<input type="checkbox" name="groupflags" value="4294967296"  id="CanTalkRedChannelAnonymous" onclick="calcFlags()"> Talk red anonymously<br>
		<input type="checkbox" name="groupflags" value="8589934592"  id="IgnoreProtectionZone" onclick="calcFlags()"> Ignore protection-zone<br>
		<input type="checkbox" name="groupflags" value="68719476736"  id="CannotBeMuted" onclick="calcFlags()"> Can not be muted<br>

    </td>
    <td valign="top" width="200">
		<input type="checkbox" name="groupflags" value="1"  id="CannotUseCombat" onclick="calcFlags()"> Can not use combat<br>
		<input type="checkbox" name="groupflags" value="2"  id="CannotAttackPlayer" onclick="calcFlags()"> Can not attack players<br>
		<input type="checkbox" name="groupflags" value="4"  id="CannotAttackMonster" onclick="calcFlags()"> Can not attack monsters<br>
		<input type="checkbox" name="groupflags" value="8192"  id="CannotUseSpells" onclick="calcFlags()"> Cannot use spells<br>

		<input type="checkbox" name="groupflags" value="16384"  id="CannotPickupItem" onclick="calcFlags()"> Cannot pickup items<br>
		<input type="checkbox" name="groupflags" value="33554432"  id="NotGainExperience" onclick="calcFlags()"> Do not gain experience<br>
		<input type="checkbox" name="groupflags" value="67108864"  id="NotGainMana" onclick="calcFlags()"> Do not gain mana<br>
		<input type="checkbox" name="groupflags" value="134217728"  id="NotGainHealth" onclick="calcFlags()"> Do not gain health<br>
		<input type="checkbox" name="groupflags" value="268435456"  id="NotGainSkill" onclick="calcFlags()"> Do not gain skill<br>

		<input type="checkbox" name="groupflags" value="2147483648"  id="NotGenerateLoot" onclick="calcFlags()"> Can not gain loot<br>
    </td>
  </tr>
</table>');
$msg->addInput('name','text');
$msg->addInput('flags','text','0');
$msg->addInput('access','text','0');
$msg->addInput('depot size','text','1000');
$msg->addInput('vip size','text','100');
$msg->addClose('Cancel');
$msg->addSubmit('Save');
$msg->show();
}

?>