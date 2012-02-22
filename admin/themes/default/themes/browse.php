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
              

			<div id="current-theme">
			    <div id="current-image" class="five columns">
			    <img src="<?php echo html_escape($current->image); ?>" alt="<?php echo __('Screenshot for %s Theme', html_escape($current->title)); ?>" />
			    <?php if($current->hasConfig): ?><a href="<?php echo html_escape(uri('themes/config?name='.$current->directory)); ?>" id="configure-button" class="blue button"><?php echo __('Configure Theme'); ?></a><?php endif; ?>
			    </div>
			    <div id="current-info">
					<h2 id="current-theme-title"><?php echo __('Current Theme'); ?></h2>
					<h2><?php echo html_escape($current->title); ?></h2>
					<p class="theme-description"><?php echo html_escape($current->description); ?></p>
				    <ul>
				        <li><span class="type"><?php echo __('Author'); ?>:</span> <span class="value"><?php echo html_escape($current->author); ?></span></li>
				        <li><span class="type"><?php echo __('Website'); ?>:</span> <span class="value"><a href="<?php echo html_escape($current->website); ?>"><?php echo html_escape($current->website); ?></a></span></li>
				    </ul>
			    </div>
			</div>

			<form method="post" id="themeswitch" action="<?php echo $this->url(array('controller'=>'themes', 'action'=>'switch'), 'default'); ?>">

<p class="managethemes"><?php echo __('Add new themes by downloading them from the <a href="http://omeka.org/add-ons/themes/">Omeka Theme Directory</a>, or <a href="http://omeka.org/codex/Theme_Writing_Best_Practices">design your own</a>!'); ?></p>			
			    <div class="themes group">
			    <?php 
			    $i = 0;
			    foreach($themes as $theme): 
			    	$i++;
			    	if (($i+2)%3 == 0) { ?>
			    <div class="theme<?php if($current == $theme) echo ' current-theme';?> three columns alpha <?php echo $i ?>">
			        <div class="crop">
			        	<img src="<?php echo html_escape($theme->image); ?>" alt="<?php echo __('Screenshot for %s Theme', html_escape($current->title)); ?>" />
			        </div>
			        <div class="meta">
			        <?php echo html_escape($theme->title); ?>
			        <ul>
			            <li><span class="type"><?php echo __('Author'); ?>:</span> <span class="value"><?php echo html_escape($theme->author); ?></span></li>
			            <li><span class="type"><?php echo __('Website'); ?>:</span> <span class="value"><a href="<?php echo html_escape($theme->website); ?>"><?php echo html_escape($theme->website); ?></a></span></li>
			        </ul>
			        </div>
			    </div>
			    <?php } else { ?>
			    <div class="theme<?php if($current == $theme) echo ' current-theme';?> three columns">
			        <div class="crop">
			        	<img src="<?php echo html_escape($theme->image); ?>" alt="<?php echo __('Screenshot for %s Theme', html_escape($current->title)); ?>" />
			        </div>
			        <div class="meta">
			        <?php echo html_escape($theme->title); ?>
			        <ul>
			            <li><span class="type"><?php echo __('Author'); ?>:</span> <span class="value"><?php echo html_escape($theme->author); ?></span></li>
			            <li><span class="type"><?php echo __('Website'); ?>:</span> <span class="value"><a href="<?php echo html_escape($theme->website); ?>"><?php echo html_escape($theme->website); ?></a></span></li>
			        </ul>
			        </div>
			    </div>
			    <?php } ?>			    
			    <?php endforeach; ?>
			    </div>
			</form>

</div>
<?php foot(); ?>
