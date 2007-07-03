<?php 	
	//First DB migration
	
	//Check if the has_derivative_image bitflag is available
	//If not, we make it
		
	if(!$this->tableHasColumn('File', 'has_derivative_image')) {
		$f = $this->getTableName('File');
		$this->query("ALTER TABLE `$f` ADD `has_derivative_image` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT '0'");
		
		
		//Now fix all them table entries
		//Now we get all the files up to speed by checking each file and modding the value accordingly
		$files = $this->getTable('File')->findAll();
		foreach ($files as $k => $file) {
			$derivativePath = $file->getPath('thumbnail');
			if(file_exists($derivativePath)) {
				$file->has_derivative_image = 1;
				$file->save();
			}
		}
		
	}
?>
