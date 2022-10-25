(function($) {
    $(document).ready(function() {        
        const lgContainer = document.getElementById('itemfiles');

        const inlineGallery = lightGallery(lgContainer, {
            plugins: [lgThumbnail, lgVideo, lgZoom],
            exThumbImage: 'data-thumb',
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
