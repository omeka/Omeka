        </div>
        
    </section>

    <footer class="container">
    
        <div class="ten columns offset-by-two">
        <?php admin_plugin_footer(); ?>
        <p class="left"><a href="http://www.omeka.org" target="_blank"><?php echo __('Powered by Omeka') ?></a> | <a href="http://omeka.org/codex/" target="_blank"><?php echo __('Documentation'); ?></a> | <a href="http://omeka.org/forums/" target="_blank"><?php echo __('Support Forums'); ?></a></p>
        
        <p class="right"><?php echo __('Version %s', OMEKA_VERSION); ?>
        <?php if (get_option('display_system_info') && has_permission('SystemInfo', 'index')): ?>
        | <a href="<?php echo html_escape(uri('system-info')); ?>"><?php echo __('More information about your system'); ?></a></p>
        <?php endif; ?>
        </div>
    </footer>

</section>

<script type="text/javascript">
  // generate mobile version for each table
  jQuery('table').each(function() {
    var table = jQuery(this); // cache table object
    var head = table.find('thead th');
    var rows = table.find('tbody tr').clone(); // appending afterwards does not break original table

    // create new table
    var newtable = jQuery(
      '<table class="mobile">' +
      '  <tbody>' +
      '  </tbody>' +
      '</table>'
    );

    // cache tbody where we'll be adding data
    var newtable_tbody = newtable.find('tbody');

    rows.each(function(i) {
      var cols = jQuery(this).find('td');
      var classname = i % 2 ? 'even' : 'odd';
      cols.each(function(k) {
        var new_tr = jQuery('<tr class="' + classname + '"></tr>').appendTo(newtable_tbody);
        new_tr.append(head.clone().get(k));
        new_tr.append(jQuery(this));
      });
    });

    jQuery(this).after(newtable);
  });
</script>

</body>

</html>
