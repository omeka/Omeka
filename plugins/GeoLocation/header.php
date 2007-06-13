<?php $key = $this->getConfig('Google Maps API Key'); ?>
<?php if($key): ?>

<script src="http://maps.google.com/maps?file=api&amp;v=2.x&amp;key=<?php echo $key;?>" type="text/javascript"></script>
<script src="<?php echo $this->webPath() . DIRECTORY_SEPARATOR. 'map.js';?>" type="text/javascript" charset="utf-8"></script>

<?php endif; ?>