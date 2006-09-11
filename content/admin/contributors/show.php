<?php
//Layout: default;
$contributor = $__c->contributors()->findById();
?>
<?php include( 'subnav.php' ); ?>
<style type="text/css" media="screen">
/* <![CDATA[ */
	#contributor-wrapper{float:left; width:400px;}
	#contributor-contact{float:left; padding: 8px; background-color: #eee; width:200px;}
/* ]]> */
</style>

<br/>
<div id="contributor-wrapper">
	<div id="contributor-main">
		<h3><?php echo $contributor->getName(); ?>
		 <span class="showByContributor"></span><a href="<?php echo $_link->to( 'objects' ); ?>?contributor=<?php echo $contributor->contributor_id; ?>">show contributions</a></h3>
		<p>Birth Year: <?php echo $contributor->contributor_birth_year; ?></p>
		<p>Gender: <?php echo $contributor->contributor_gender; ?></p>
		<p>Race: <?php echo $contributor->contributor_race; ?></p>
		<p>Race (Other): <?php echo $contributor->contributor_race_other; ?></p>
	</div>

	<div id="contributor-misc">
	<p>Occupation: <?php echo $contributor->contributor_occupation; ?></p>
	<p>Location During: <?php echo $contributor->contributor_location_during; ?></p>
	<p>Location of Evacuation: <?php echo $contributor->contributor_location_after; ?></p>
	<p>Current Location: <?php echo $contributor->contributor_location_current; ?></p>
	</div>
</div>

<div id="contributor-contact">
<p>Contact Consent: <?php echo $contributor->contributor_contact_consent; ?></p>
<p>Email: <?php echo $contributor->contributor_email; ?></p>
<p>Phone: <?php echo $contributor->contributor_phone; ?></p>
<p>Fax: <?php echo $contributor->contributor_fax; ?></p>
<p>Address: <?php echo $contributor->contributor_address; ?></p>
<p>City: <?php echo $contributor->contributor_city; ?></p>
<p>State: <?php echo $contributor->contributor_state; ?></p>
<p>Zipcode: <?php echo $contributor->contributor_zipcode; ?></p>
</div>

<br class="clear"/>