<?php

namespace GetHoard\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

use GetHoard\Models\Favourite;
use Auth;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class Snippet extends Model
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'snippets';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'extension', 'code', 'description'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];
    
    public function author() {
        return $this->belongsTo('GetHoard\Models\User', 'user_id');
    }
    
    public function favourites() {
        return $this->hasMany('GetHoard\Models\Favourite');
    }
    
    public function generateCode() {
	    if($this->code == null) {
		    $code = substr(md5(microtime()),rand(0,26),8);
		    while (Snippet::where('code', $code)->count() != 0) {
			    $code = substr(md5(microtime()),rand(0,26),8);
		    }
		    $this->code = $code;		  
		    
		    return $code;
	    }
    }
    
    public function getContents() {
	    $contents = Storage::disk('local')->get($this->code);
	    return $contents;
    }
    
    public function updateContents($contents) {
        Storage::disk('local')->put($this->code, $contents);
    }
    
    public function getDate() {
	    return date("jS F Y", strtotime($this->updated_at));
    }
    
    public function getSize() {
	    return $this->formatSize(Storage::size($this->code));
    }
    
    public function descShort() {
	    return $this->description ? 
	    	(strlen($this->description) > 90 ? 
	    		substr($this->description, 0, 89).'...' : $this->description)
			: "No description.";
    }
    
    public function isFavourited() {
	    if(!Auth::check()) {
		    return false;
	    }
	    return Favourite::where('user_id', Auth::user()->id)
        			  ->where('snippet_id', $this->id)->count() > 0 ? true : false;
    }
    
    public function formatSize($bytes, $precision = 2) { 
	    $units = array('B', 'KB', 'MB', 'GB', 'TB'); 
	
	    $bytes = max($bytes, 0); 
	    $pow = floor(($bytes ? log($bytes) : 0) / log(1024)); 
	    $pow = min($pow, count($units) - 1); 
	
	    // Uncomment one of the following alternatives
	    // $bytes /= pow(1024, $pow);
	     $bytes /= (1 << (10 * $pow)); 
	
	    return round($bytes, $precision) . '' . $units[$pow]; 
	}
	
	public function getLangFromExtension() {
		$extensions = [
			'js' => 'javascript',
			'html' => 'markup',
			'html5' => 'markup',
			'htm' => 'markup',
			'xhtml' => 'markup',
			'jhtml' => 'markup',
			'php' => 'php',
			'php5' => 'php',
			'php4' => 'php',
			'php3' => 'php',
			'phtml' => 'php',
			'jhtml' => 'markup',
			'svg' => 'markup',
			'xml' => 'markup',
			'rss' => 'markup',
			'css' => 'css',
			'py' => 'python',
			'rb' => 'ruby'
		];
		
		return (array_key_exists($this->extension, $extensions)) ? 
			$extensions[$this->extension] : 'unknown';
	}
}
