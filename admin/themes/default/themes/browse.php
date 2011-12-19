<?php
$pageTitle = __('Browse Themes');
head(array('title'=>$pageTitle, 'content_class' => 'vertical-nav', 'bodyclass'=>'themes primary'));?>
<h1><?php echo $pageTitle; ?> <?php echo __('(%s total)', count($themes)); ?></h1>

<?php common('settings-nav'); ?>

<div id="primary">
    <?php echo flash(); ?>
    <h2 id="current-theme-title"><?php echo __('Current Theme'); ?>: &quot;<?php echo html_escape($current->title); ?>&quot; <?php if($current->hasConfig): ?><a href="<?php echo html_escape(uri('themes/config?name='.$current->directory)); ?>" class="configure-button button"><?php echo __('Configure'); ?></a><?php endif; ?></h2>            

<div id="current-theme">
    <img src="<?php echo html_escape($current->image); ?>" width="342px" alt="<?php echo __('Screenshot for %s Theme', html_escape($current->title)); ?>" />
    
    <ul>
        <li><span class="type"><?php echo __('Author'); ?>:</span> <span class="value"><?php echo html_escape($current->author); ?></span></li>
        <li><span class="type"><?php echo __('License'); ?>:</span> <span class="value"><?php echo html_escape($current->license); ?></span></li>
        <li><span class="type"><?php echo __('Website'); ?>:</span> <span class="value"><a href="<?php echo html_escape($current->website); ?>"><?php echo html_escape($current->website); ?></a></span></li>
        <li><span class="type"><?php echo __('Description'); ?>:</span> <span class="value"><?php echo html_escape($current->description); ?></span></li>
    </ul>
</div>

<h2><?php echo __('Change Theme'); ?></h2>
<form method="post" id="themeswitch" action="<?php echo $this->url(array('controller'=>'themes', 'action'=>'switch'), 'default'); ?>">
    <div class="themes group">
    <?php foreach($themes as $theme): ?>
    <div class="theme<?php if($current == $theme) echo ' current-theme';?>">
        <div class="meta">
        <label><input type="radio" name="public_theme" value="<?php echo html_escape($theme->directory); ?>" <?php if($current == $theme):  ?>checked="checked" <?php endif; ?>/>   
        <?php echo html_escape($theme->title); ?></label>
        <ul>
            <li><span class="type"><?php echo __('Author'); ?>:</span> <span class="value"><?php echo html_escape($theme->author); ?></span></li>
            <li><span class="type"><?php echo __('License'); ?>:</span> <span class="value"><?php echo html_escape($theme->license); ?></span></li>
            <li><span class="type"><?php echo __('Website'); ?>:</span> <span class="value"><a href="<?php echo html_escape($theme->website); ?>"><?php echo html_escape($theme->website); ?></a></span></li>
        </ul>
        </div>
        <div class="description">
        <img src="<?php echo html_escape($theme->image); ?>" width="296px" alt="<?php echo __('Screenshot for %s Theme', html_escape($current->title)); ?>" />
        </div>
    </div>
    <?php endforeach; ?>
    </div>
    <div>
        <input type="submit" name="submit" class="submit" id="submit" value="<?php echo __('Switch Theme'); ?>" />
    </div>
</form>

<p class="managethemes"><?php echo __('Add new themes by downloading them from the <a href="http://omeka.org/add-ons/themes/">Omeka Theme Directory</a>, or <a href="http://omeka.org/codex/Theme_Writing_Best_Practices">design your own</a>!'); ?></p>
</div>
<?php foot(); ?>
