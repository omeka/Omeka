<?php 
require_once HELPERS;
class FormFunctionsTestCase extends OmekaTestCase
{

	
	public function testLabel()
	{
		$text = "Label text";
		$id = 'foobar';
		$label = label($id,$text,true);
		$this->assertEqual($label,'<label for="foobar">Label text</label>');
		
		$label = label(array('name'=>'foobar','id'=>'foobar','class'=>'label'),$text,true);
		$this->assertEqual($label,'<label name="foobar" id="foobar" class="label">Label text</label>');
	}
	
	public function testSelect()
	{
		$collections = $this->getTable('Collection')->findAll();
		$this->assertEqual(2,$collections->count());
		
		$default = 1;
		$label = 'Foobar';
		$optionValue = 'id';
		$optionDesc = 'name';
		
		ob_start();
		
		select(array(),$collections,$default,$label,$optionValue,$optionDesc);
		
		$select = ob_get_contents();
		$select = str_replace(array("\t","\n"),'',$select);
		$this->assertEqual($select,'<label>Foobar<select><option value="">Select Below&nbsp;</option><option value="1" selected="selected">Collection1</option><option value="2">Collection2</option></select></label>');
		
		ob_clean();
		
		select(array('id'=>'foobar','name'=>'foobar','class'=>'select'),$collections,$default,$label,$optionValue,$optionDesc);
		
		$select = ob_get_contents();
		$select = str_replace(array("\t","\n"),'',$select);
		$this->assertEqual($select,'<label for="foobar">Foobar</label><select id="foobar" name="foobar" class="select"><option value="">Select Below&nbsp;</option><option value="1" selected="selected">Collection1</option><option value="2">Collection2</option></select>');
		
		ob_clean();
		
		$coll_array = array(1=>'Collection1',2=>'Collection2');
		select(array('id'=>'foobar','name'=>'foobar','class'=>'select'),$coll_array,$default,$label);
		$select = ob_get_contents();
		$select = str_replace(array("\t","\n"),'',$select);
		
		$this->assertEqual($select,'<label for="foobar">Foobar</label><select id="foobar" name="foobar" class="select"><option value="">Select Below&nbsp;</option><option value="1" selected="selected">Collection1</option><option value="2">Collection2</option></select>');
		
		ob_clean();
		
		$test_array = array('default'=>'default');
		select(array('name'=>'foobar'),$test_array,'default');
		
		$select = ob_get_clean();
		$select = $this->stripSpace($select);
		
		$this->assertEqual($select, '<select name="foobar"><option value="">Select Below&nbsp;</option><option value="default" selected="selected">default</option></select>');
	}
	
	public function testText()
	{
		$default = "Foobar";
		$label = "Label text";
		$attr = array('name'=>'foobar','id'=>'foobar');
		ob_start();
		
		text($attr,$default,$label);
		
		$text = ob_get_contents();
		$text = str_replace(array("\t","\n"),'',$text);
		$this->assertEqual($text, '<label for="foobar">Label text</label><input type="text" name="foobar" id="foobar" value="Foobar" />');
		ob_clean();
		
		text(array(),$default);
		
		$text = ob_get_contents();
		$text = str_replace(array("\t","\n"),'',$text);
		$this->assertEqual($text, '<input type="text" value="Foobar" />');
		ob_clean();
		
		text($attr,$default);
		
		$text = ob_get_contents();
		$text = str_replace(array("\t","\n"),'',$text);
		$this->assertEqual($text, '<input type="text" name="foobar" id="foobar" value="Foobar" />');	
		ob_clean();
		
		textarea($attr,$default,$label);
		$text = ob_get_contents();
		$text = $this->stripSpace($text);

		$this->assertEqual($text, '<label for="foobar">Label text</label><textarea name="foobar" id="foobar">Foobar</textarea>');
		Zend_Debug::dump( $text );
		ob_clean();
		
		textarea($attr);
		$text = ob_get_contents();
		$text = $this->stripSpace($text);
		$this->assertEqual($text,'<textarea name="foobar" id="foobar"></textarea>');
		ob_clean();
	}
	
	public function testRadio()
	{
		$default = 2;
		$values = array(2=>'Yes',1=>'No',0=>'Unknown');
		$attr = array('name'=>'foobar','id'=>'foobar');
		
		ob_start();
		
		radio($attr,$values,$default);
		
		$radio = ob_get_contents();
		$radio = $this->stripSpace($radio);
		$this->assertEqual($radio,'<label class="radiolabel"><input type="radio" name="foobar" id="foobar" value="2" checked="checked" />Yes</label><label class="radiolabel"><input type="radio" name="foobar" id="foobar" value="1" />No</label><label class="radiolabel"><input type="radio" name="foobar" id="foobar" value="0" />Unknown</label>');
		
		ob_clean();
	}
	
	public function testCheckbox()
	{
		$attr = array('name'=>'foobar','id'=>'foobar');
		$value = 'foobar';
		$checked = true;
		$label = 'Foobar';
		
		ob_start();
		
		checkbox($attr,true,$value,$label);
		
		$checkbox = ob_get_contents();
		$checkbox = $this->stripSpace($checkbox);
		$this->assertEqual($checkbox,'<label for="foobar">Foobar</label><input type="checkbox" name="foobar" id="foobar" checked="checked" value="foobar" />');
		ob_clean();
		
		checkbox($attr,false,$value);
		
		$checkbox = ob_get_contents();
		$checkbox = $this->stripSpace($checkbox);
		$this->assertEqual($checkbox,'<input type="checkbox" name="foobar" id="foobar" value="foobar" />');
		ob_clean();
		
		hidden($attr,$value);
		$hidden = ob_get_contents();
		$hidden = $this->stripSpace($hidden);
		$this->assertEqual($hidden,'<input type="hidden" name="foobar" id="foobar" value="foobar" />');
		ob_clean();
	}
	
	public function testMetatextForm()
	{
		
	}
}