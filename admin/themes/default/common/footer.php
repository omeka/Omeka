			<div class="clearfix"></div>
		</div>
    </div>

    <footer class="container" role="contentinfo">
        <div class="ten columns offset-by-two">
        	<?php fire_plugin_hook('admin_footer', array('view'=>$this)); ?>
			<p class="left">
				<a href="https://omeka.org" target="_blank"><?php echo __('Powered by Omeka') ?></a>
				<a href="https://omeka.org/classic/docs/" target="_blank"><?php echo __('Documentation'); ?></a>
				<a href="https://forum.omeka.org" target="_blank"><?php echo __('Support Forums'); ?></a>
				<a href="#content"><?php echo __('Back to top'); ?></a>
			</p>
        
	        <p class="right">
		        <?php echo __('Version %s', OMEKA_VERSION); ?>
				<?php if (get_option('display_system_info') && is_allowed('SystemInfo', 'index')): ?>
					<a href="<?php echo html_escape(url('system-info')); ?>"><?php echo __('System Information'); ?></a>
				<?php endif; ?>
			</p>
        </div>
    </footer>
</div>
<script type="text/javascript">
jQuery(document).ready(function () {
    Omeka.runReadyCallbacks();
});
</script>
</body>

</html>
