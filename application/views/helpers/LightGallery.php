<?php
/**
 * Return media viewer using lightGallery plugin.
 *
 * @package Omeka\View\Helper
 */
class Omeka_View_Helper_LightGallery extends Zend_View_Helper_Abstract
{
    /**
     * Render a gallery of files using lightGallery
     *
     * @param array $files The files to render
     * @param bool $supported If true, render the gallery (default); if false,
     *  render a list of files not supported by the gallery
     * @return string
     */
    public function lightGallery($files, $supported = true)
    {
        $sortedFiles = $this->_prepareFiles($files);
        $html = '';

        if ($supported) {
            $html .= '<div id="itemfiles">';
            $mediaCaption = get_theme_option('lightgallery_caption');

            foreach ($sortedFiles['gallery'] as $galleryEntry) {
                $file = $galleryEntry['file'];
                $source = $file->getWebPath();
                $mediaCaptionOptions = [
                    'none' => '',
                    'title' => 'data-sub-html="' . metadata($file, 'display_title') . '"',
                    'description' => 'data-sub-html="'. metadata($file, array('Dublin Core', 'Description')) . '"'
                ];
                $mediaCaptionAttribute = ($mediaCaption) ? $mediaCaptionOptions[$mediaCaption] : '';

                $mediaType = ($file->mime_type == 'video/quicktime') ? 'video/mp4' : $file->mime_type;
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
                    foreach ($galleryEntry['tracks'] as $track) {
                        $label = metadata($track, 'display_title');
                        $srclang = metadata($track, array('Dublin Core', 'Language'), array('no_escape' => true));
                        $type = metadata($track, array('Dublin Core', 'Type'), array('no_escape' => true));
                        $videoSrcObject['tracks'][] = [
                            'src' => $track->getWebPath(),
                            'label' => $label,
                            'srclang' => $srclang !== null ? $srclang : '',
                            'kind' => $type !== null ? $type : 'captions',
                        ];
                    }
                    $videoSrcJson = json_encode($videoSrcObject);
                    $videoThumbnail = ($file->hasThumbnail()) ? metadata($file, 'thumbnail_uri') : img('fallback-video.png');

                    $html .= '<div data-video="' . html_escape($videoSrcJson) . '" ' . $mediaCaptionAttribute . 'data-thumb="' . html_escape($videoThumbnail) . '" data-download-url="' . $source . '">';
                } else if ($mediaType == 'application/pdf') {
                    $html .= '<div data-iframe="' . html_escape($source) . '" '. $mediaCaptionAttribute . 'data-src="' . $source . '" data-thumb="' . html_escape(metadata($file, 'thumbnail_uri')) . '" data-download-url="' . $source . '">';
                    $html .= file_markup($file);
                } else {
                    $html .= '<div data-src="' . $source . '" ' . $mediaCaptionAttribute . 'data-thumb="' . html_escape(metadata($file, 'thumbnail_uri')) . '" data-download-url="' . $source . '">';
                    $html .= file_markup($file);
                }
                $html .= '</div>';
            }
            $html .= '</div>';
        } elseif ($sortedFiles['other']) {
            $html .= '<div id="other-files" class="element">';
            $html .= '<h3>' . $this->view->translate('Other Files') . '</h3>';
            $html .= file_markup($sortedFiles['other'], array(), array('class' => 'element-text'));
            $html .= '</div>';
        }

        return $html;
    }

    protected function _prepareFiles($files)
    {
        $sortedFiles = ['gallery' => [], 'other' => []];
        $whitelist = ['image/bmp', 'image/gif', 'image/jpeg', 'image/png', 'image/svg+xml', 'video/mp4', 'video/x-m4v', 'video/ogg', 'video/webm', 'video/quicktime', 'application/pdf'];
        $html5videos = [];

        $index = 0;
        foreach ($files as $file) {
            $mediaType = $file->mime_type;
            if (in_array($mediaType, $whitelist)) {
                $sortedFiles['gallery'][$index]['file'] = $file;
                if (strpos($mediaType,'video') !== false) {
                    $html5videos[$index] = pathinfo($file->original_filename, PATHINFO_FILENAME);
                    $sortedFiles['gallery'][$index]['tracks'] = [];
                }
                $index++;
            } else {
                $sortedFiles['other'][] = $file;
            }
        }
        if ($html5videos && $sortedFiles['other']) {
            foreach ($html5videos as $fileIndex => $filename) {
                foreach ($sortedFiles['other'] as $key => $file) {
                    if ($file->original_filename == "$filename.vtt") {
                        $sortedFiles['gallery'][$fileIndex]['tracks'][] = $file;
                        unset($sortedFiles['other'][$key]);
                    }
                }
            }
        }

        return $sortedFiles;
    }
}
