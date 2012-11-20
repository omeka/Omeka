            </div>

            <footer class="login-footer">
                <?php fire_plugin_hook('admin_footer', array('view'=>$this)); ?>
                <p>
                    <a href="http://omeka.org" id="omeka-logo">
                        <?php echo __('Powered by Omeka'); ?>
                    </a> | <?php echo __('Version %s', OMEKA_VERSION); ?>
                </p>
            </footer>
        </div>
    </div>
</body>
</html>
