<?php

class Kea_Token_Factory
{
	public static function getToken($type)
	{
		switch ($type) {
			case('action'):
				require_once 'Kea/Action/Token.php';
				return new Kea_Action_Token;
			break;
			case('template'):
				require_once 'Kea/Template/Token.php';
				return new Kea_Template_Token;
			break;
		}
	}
}

?>