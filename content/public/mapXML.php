<?php
	if (@$_REQUEST['id']):
		$object = $__c->objects()->findById(@$_REQUEST['id']);
		$map_objects['page'] = 1;
		$map_objects['per_page'] = 1; 
		$map_objects['total'] = 1;
		$map_objects['objects'][0] = $object;
	
	else:
		$map_objects = $__c->objects()->getMapObjects(50);
	endif;
	$featured = $__c->objects()->getRandomMapFeatured();
	$featured->getTags();
	$featured->getFilesWithThumbnails();

function windowsToAscii($text, $replace_single_quotes = true, $replace_double_quotes = true, $replace_emdash = true, $use_entities = false)
{
	$cout = '';
	
	
    $translation_table_ascii = array(
        145 => '\'', 
        146 => '\'', 
        147 => '"', 
        148 => '"', 
        151 => '-'
    );

    $translation_table_entities = array(
        145 => '&lsquo;', 
        146 => '&rsquo;', 
        147 => '&ldquo;', 
        148 => '&rdquo;', 
        151 => '&mdash;'
      );

    $translation_table = ($use_entities ? $translation_table_entities : $translation_table_ascii);

    if ($replace_single_quotes) {
        $text = preg_replace('#\x' . dechex(145) . '#', $translation_table[145], $text);
        $text = preg_replace('#\x' . dechex(146) . '#', $translation_table[146], $text);
    }

    if ($replace_double_quotes) {
        $text = preg_replace('#\x' . dechex(147) . '#', $translation_table[147], $text);
        $text = preg_replace('#\x' . dechex(148) . '#', $translation_table[148], $text);
    }

    if ($replace_emdash) {
        $text = preg_replace('#\x' . dechex(151) . '#', $translation_table[151], $text);
    }
    
	for($i=0;$i<strlen($text);$i++) {
	   $ord=ord($text[$i]);
	   if($ord>=192&&$ord<=239) $cout.=chr($ord-64);
	   elseif($ord>=240&&$ord<=255) $cout.=chr($ord-16);
	   elseif($ord==168) $cout.=chr(240);
	   elseif($ord==184) $cout.=chr(241);
	   elseif($ord==185) $cout.=chr(252);
	   elseif($ord==150||$ord==151) $cout.=chr(45);
	   elseif($ord==147||$ord==148||$ord==171||$ord==187) $cout.=chr(34);
	   elseif($ord>=128&&$ord<=190) $i=$i;
	   else $cout.=chr($ord);
	}
	
	$cout = str_replace("‘", "'", $cout);
	$cout = str_replace("’", "'", $cout);
	$cout = str_replace("”", '"', $cout);
	$cout = str_replace("“", '"', $cout);
	$cout = str_replace("–", "-", $cout);
	$cout = str_replace("…", "...", $cout);
	
	return htmlnumericentities($cout);
} 
	header('Content-type: text/xml');

function allhtmlentities($string) {
   if ( strlen($string) == 0 )
       return $string;
   $result = '';
   $string = htmlentities($string, HTML_ENTITIES);
   $string = preg_split("//", $string, -1, PREG_SPLIT_NO_EMPTY);
   $ord = 0;
   for ( $i = 0; $i < count($string); $i++ ) {
       $ord = ord($string[$i]);
       if ( $ord > 127 ) {
           $string[$i] = '&#' . $ord . ';';
       }
   }
   return implode('',$string);
}

function htmlnumericentities($str){
  return preg_replace('/[^!-%\x27-;=?-~ ]/e', '"&#".ord("$0").chr(59)', $str);
}

?>
<mapitems page="<?php echo $map_objects['page']; ?>" per_page="<?php echo $map_objects['per_page']; ?>" total="<?php echo $map_objects['total']; ?>">
	<?php foreach( $map_objects['objects'] as $object): $object->getTags(); $object->getFilesWithThumbnails(); ?>
	<item id="<?php echo $object->getId(); ?>" latitude="<?php echo $object->latitude; ?>" longitude="<?php echo $object->longitude; ?>">
		<short_desc><?php 

			if ($object->category_name=='Online Stories'){

				if ($object->getCategoryMetadata('Story Text')) echo windowsToAscii($object->getCategoryMetadata('Story Text', 150));
				elseif ($object->object_title) echo windowsToAscii($object->object_title);
				elseif ($object->object_description) echo windowsToAscii($object->object_description);
				else echo 'Contributed Story';
			}

			else if ($object->category_name=='Online Images'){
				if ($object->getCategoryMetadata('Image Description')) echo windowsToAscii($object->getCategoryMetadata('Image Description', 150));
				elseif ($object->object_description) echo windowsToAscii($object->object_description);
				elseif ($object->object_title) echo windowsToAscii($object->object_title);
			 	else echo 'Contributed Image';
			}

			elseif ($object->category_name=='Online Files'){
				if ($object->getCategoryMetadata('File Description')) echo windowsToAscii($object->getCategoryMetadata('File Description', 150));
				elseif ($object->object_title) echo windowsToAscii($object->object_title);
				elseif ($object->object_description) echo windowsToAscii($object->object_description);
				else echo 'Contributed File';
			}

			else if ($object->category_name=='Outside Links'){
					if ($object->getCategoryMetadata('Outside Link Content Provider Name')) echo allhtmlentities($object->getCategoryMetadata('Outside Link Content Provider Name', 100));
					elseif ($object->object_title) echo windowsToAscii($object->object_title);
					elseif ($object->object_description) echo windowsToAscii($object->object_description);
				 	else echo 'Contributed Link';
			}
			else {
				echo $object->category_id; 
			}
			
		?></short_desc>
		<tags>
			<?php foreach( $object->tags as $tag ):?>
			<tag tag_id="<?php echo $tag['tag_id']?>" tag_name="<?php echo $tag['tag_name']; ?>" tag_count="<?php echo $tag['tagCount']; ?>" />
			<?php endforeach; ?>
		</tags>
		<files>
			<?php if ($object->category_name=='Online Images'): foreach( $object->files as $file ): ?>
			<file file_title="<?php echo $file->file_title; ?>" file_archive_filename="<?php echo $file->file_archive_filename; ?>" file_thumbnail_name="<?php echo $file->file_thumbnail_name; ?>" />
			<?php endforeach; endif; ?>
		</files>
		<category_name><?php echo ($object->category_name); ?></category_name>
	</item>
	<?php endforeach; ?>
</mapitems>
