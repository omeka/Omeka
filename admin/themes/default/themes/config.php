<?php head(array('title'=>'Theme Configuration', 'bodyclass'=>'plugins')); ?>
<?php echo js('jQuery'); ?>
<script type="text/javascript">
    jQuery.noConflict();
    
    jQuery(document).ready(function() {
    
        var files = jQuery("input[type=file]");

        files.each( function(i, val) {
           fileInput = jQuery(val);
           fileInputName = fileInput.attr("name");
           
           hiddenFile = jQuery("#hidden_file_" + fileInputName);
           hiddenFileName = jQuery.trim(hiddenFile.attr("value"));
           if (hiddenFileName != "") {
                              
               var fileNameDiv = jQuery(document.createElement('div'));
               fileNameDiv.attr('id', 'x_hidden_file_' + fileInputName);
               fileNameDiv.text(hiddenFileName);
               
               var button = jQuery(document.createElement('a'));
               button.text('Change');
               button.attr('class', 'submit');
               
               button.click(function () {
                     hiddenFile = jQuery("#hidden_file_" + fileInputName);
                     hiddenFile.attr("value", "");                     
                     fileInput.show();
                     fileNameDiv.hide(); 
                     jQuery(this).hide();
               });
               
               fileNameDiv.append(button);
               
               fileInput.after(fileNameDiv);
               fileInput.hide();
           }
        });
    });
    
    

</script>

<div id="primary">
    <?php echo flash(); ?>
    <h2>Please Configure The &quot;<?php echo html_escape($theme->title); ?>&quot; Theme</h2>
        <?php echo $configForm; ?>
</div>

<?php foot(); ?>