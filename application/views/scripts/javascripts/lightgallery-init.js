(function($) {
    $(document).ready(function() {        
        const lgContainer = document.getElementById('itemfiles');

        const inlineGallery = lightGallery(lgContainer, {
            selector: '.media.resource',
            plugins: [lgThumbnail, lgVideo, lgZoom],
            thumbnail: true,
            container: lgContainer,
            hash: false,
            closable: false,
            showMaximizeIcon: true,
            appendSubHtmlTo: '.lg-item',
            captions: true,
            slideDelay: 400,
            allowMediaOverlap: false
        });  

        inlineGallery.openGallery();
    });
  })(jQuery)