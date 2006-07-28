<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Contact JWA | Katrina's Jewish Voices</title>
<?php include ('inc/metalinks.php'); ?>

<script type="text/javascript" src="<?php echo $_link->in( 'functionAddEvent.js', 'j/ajaxcontact' ); ?>"></script>
<script type="text/javascript" src="<?php echo $_link->in( 'contact.js', 'j/ajaxcontact' ); ?>"></script>
<script type="text/javascript" src="<?php echo $_link->in( 'xmlHttp.js', 'j/ajaxcontact' ); ?>"></script>


</head>

<body id="about" class="contact">
<a class="hide" href="#content">Skip to Content</a>
<div id="wrap">
	<?php include("inc/header.php"); ?>
	<div id="content">
		<h2>Contact</h2>
		<?php include ("inc/secondarynav.php"); ?>
		
		<div id="primary">
		<h3>Contact <abbr title="Jewish Women&#8217;s Archive">JWA</abbr></h3>
		<p id="loadBar" style="display:none;">
			<strong>Sending Email. Hold on just a sec&#8230;</strong>
			<img src="img/loading.gif" alt="Loading..." title="Sending Email" />
		</p>
		<p id="emailSuccess" style="display:none;">
			<strong style="color:#38c;">Success! Your Email has been sent.</strong>
		</p>
			<form action="scripts/contact.php" method="post" id="cForm">
				<fieldset id="message">
					<label for="posText">Message:</label>
					<textarea cols="50" rows="5" name="posText" id="posText"></textarea>
				</fieldset>
				<fieldset id="contactinfo">
					<label for="posName">Name</label>
					<input class="textinput" type="text" size="25" name="posName" id="posName" />
					<label for="posEmail">Email</label>
					<input class="textinput" type="text" size="25" name="posEmail" id="posEmail" />
					<label for="posRegard">Subject</label>
					<input class="textinput" type="text" size="25" name="posRegard" id="posRegard" />
					<label for="selfCC" id="selfCClabel">
						<input type="checkbox" name="selfCC" id="selfCC" value="send" /> Send CC to self
					</label>
					<label>
						<input class="submit" type="submit" name="sendContactEmail" id="sendContactEmail" value=" Send Email " />
					</label>
				</fieldset>
			</form>
		</div>
		<div id="secondary">
				<div id="jwa-contact">
								<p>Mail or Phone:</p>

								<address class="vcard">
								<span class="fn org" id="jwa-org">Jewish Women&#8217;s Archive</span>
								<span class="adr" id="jwa-address">
									<span class="street-address">138 Harvard Street</span>
									<span class="locality">Brookline</span>, <abbr class="region" title="Massachusettes">MA</abbr> <span class="postal-code">02446</span>

								</span>
								<span class="tel" id="jwa-tel"><span class="value">617.232.2258</span></span>
								<span><a href="http://www.jwa.org" class="url" id="jwa-url">www.jwa.org</a></span>
								</address>
							</div>
			</div>
		</div>
	</div>
	
<?php include("inc/footer.php"); ?>
</div>
</body>
</html>