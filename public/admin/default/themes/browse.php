<?php
head(array('title'=>'foo'));
?>

<?php foreach($this->themes as $theme): ?>

<h2><?php echo $theme['ini']->title; ?></h2>

<p>Author: <?php echo $theme['ini']->author; ?></p>

<p>Description: <?php echo $theme['ini']->description; ?></p>

<p>License: <?php echo $theme['ini']->license; ?></p>

<p>Website: <a href="http://<?php echo $theme['ini']->website; ?>"><?php echo $theme['ini']->website; ?></a></p>

<img src="<?php echo $theme['image']; ?>"/>

<?php endforeach; ?>
<img src="<?php img('foo.jpg');?>"/>
<?php
footer();
?>