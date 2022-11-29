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
    public function lightGallery($files)
    {
        $sortedFiles = $this->_prepareFiles($files);
        $html = '';
        $html .= '<div id="itemfiles" class="lightgallery">';
        $captionOption = get_theme_option('lightgallery_caption');

        foreach ($sortedFiles['gallery'] as $galleryEntry) {
            $file = $galleryEntry['file'];
            $source = $file->getWebPath();
            switch ($captionOption) {
                case 'title':
                    $caption = metadata($file, 'rich_title', ['no_escape' => true]);
                    break;
                case 'description':
                    $caption = metadata($file, ['Dublin Core', 'Description'], ['no_escape' => true]);
                    break;
                case 'none':
                default:
                    $caption = '';
            }

            $attributes = [
                'data-thumb' => record_image_url($file, 'thumbnail'),
                'data-download-url' => $source,
            ];
            if (strlen((string) $caption)) {
                $attributes['data-sub-html'] = $caption;
            };

            $mediaType = ($file->mime_type == 'video/quicktime') ? 'video/mp4' : $file->mime_type;
            if (strpos($mediaType, 'video') !== false || (strpos($mediaType, 'audio') !== false)) {
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
                if (isset($galleryEntry['tracks'])) {
                    foreach ($galleryEntry['tracks'] as $track) {
                        $label = metadata($track, 'display_title');
                        $srclang = metadata($track, ['Dublin Core', 'Language'], ['no_escape' => true]);
                        $type = metadata($track, ['Dublin Core', 'Type'], ['no_escape' => true]);
                        $videoSrcObject['tracks'][] = [
                            'src' => $track->getWebPath(),
                            'label' => $label,
                            'srclang' => $srclang !== null ? $srclang : '',
                            'kind' => $type !== null ? $type : 'captions',
                        ];
                    }
                }

                $attributes['data-video'] = json_encode($videoSrcObject);
            } else if ($mediaType == 'application/pdf') {
                $attributes['data-iframe'] = 'true';
                $attributes['data-src'] = $source;
            } else {
                $attributes['data-src'] = $source;
            }
            $html .= '<div ' . tag_attributes($attributes) . '>';
            $html .= '</div>';
        }
        $html .= '</div>';
        return $html;
    }

    public function otherFiles($files)
    {
        $sortedFiles = $this->_prepareFiles($files);
        if ($sortedFiles['other']) {
            $html = '';
            $html .= '<div id="other-files" class="element">';
            $html .= '<h3>' . $this->view->translate('Other Files') . '</h3>';
            $html .= $this->_displayFileList($sortedFiles['other']);
            $html .= '</div>';
            return $html;
        }
    }

    protected function _displayFileList($files)
    {
        $html = '';
        foreach ($files as $file) {
            $linkToFileMetadata = option('link_to_file_metadata');
            $fileLink = ($linkToFileMetadata) ? record_url($file, 'show') : $file->getWebPath();
            $html .= '<div class="element-text"><a href="' . $fileLink . '" class="other-files-link">';
            $html .= record_image($file, 'square_thumbnail', ['alt' => '']);
            $html .= $file->getProperty('display_title');
            $html .= '</a></div>';
        }
        return $html;
    }

    protected function _prepareFiles($files)
    {
        $sortedFiles = ['gallery' => [], 'other' => []];
        $whitelist = ['image/bmp', 'image/gif', 'image/jpeg', 'image/png', 'image/svg+xml', 'audio/mp3', 'audio/mpeg', 'audio/mpeg3', 'audio/aac', 'audio/mp4', 'audio/ogg', 'video/mp4', 'video/x-m4v', 'video/ogg', 'video/webm', 'video/quicktime', 'application/pdf'];
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
