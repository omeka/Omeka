<?php
/*
	Debug levels
*/
define ('KEA_DEBUG_ERRORS',		1);
define ('KEA_DEBUG_SQL',		false);
define ('KEA_DEBUG_TEMPLATE',	true);

switch (KEA_DEBUG_ERRORS) {
	case (1): error_reporting(E_ALL | E_STRICT);	break;
	case (2): error_reporting(E_ALL);				break;
	case (3): error_reporting(E_ALL  ^  E_NOTICE);	break;
	case (4): error_reporting(E_WARNING);			break;
	case (5): error_reporting(0);					break;
}
?>