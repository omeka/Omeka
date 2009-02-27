<?php 
class Omeka_File_Derivative_Image
{
    protected static function checkOmekaCanMakeDerivativeImages()
    {
        //Check to see if ImageMagick is installed
        if (!self::checkForImageMagick(get_option('path_to_convert'))) {
            throw new Omeka_Upload_Exception('ImageMagick is not properly configured.  Please check your settings and then try again.' );
        }        
        
        //Check the constraints to make sure they are valid
        $constraints = array('fullsize_constraint', 
                             'thumbnail_constraint', 
                             'square_thumbnail_constraint');
        
        foreach ($constraints as $constraint) {
            $constraint_size = get_option($constraint);
            
            if (!$constraint_size or !is_numeric($constraint_size)) {
                throw new Omeka_Upload_Exception( 
                    "The sizes for derivative images have not been configured properly." );
            }
        }
    }
    
    /**
     * Determine whether or not ImageMagick has been correctly installed or configured for Omeka to use.  
     * 
     * This appears to work on most hosting environments, but there are some
     * where ImageMagick may return codes other than 0 even though it appears to
     * be loaded on the host machine. It remains to be seen whether this is an
     * error in configuring their servers or an error where Omeka should
     * examine/accept other return status codes.
     *
     * @param string
     * @return boolean True if the command line return status is 0 when
     * attempting to run ImageMagick's convert utility, false otherwise.
     **/
    protected static function checkForImageMagick($path) {
        exec($path, $convert_version, $convert_return);
        return($convert_return == 0);
    }
    
    /**
     * Generate all the derivative images for this file.  
     * 
     * Currently, derivative images include 'fullsize', 'thumbnail' and 
     * 'square_thumbnail' sizes. New sizes could be added if a plugin were to 
     * hook into the 'after_file_upload' hook, so this method does not need to 
     * be extensible.
     * 
     * @param string
     * @return void
     **/
    public function createDerivativeImages($path)
    {
        //Function processes derivatives of every image uploaded - additional images may be created using createImage function.  Additionally, plugin hooks allow you to add your own additional image sizes [DL]
        
        //Retrieve the image sizes from the database
        $full_constraint = get_option('fullsize_constraint');
        $thumb_constraint = get_option('thumbnail_constraint');
        $square_thumbnail_constraint = get_option('square_thumbnail_constraint');
        
        $this->createImage($path, FULLSIZE_DIR, $full_constraint);
        $this->createImage($path, THUMBNAIL_DIR, $thumb_constraint);
        $this->createImage($path, SQUARE_THUMBNAIL_DIR, $square_thumbnail_constraint, "square");
        
    }
    
    /**
     * Generate a derivative image from an existing image stored in Omeka's archive.  
     * 
     * This image will be generated based on a constraint given in pixels.  For 
     * example, if the constraint is 500, the resulting image file will be scaled 
     * so that the largest side is 500px. If the image is less than 500px on both 
     * sides, the image will not be resized.
     * 
     * All derivative images will be JPEG, which is specified by the constant 
     * IMAGE_DERIVATIVE_EXT.  
     * 
     * Currently, derivative images will only be generated for file types that 
     * can be read by PHP's getimagesize() function.  Documentation for supported 
     * file types can be found on PHP.net's doc page for getimagesize() or 
     * image_type_to_mime_type().
     * 
     * @throws Omeka_Upload_Exception
     * @param string The full path to the archived file.
     * @param string The full path to the directory in which to create the derivative image.
     * @param integer The size constraint for the image (in pixels).
     * @param string The type of the image to generate (optional).  If the type 
     * specified is "square", Omeka will generated a derivative image that is 
     * centered and cropped to a square.  This is primarily used for generation 
     * of square thumbnails, though a plugin could also take advantage of it.
     * @return string The filename of the generated image file.
     **/
    protected function createImage( $old_path, $new_dir, $constraint, $type=null) {
            
        $convertPath = get_option('path_to_convert');
                        
        if (file_exists($old_path) && is_readable($old_path) && getimagesize($old_path)) {    
            
            $filename = basename($old_path);
            $new_name = explode('.', $filename);
            //ensures that all generated files are jpeg
            $new_name[1] = IMAGE_DERIVATIVE_EXT;
            $imagename = implode('.', $new_name);
            $new_path = rtrim($new_dir, DIRECTORY_SEPARATOR ) . DIRECTORY_SEPARATOR . $imagename;

            $old_path = escapeshellarg( $old_path );
            $new_path = escapeshellarg( $new_path );

            if (!$constraint) {
                throw new Omeka_Upload_Exception('Image creation failed - Image size constraint must be specified within application settings');
            }

            switch ($type) {
            case "square":
                $command = ''.$convertPath.' '.$old_path.' -thumbnail x'.($constraint*2).' -resize "'.($constraint*2).'x<" -resize 50% -gravity center -crop '.$constraint.'x'.$constraint.'+0+0 +repage '.$new_path.'';
                break;
            default:
                $command = ''.$convertPath.' '.$old_path.' -resize '.escapeshellarg($constraint.'x'.$constraint.'>').' '.$new_path.'';                        
            }

            exec($command, $result_array, $result_value);

            if ($result_value == 0) {
                //Image was created, so set the derivative bitflag
                if (!$this->has_derivative_image) {
                    $this->has_derivative_image = 1;
                }

                return $imagename;    
            } else {
                throw new Omeka_Upload_Exception('Something went wrong with image creation.  Please notify an administrator.');
            }
        }
    }    
}