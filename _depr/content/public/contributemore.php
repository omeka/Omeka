<h2>Thanks for your Contribution!</h2>
<p>Would you like to add another file?</p>
<ul id="choices">
<li><a href="<?php echo $_link->to('contribute').'?contributor_id='.self::$_session->getValue( 'contributed_object' )->contributor_id; ?>" rel="deactivate">Yes, please!</a></li>
<li><a href="#" class="lbAction" rel="deactivate">No thanks</a></li>
</ul>