<?php
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

class IOBox
{
private $dom;
public $target;

function __construct($name){
	$this->name = $name;
	if (isset($_POST['ajax']))
		$code = '<table cellspacing="10px" onmouseup="Cookies.create(\'iobox_x\',document.getElementById(\'iobox\').style.left,1);Cookies.create(\'iobox_y\',document.getElementById(\'iobox\').style.top,1);" style="visibility:hidden" id="iobox" class="draggable"><tr><td><fieldset><form id="'.$this->name.'" method="post"></form></fieldset></td></tr></table>';
	else
		$code = '<div id="iobox" class="iobox"><fieldset><form id="'.$this->name.'" method="post"></form></fieldset></div>';
	$dom = new DOMDocument();
	$this->dom = $dom->createDocumentFragment();
	$this->dom->loadHTML($code);
	echo htmlspecialchars($this->dom->saveHTML());
}
public function addMsg($msg){
	$this->dom->getElementById($this->name)->appendChild($this->dom->createElement('p',$msg));
}
public function addCaptcha(){
	global $cfg;
	if(!$cfg['use_captcha']) return;
	$_SESSION['RandomText'] = substr(str_shuffle(strtolower('qwertyuipasdfhjklzxcvnm12345789')), 0, 6);
	$image = $this->dom->getElementById($this->name)->appendChild($this->dom->createElement('img'));
	$image->setAttribute('width','250px');
	$image->setAttribute('height','40px');
	$image->setAttribute('alt','Verification Image');
	if (isset($_POST['ajax']))
		$image->setAttribute('src','doimg.php?'.time());
	else
		$image->setAttribute('src','../doimg.php?'.time());
	$input = $this->dom->getElementById($this->name)->appendChild($this->dom->createElement('input'));
	$input->setAttribute('id','captcha');
	$input->setAttribute('name',$this->name.'__captcha');
	$input->setAttribute('type','text');
	$input->setAttribute('maxlength','10');
	$input->setAttribute('style','text-transform: uppercase');
	$label = $this->dom->getElementById($this->name)->appendChild($this->dom->createElement('label'));
	$label->setAttribute('for', 'captcha');
}
public function addSelect($name, $options, $label = null){
	$select = $this->dom->getElementById($this->name)->appendChild($this->dom->createElement('select'));
	$select->setAttribute('name',$this->name.'__'.$name);
	foreach (array_keys($options) as $o){
		$option = $select->appendChild($this->dom->createElement('option', $options[$o]));
		$option->setAttribute('value',$o);
	}
	$label = $this->dom->getElementById($this->name)->appendChild($this->dom->createElement('label', isset($label) ? $label : ucfirst($name)));
	$label->setAttribute('for', $this->name.'__'.$name);
}
public function addInput($name, $type = 'text', $value = '', $length = 100, $label = null){
	$input = $this->dom->getElementById($this->name)->appendChild($this->dom->createElement('input'));
	$input->setAttribute('id',$this->name.'__'.$name);
	$input->setAttribute('name',$this->name.'__'.$name);
	$input->setAttribute('type',$type);
	$input->setAttribute('maxlength',$length);
	$input->setAttribute('value',$value);
	$label = $this->dom->getElementById($this->name)->appendChild($this->dom->createElement('label', isset($label) ? $label : ucfirst($name)));
	$label->setAttribute('for', $this->name.'__'.$name);
}
public function addCheckBox($name, $checked = false, $label = null){
	$input = $this->dom->getElementById($this->name)->appendChild($this->dom->createElement('input'));
	$input->setAttribute('id',$this->name.'__'.$name);
	$input->setAttribute('name',$this->name.'__'.$name);
	$input->setAttribute('type','checkbox');
	if ($checked) $input->setAttribute('checked','checked');
	$label = $this->dom->getElementById($this->name)->appendChild($this->dom->createElement('label', isset($label) ? $label : ucfirst($name)));
	$label->setAttribute('for', $this->name.'__'.$name);
}
public function addTextbox($name,$value = '',$cols = 40,$rows = 10){
	$textarea = $this->dom->getElementById($this->name)->appendChild($this->dom->createElement('textarea',$value));
	$textarea->setAttribute('id',$this->name.'__'.$name);
	$textarea->setAttribute('name',$this->name.'__'.$name);
	$textarea->setAttribute('cols',$cols);
	$textarea->setAttribute('rows',$rows);
}
public function addSubmit($text){
	$button = $this->dom->getElementById($this->name)->appendChild($this->dom->createElement('input'));
	$button->setAttribute('style', 'width: 100px; height: 25px;');
	$button->setAttribute('type', 'submit');
	$button->setAttribute('name', $this->name.'__'.$this->name);
	$button->setAttribute('id', $this->name.'__'.$this->name);
	$button->setAttribute('value', $text);
}
public function addReload($text){
	$button = $this->dom->getElementById($this->name)->appendChild($this->dom->createElement('input'));
	$button->setAttribute('style', 'width: 100px; height: 25px;');
	$button->setAttribute('type', 'button');
	$button->setAttribute('value', $text);
	$button->setAttribute('onclick', 'ajax(\'form\',\''.$_SERVER['PHP_SELF'].'\',\'\',true)');
}
public function addRefresh($text){
	$button = $this->dom->getElementById($this->name)->appendChild($this->dom->createElement('input'));
	$button->setAttribute('style', 'width: 100px; height: 25px;');
	$button->setAttribute('type', 'button');
	$button->setAttribute('value', $text);
	$button->setAttribute('onclick', 'location.reload(false)');
}
public function addClose($text){
	$button = $this->dom->getElementById($this->name)->appendChild($this->dom->createElement('input'));
	$button->setAttribute('style', 'width: 100px; height: 25px;');
	$button->setAttribute('type', 'button');
	$button->setAttribute('value', $text);
	$button->setAttribute('onclick', 'document.getElementById(\'iobox\').style[\'visibility\'] = \'hidden\'');
}
public function addCode($code){
	$doc = new DOMDocument();
	if ($doc->loadHTML($code)){
		$this->dom->getElementById($this->name)->appendChild($doc);
	}
}
public function addLabel($code){
	$this->dom->getElementsByTagName('fieldset')->item(0)->appendChild($this->dom->createElement('legend',$code));
}
public function getCode(){
	if (isset($_POST['ajax']))
		$this->dom->getElementById($this->name)->setAttribute('action', 'javascript:ajax(\'form\',\''.$this->target.'\',getParams(document.getElementById(\''.$this->name.'\')),true)');
	else
		$this->dom->getElementById($this->name)->setAttribute('action', $this->target);
	return $this->dom->saveHTML();
}
public function show(){
	echo $this->getCode();
}
}//endclass

class Form
{
public $attrs;
public function __construct($name){
		foreach( array_keys($_POST) as $key){
			if (eregi('^'.$name.'__',$key)){
				$p = explode('__', $key);
				$this->attrs[$p[1]] = trim($_POST[$key]);
			}
		}
}
public function getBool($attr){
	return $this->attrs[$attr] === 'on';
}
public function exists(){
	if (isset($this->attrs)) return true;
	else return false;
}
public function validated(){
	global $cfg;
	if (!$cfg['use_captcha']) return true;
	if (strtolower($this->attrs['captcha']) === $_SESSION['RandomText'] && !empty($_SESSION['RandomText'])){
		$_SESSION['RandomText'] = null;
		return true;
	}else return false;
}
}
