<?php
head(array(), 'login');

if (isset($errorMessage)) {
	echo '<h1>'.$errorMessage.'</h1>';
}
?>
<form action="<?php echo uri('users/login');?>" method="post" accept-charset="utf-8">
	Username: <input type="text" name="username"/><br/>
	Password: <input type="password" name="password"/><br/>
	<p><input type="submit" value="Continue &rarr;"></p>
</form>
<?php
foot();
?>