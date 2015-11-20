<?php

namespace GetHoard\Http\Controllers;

use GetHoard\Models\Auth\User;
use GetHoard\Models\Snippet;
use Validator;
use Auth;
use GetHoard\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class SnippetController extends Controller
{
    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth', ['except' => '']);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|alpha_dash|max:25',
            'extension' => 'required|alpha_num|max:5',
            'description' => 'required|string|max:255',
			//'code' => 'required|alpha_num|size:8|unique:snippets',
        ]);
    }

    /**
     * Create a new Snippet instance after a valid submission.
     *
     * @param  array  $data
     * @return Snippet
     */
    protected function create(array $data)
    {
        $snippet = Snippet::create([
            'name' => $data['name'],
            'extension' => strtolower($data['extension']),
            'description' => $data['description'],
        ]);
        
        $code = $snippet->generateCode();
        $snippet->updateContents(File::get($data['file']));
        $snippet->author()->associate(Auth::user());
        $snippet->save();
        
        return $snippet;
    }
    
    public function getUpload() {
	    return view('snippets.upload');
    }
    
    public function getView() {
	    return view('snippets.view')->withSnippets(Auth::user()->snippets);
    }
    
    public function postUpload(Request $request) {
	    
	    if (!$request->file('file')->isValid()) {
            return redirect()->back()
                        ->withErrors(['File is not valid.'])
                        ->withInput();
		}
		
	    $file = $request->file('file');
	    $data['file'] = $file;
	    $data['description'] = $request->get('description');
		$data['extension'] = $file->getClientOriginalExtension();
	    $data['name'] = preg_replace('/\\.[^.\\s]{3,4}$/', '', $file->getClientOriginalName());
		
	    $validator = $this->validator($data);
		if($validator->fails()) {
            return redirect()->back()
                        ->withErrors($validator)
                        ->withInput();
        }
        
        $this->create($data);
        
        return redirect()->action('SnippetController@getView');
    }
}