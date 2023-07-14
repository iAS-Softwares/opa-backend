<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory;
	
    protected $table = 'images';
	
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'user_id',
        'filename',
        'file_location',
        'mime',
    ];
	
	
    static function makeEntry($filename, $fileLocation, $owner, $name='')
    {
		$fileName=storage_path() .$fileLocation.'/'.$filename;
		$fileName=avinash_is_local()?(str_replace("\\", "/", $fileName)):$fileName;
		$mime = null;
		// Try fileinfo first
		if (function_exists('finfo_open')) {
			$finfo = finfo_open(FILEINFO_MIME);
			if ($finfo !== false) {
				$mime = finfo_file($finfo, $fileName);
				finfo_close($finfo);
			}
		}
		// Fallback to mime_content_type() if finfo didn't work
		if (is_null($mime) && function_exists('mime_content_type')) {
			$mime = mime_content_type($fileName);
		}
		// Final fallback, detection based on extension
		if (is_null($mime)) {
			$extension = self::getTypeIcon(getTypeIcon);
			if (array_key_exists($extension, self::$mimeMap)) {
				$mime = self::$mimeMap[$extension];
			} else {
				$mime = "application/octet-stream";
			}
		}
		
        $newImage = Image::create([
			'name' => $name,
			'user_id' => $owner,
			'filename' => $filename,
			'file_location' => $fileLocation . $filename,
			'mime' => $mime
		]);
		return $newImage;
    }
}
