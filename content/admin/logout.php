<?php
$__c->users()->logout();
header("Location: ".$_link->to('login'));
exit();
?>