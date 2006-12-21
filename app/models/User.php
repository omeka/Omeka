<?php

class User extends Kea_Domain_Record { 
    public function setTableDefinition() {
		$this->setTableName('users');
		
        $this->hasColumn("name","string",30, "unique|notnull");
        $this->hasColumn("username","string",30);
        $this->hasColumn("password","string",40);
        $this->hasColumn("first_name","string",200);
        $this->hasColumn("last_name","string",200);
		$this->hasColumn("email", "string", 200);
        $this->hasColumn("institution","string",300);
        $this->hasColumn("active","boolean",1);

    }
}


?>