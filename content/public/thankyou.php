<?php
// Layout: thankyou;
?>

<?php if (@$_REQUEST['success']!='true'): ?>
<h2>Thank you!</h2>

<p>Thanks for submitting your story and/or file. Please ask your friends and family about our site, and encourage them to share their own experiences by sending them an email (or, just continue on to the <a href="<?php echo $_link->to(''); ?>">home page</a>):</p>

<form id="tellothers" method="POST" action="<?php echo $_link->to('contactmail'); ?>">
	<fieldset>
		<label for="mailFromName">Your Name</label>
		<input type="text" class="textinput" id="mailFromName" name="mailFromName" />
		<label for="mailFromEmail">Your Email</label>
		<input type="text" class="textinput" id="mailFromEmail" name="mailFromEmail" />
		<label for="mailToEmail">Who would you like to send the email to?</label>
		<p class="instructions">Separate email addresses with commas</p>
		<textarea id="mailToEmail" name="mailToEmail" rows="5" cols="30"></textarea>
		<label for="mailMessage">If you'd like to include a personal message, enter it below:</label>
		<textarea id="mailMessage" name="mailMessage" rows="10" cols="30"></textarea>

		<input type="hidden" name="">
		<input type="submit" name="sendEmail" value="Submit" class="submitinput" />
	</fieldset>
</form>
<?php else: ?>
<h2>Thank you!</h2>

<p>Thanks for spreading the word. <a href="<?php echo $_link->to('browse'); ?>">Click here</a> to return to the archive.</p>


<?php endif; ?>