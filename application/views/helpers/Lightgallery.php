<?php
/**
 * Return media viewer using lightgallery plugin.
 * 
 * @package Omeka\View\Helper
 */
class Omeka_View_Helper_Lightgallery extends Zend_View_Helper_Abstract
{
    public function lightGallery($files = null, $supported = true) {
        
        $sortedMedia = $this->_prepareLightgalleryFiles($files);
        $html = '';

        if ($supported) {

            $html .= '<div id="itemfiles">';
            $mediaCaption = get_theme_option('media_caption');

            foreach ($sortedMedia['lightMedia'] as $index) {
                $media = $index['media'];
                $source = $media->getProperty('uri');
                $mediaCaptionOptions = [
                    'none' => '',
                    'title' => 'data-sub-html="' . metadata($media, 'display_title') . '"',
                    'description' => 'data-sub-html="'. metadata($media, array('Dublin Core', 'Description')) . '"'
                ];
                $mediaCaptionAttribute = ($mediaCaption) ? $mediaCaptionOptions[$mediaCaption] : '';
                $mediaType = ($media->mime_type == 'video/quicktime') ? 'video/mp4' : $media->mime_type;
                if (strpos($mediaType, 'video') !== false) {
                    $videoSrcObject = [
                        'source' => [
                            [
                                'src' => $source, 
                                'type' => $mediaType,
                            ]
                        ], 
                        'attributes' => [
                            'preload' => false, 
                            'playsinline' => true, 
                            'controls' => true,
                        ],
                    ];
                    if (isset($index['tracks'])) {
                        foreach ($index['tracks'] as $key => $track) {
                            $label = metadata($track, 'display_title');
                                $srclang = (metadata($track, array('Dublin Core', 'Language'))) ? metadata($track, array('Dublin Core', 'Language'), array('no_escape' => true)) : '';
                                $type = (metadata($track, array('Dublin Core', 'Type'))) ? metadata($track, array('Dublin Core', 'Type'), array('no_escape' => true)) : 'captions';
                            $videoSrcObject['tracks'][$key]['src'] = $track->getWebPath();
                            $videoSrcObject['tracks'][$key]['label'] = $label;
                            $videoSrcObject['tracks'][$key]['srclang'] = $srclang;
                            $videoSrcObject['tracks'][$key]['kind'] = $type;
                        }
                    }
                    $videoSrcJson = json_encode($videoSrcObject);
                    $videoThumbnail = ($media->hasThumbnail()) ? metadata($media, 'thumbnail_uri') : img('fallback-video.png');
                    $html .= '<div data-video="' . html_escape($videoSrcJson) . '" ' . $mediaCaptionAttribute . 'data-thumb="' . html_escape($videoThumbnail) . '" data-download-url="' . $source . '" class="media resource">';
                    $html .= '<img src="' . $videoThumbnail . '" alt="" role="presentation">';
                } else if ($mediaType == 'application/pdf') {
                    $html .= '<div data-iframe="' . html_escape($source) . '" '. $mediaCaptionAttribute . 'data-src="' . $source . '" data-thumb="' . html_escape(metadata($media, 'thumbnail_uri')) . '" data-download-url="' . $source . '" class="media resource">';
                    $html .= file_markup($media);
                } else {
                    $html .=  '<div data-src="' . $source . '" ' . $mediaCaptionAttribute . 'data-thumb="' . html_escape(metadata($media, 'thumbnail_uri')) . '" data-download-url="' . $source . '" class="media resource">';
                    $html .= file_markup($media);
                }
                $html .= '</div>';
            }
            $html .= '</div>';
        } elseif (isset($sortedMedia['otherMedia'])) {
            $otherMedia = $sortedMedia['otherMedia'];
            $html .= '<div id="other-media" class="element">';
            $html .= '<h3>' . get_view()->translate('Other Media') . '</h3>';
            foreach($otherMedia as $media) {
                $html .= '<div class="element-text">' . link_to($media, null, metadata($media, 'display_title')) . '</div>';
            }
            $html .= '</div>';
        }

        return $html;
    }

    protected function _prepareLightgalleryFiles($files = null) {
        $sortedMedia = [];
        $whitelist = ['image/bmp', 'image/gif', 'image/jpeg', 'image/png', 'image/svg+xml', 'video/flv', 'video/x-flv', 'video/mp4', 'video/m4v',
                    'video/webm', 'video/wmv', 'video/quicktime', 'application/pdf'];
        $html5videos = [];
        $mediaCount = 0;

        foreach ($files as $media) {
            $mediaType = $media->mime_type;
            if (in_array($mediaType, $whitelist)) {
                $sortedMedia['lightMedia'][$mediaCount]['media'] = $media;
                if (strpos($mediaType,'video') !== false) {
                    $html5videos[$mediaCount] = pathinfo($media->filename, PATHINFO_FILENAME);
                    $sortedMedia['lightMedia'][$mediaCount]['tracks'] = [];
                }
                $mediaCount++;
            } else {
                $sortedMedia['otherMedia'][] = $media;
            }
        }
        if ((count($html5videos) > 0) && isset($sortedMedia['otherMedia'])) {
            foreach ($html5videos as $fileId => $filename) {
                foreach ($sortedMedia['otherMedia'] as $key => $otherMedia) {
                    if ($otherMedia->filename == "$filename.vtt") {
                        $sortedMedia['lightMedia'][$fileId]['tracks'][] = $otherMedia;
                        unset($sortedMedia['otherMedia'][$key]);
                    }
                }
            }   
        }

        return $sortedMedia;
    }
}
