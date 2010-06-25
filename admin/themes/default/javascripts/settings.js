function checkImageMagick(checkImageMagickUrl){
    if (!$('path_to_convert')) {
        return;
    }
    var testButton = new Element('button', {'type': 'button', 'id': 'test-button'});
    var resultDiv = new Element('div', {'id': 'im-result'});
    var imageMagickInput = $('path_to_convert');
    imageMagickInput.insert({'after':resultDiv});
    imageMagickInput.insert({'after': testButton});
    testButton.update('Test').observe('click', function(e){
        new Ajax.Request(checkImageMagickUrl, {
            method: 'get',
            parameters: 'path-to-convert=' + imageMagickInput.getValue(),
            onComplete: function(t) {
                resultDiv.update(t.responseText);
            }
        });
    });
}