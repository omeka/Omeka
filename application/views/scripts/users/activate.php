<?php head(array('title' => 'User Activation'), $header); ?>
<h1>User Activation</h1>

<?php echo flash(); ?>
<h2>Hello, <?php echo html_escape($user->first_name . ' ' . $user->last_name); ?>. Your username is: <?php echo html_escape($user->username); ?></h2>

<form method="post">
    <fieldset>
    <div class="field">
    <?php echo label('new_password1', 'Create a Password'); ?>
        <div class="inputs">
        <input type="password" name="new_password1" id="new_password1" class="textinput" />
        
        </div>
    </div>
    <div class="field">
        <label for="new_password2">Re-type the Password:</label>
        <div class="inputs">
            <input type="password" name="new_password2" id="new_password2" class="textinput" />
        </div>
    </div>
    </fieldset>
    <div>
    <input type="submit" class="submit" name="submit" value="Activate"/>
    </div>
</form>

<?php foot(array(), $footer); ?>
