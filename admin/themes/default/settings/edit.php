<?php head(array('title'=>'Edit General Settings', 'content_class' => 'vertical-nav', 'bodyclass'=>'settings primary')); ?>
<script type="text/javascript" charset="utf-8">
//<![CDATA[
    Event.observe(window, 'load', function(){
        var testButton = new Element('button', {'type': 'button', 'id': 'test-button'});
        var loaderGif = new Element('img', {'src': <?php echo js_escape(img("loader.gif")); ?>});
        var resultDiv = new Element('div', {'id': 'im-result'});
        var imageMagickInput = $('path_to_convert');
        imageMagickInput.insert({'after':resultDiv});
        imageMagickInput.insert({'after': testButton});
        testButton.update('Test').observe('click', function(e){
            testButton.insert({'after': loaderGif});
            new Ajax.Request(<?php echo js_escape(uri(array("controller"=>"settings","action"=>"check-imagemagick"))); ?>, {
                method: 'get',
                parameters: 'path-to-convert=' + imageMagickInput.getValue(),
                onComplete: function(t) {
                    loaderGif.hide();
                    resultDiv.update(t.responseText);
                }
            });
        });
    });
//]]>    
</script>

<h1>Edit General Settings</h1>

<?php common('settings-nav'); ?>

<div id="primary">
<?php echo flash(); ?>
<?php echo $this->form; ?>
</div>
<?php foot(); ?>