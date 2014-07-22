<?php
echo head(array('title' => __('Appearance'), 'bodyclass' => 'themes'));
echo common('appearance-nav');
echo flash();

if ($current->image) {
    $currentScreenshot = html_escape($current->image);
} else {
    $currentScreenshot = img('fallback-theme.png');
}
?>

<div id="current-theme" class="theme">
    <div id="current-image" class="five columns alpha">
        <div class="crop"><img src="<?php echo $currentScreenshot; ?>" alt="<?php echo __('Screenshot for %s Theme', html_escape($current->title)); ?>" /></div>
    <?php if($current->hasConfig): ?><a href="<?php echo html_escape(url('themes/config?name=' . $current->directory)); ?>" id="configure-button" class="blue button"><?php echo __('Configure Theme'); ?></a><?php endif; ?>
    </div>
    <div id="current-info" class="five columns omega">
        <h2 id="current-theme-title"><?php echo __('Current Theme'); ?></h2>
        <h3><?php echo html_escape($current->title); ?></h3>
        <p class="author"><a href="<?php echo html_escape($current->website); ?>"><?php echo __('By %s', html_escape($current->author)); ?></a></p>
        <p class="theme-description"><?php echo html_escape($current->description); ?></p>
        <p class="theme-support-link"><a href="<?php echo $current->support_link; ?>" target="_blank"><?php echo __('Get support');?></a></p>
    </div>
</div>

<p class="managethemes"><?php echo __('Add new themes by downloading them from the <a href="http://omeka.org/add-ons/themes/" target="_blank">Omeka Theme Directory</a>, or <a href="http://omeka.org/codex/Theme_Writing_Best_Practices" target="_blank">design your own</a>!'); ?></p>
<div class="themes group">
    <form method="post" action="<?php echo $this->url(array('controller'=>'themes', 'action'=>'switch'), 'default'); ?>">
<?php 
$i = 0;
foreach($themes as $theme): 
    if ($current != $theme ):
    if ($theme->image) {
        $themeScreenshot = html_escape($theme->image);
    } else {
        $themeScreenshot = img('fallback-theme.png');
    }
?>
        <div class="theme<?php if($current == $theme) echo ' current-theme';?> three columns<?php if ($i++ % 3) echo ' alpha'; ?>">
            <div class="crop">
                <img src="<?php echo $themeScreenshot; ?>" alt="<?php echo __('Screenshot for %s Theme', html_escape($theme->title)); ?>" />
            </div>
            <button type="submit" name="public_theme" class="use-theme green button" value="<?php echo html_escape($theme->directory); ?>"><?php echo __('Use this theme'); ?></button>
            <div class="meta">
                <h3><?php echo html_escape($theme->title); ?></h3>
                <p class="author"><a href="<?php echo html_escape($theme->website); ?>" target="_blank"><?php echo __('By %s', html_escape($theme->author)); ?></a></p>
                <p class="theme-support-link"><a href="<?php echo $current->support_link; ?>" target="_blank"><?php echo __('Get support');?></a></p>
            </div>
            <?php fire_plugin_hook('admin_themes_browse_each', array('theme' => $theme, 'view' => $this)); ?>
        </div>
<?php
    endif;
endforeach;
echo $csrf;
?>
    </form>
</div>
<div style="clear:both"><?php fire_plugin_hook('admin_themes_browse', array('themes' => $themes, 'view' => $this)); ?></div>
<?php echo foot(); ?>
