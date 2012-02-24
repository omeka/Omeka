<?php
$pageTitle = __('Browse Themes');
head(array('title'=>$pageTitle, 'content_class' => 'vertical-nav', 'bodyclass'=>'themes primary'));?>
<h1 class="section-title"><?php echo $pageTitle; ?> <?php echo __('(%s total)', count($themes)); ?></h1>

	<section id="content" class="container">
	
		<div class="two columns">
			&nbsp;
		</div>
		
		<div class="ten columns">

   			<?php echo flash(); ?>
              

			<div id="current-theme" class="theme">
			    <div id="current-image" class="five columns alpha">
			    	<div class="crop"><img src="<?php echo html_escape($current->image); ?>" alt="<?php echo __('Screenshot for %s Theme', html_escape($current->title)); ?>" /></div>
			    <?php if($current->hasConfig): ?><a href="<?php echo html_escape(uri('themes/config?name='.$current->directory)); ?>" id="configure-button" class="blue button"><?php echo __('Configure Theme'); ?></a><?php endif; ?>
			    </div>
			    <div id="current-info" class="five columns omega">
					<h2 id="current-theme-title"><?php echo __('Current Theme'); ?></h2>
					<h3><a href="<?php echo html_escape($current->website); ?>"><?php echo html_escape($current->title); ?></a></h3>
					<p class="author"><?php echo __('By'); ?> <?php echo html_escape($current->author); ?></p>
					<p class="theme-description"><?php echo html_escape($current->description); ?></p>
			    </div>
			</div>

			

<p class="managethemes"><?php echo __('Add new themes by downloading them from the <a href="http://omeka.org/add-ons/themes/">Omeka Theme Directory</a>, or <a href="http://omeka.org/codex/Theme_Writing_Best_Practices">design your own</a>!'); ?></p>			
			    <div class="themes group">
			    <?php 
			    $i = 0;
			    foreach($themes as $theme): 
			    	if ($current != $theme ): ?>
				    	<form method="post" class="themeswitch" action="<?php echo $this->url(array('controller'=>'themes', 'action'=>'switch'), 'default'); ?>">
				    	<?php $i++;
				    	if (($i+2)%3 == 0) { ?>
						    <div class="theme<?php if($current == $theme) echo ' current-theme';?> three columns alpha">
				    <?php } else { ?>
					    <div class="theme<?php if($current == $theme) echo ' current-theme';?> three columns">
				    <?php } ?>			    
				        <input type="radio" name="public_theme" value="<?php echo html_escape($theme->directory); ?>" checked="checked" /> 
				        <div class="crop">
				        	<img src="<?php echo html_escape($theme->image); ?>" alt="<?php echo __('Screenshot for %s Theme', html_escape($current->title)); ?>" />
				        </div>
				        <input type="submit" name="submit" class="use-theme green button" value="<?php echo __('Use this theme'); ?>" />
				        <div class="meta">
					        <h3><a href="<?php echo html_escape($theme->website); ?>"><?php echo html_escape($theme->title); ?></a></h3>
					        <p class="author">By <?php echo html_escape($theme->author); ?></p>
				        </div>
				    </div>
				    </form>
			    	<?php endif; ?>
			    <?php endforeach; ?>
			    </div>
			</form>

</div>
<?php foot(); ?>
