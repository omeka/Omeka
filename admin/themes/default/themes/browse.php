<?php head(array('title'=>'Browse Themes', 'content_class' => 'vertical-nav', 'bodyclass'=>'themes primary'));?>
<h1>Browse Themes (<?php echo count($themes); ?> total)</h1>

<?php common('settings-nav'); ?>

<div id="primary">
    <?php echo flash(); ?>
    <h2>Current Theme: &quot;<?php echo html_escape($current->title); ?>&quot;</h2>

<div id="current-theme">
    <img src="<?php echo html_escape($current->image); ?>" width="342px" />
    
    <ul>
        <li><span class="type">Author:</span> <span class="value"><?php echo html_escape($current->author); ?></span></li>
        <li><span class="type">License:</span> <span class="value"><?php echo html_escape($current->license); ?></span></li>
        <li><span class="type">Website:</span> <span class="value"><a href="http://<?php echo html_escape($current->website); ?>"><?php echo html_escape($current->website); ?></a></span></li>
        <li><span class="type">Description:</span> <span class="value"><?php echo html_escape($current->description); ?></span></li>
        
    </ul>
</div>

<h2>Change Theme</h2>
<form method="post" id="themeswitch">
    <div class="themes">
    <?php foreach($themes as $theme): ?>
    <div class="theme<?php if($current == $theme) echo ' current-theme';?>">
        <div class="meta">
        <label><input type="radio" name="public_theme" value="<?php echo html_escape($theme->directory); ?>" <?php if($current == $theme):  ?>checked="checked" <?php endif; ?>/>   
        <?php echo html_escape($theme->title); ?></label>
        <ul>
            <li><span class="type">Author:</span> <span class="value"><?php echo html_escape($theme->author); ?></span></li>
            <li><span class="type">License:</span> <span class="value"><?php echo html_escape($theme->license); ?></span></li>
            <li><span class="type">Website:</span> <span class="value"><a href="http://<?php echo html_escape($theme->website); ?>"><?php echo html_escape($theme->website); ?></a></span></li>
        </ul>
        </div>
        <div class="description">
        <img src="<?php echo html_escape($theme->image); ?>" width="296px"/>
        </div>
    </div>
    <?php endforeach; ?>
    </div>
    <input type="submit" name="submit" class="submit submit-medium" id="submit" value="Switch Theme" />
</form>

<p class="managethemes">Add new themes by downloading them from the <a href="http://omeka.org/add-ons/themes/">Omeka Theme Directory</a>, or <a href="http://omeka.org/codex/Theme_Writing_Best_Practices">design your own</a>!</p>
</div>
<?php foot(); ?>