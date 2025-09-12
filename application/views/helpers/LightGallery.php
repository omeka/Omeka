<?php
/**
 * Return media viewer using lightGallery plugin.
 *
 * @package Omeka\View\Helper
 */
class Omeka_View_Helper_LightGallery extends Zend_View_Helper_Abstract
{
    protected static $_callbacks = [
        'image/bmp' => 'Omeka_View_Helper_LightGallery::image',
        'image/gif' => 'Omeka_View_Helper_LightGallery::image',
        'image/jpeg' => 'Omeka_View_Helper_LightGallery::image',
        'image/png' => 'Omeka_View_Helper_LightGallery::image',
        'image/svg+xml' => 'Omeka_View_Helper_LightGallery::image',
        'image/jp2' => 'Omeka_View_Helper_LightGallery::derivativeImage',
        'image/tiff' => 'Omeka_View_Helper_LightGallery::derivativeImage',
        'audio/mp3' => 'Omeka_View_Helper_LightGallery::video',
        'audio/mpeg' => 'Omeka_View_Helper_LightGallery::video',
        'audio/mpeg3' => 'Omeka_View_Helper_LightGallery::video',
        'audio/aac' => 'Omeka_View_Helper_LightGallery::video',
        'audio/mp4' => 'Omeka_View_Helper_LightGallery::video',
        'audio/ogg' => 'Omeka_View_Helper_LightGallery::video',
        'video/mp4' => 'Omeka_View_Helper_LightGallery::video',
        'video/x-m4v' => 'Omeka_View_Helper_LightGallery::video',
        'video/ogg' => 'Omeka_View_Helper_LightGallery::video',
        'video/webm' => 'Omeka_View_Helper_LightGallery::video',
        'video/quicktime' => 'Omeka_View_Helper_LightGallery::video',
        'application/pdf' => 'Omeka_View_Helper_LightGallery::pdf',
    ];

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
        $callbacks = self::getCallbacks();

        if (!$sortedFiles['gallery']) {
            return '';
        }

        $html = '<div id="omeka-lightgallery" class="lightgallery">';
        $captionOption = get_theme_option('lightgallery_caption');

        foreach ($sortedFiles['gallery'] as $galleryEntry) {
            $file = $galleryEntry['file'];
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
                'data-download-url' => $file->getWebPath(),
                'title' => $file->getAltText(),
            ];
            if (strlen((string) $caption)) {
                $attributes['data-sub-html'] = $caption;
            };

            $fileAttrs = call_user_func($callbacks[$file->mime_type], $file);
            $html .= '<div ' . tag_attributes($fileAttrs + $attributes) . '></div>';
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
        $callbacks = self::getCallbacks();
        $sortedFiles = ['gallery' => [], 'other' => []];
        $html5videos = [];

        $index = 0;
        foreach ($files as $file) {
            $mediaType = $file->mime_type;
            if (array_key_exists($mediaType, $callbacks)) {
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

    protected static function image($file)
    {
        return ['data-src' => $file->getWebPath()];
    }

    protected static function derivativeImage($file)
    {
        return ['data-src' => record_image_url($file, 'fullsize')];
    }

    protected static function video($file)
    {
        $mediaType = ($file->mime_type == 'video/quicktime') ? 'video/mp4' : $file->mime_type;
        $videoSrcObject = [
            'source' => [
                [
                    'src' => $file->getWebPath(),
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
        return ['data-video' => json_encode($videoSrcObject)];
    }

    protected static function pdf($file)
    {
        return [
            'data-iframe' => 'true',
            'data-iframe-title' => $file->getAltText(),
            'data-src' => $file->getWebPath(),
        ];
    }

    protected static function getCallbacks()
    {
        return apply_filters('light_gallery_callbacks', self::$_callbacks);
    }
}
