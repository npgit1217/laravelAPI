<?php

namespace App\Http\Controllers\API;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\API\BaseController;

class PostController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data['posts'] = Post :: all();

        
        return $this->sendResponce($data,'All Post Data');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        //print_r($request);die;
         $validateUser = Validator::make(
            $request -> all(),
            [
                'title' => 'required',
                'description' => 'required',
                'image' => 'required|mimes:png,jpg,jpeg,gif',
            ]
        );

        if($validateUser->fails()){
                return response()->json([
                    'status'=>false,
                    'message'=>'Validation error',
                    'errors'=>$validateUser->errors()->all()
                ],401);

                return sendError('Validation error',$validateUser->errors()->all());
            }

        $img = $request->image;
        $ext = $img->getClientOriginalExtension();
        $imageName = time().'.'.$ext; 
        $img->move(public_path().'/uploads',$imageName);

        $post = Post::create([
            'title' => $request->title,
                'description' => $request->description,
                'image' => $imageName,
        ]);

       
         return $this->sendResponce($post,'Post Created Successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data['post'] = Post::select('id','title','description','image')->where([id=>$id])->get();
       
         return $this->sendResponce($data,'Post data found');
    
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validateUser = Validator::make(
            $request -> all(),
            [
                'title' => 'required',
                'description' => 'required',
                'image' => 'required|mimes:png,jpg,jpeg,gif',
            ]
        );

        if($validateUser->fails()){
                
                return sendError('Validation error',$validateUser->errors()->all());
            }
        //return $postImage;where([id=>$id])->
        $postImage = Post::select('id','image')->where(['id'=>$id])->get();
       //return $postImage[0]['image'];
        if($request->image != ''){
            $path = public_path().'/uploads';
            if($postImage[0]['image'] != '' && $postImage[0]['image'] != null){
                if(file_exists($path.$postImage[0]['image'])){
                    unlink($path.$postImage[0]['image']);
                }
            }

            $img = $request->image;
            $ext = $img->getClientOriginalExtension();
            $imageName = time().'.'.$ext; 
            $img->move(public_path().'/uploads',$imageName);
        }else{
            $imageName = $postImage[0]['image'];
        }


        

        $post = Post::where(['id'=>$id])->update([
            'title' => $request->title,
            'description' => $request->description,
            'image' => $imageName,
        ]);

         
        return $this->sendResponce($post,'Post Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $img = Post::select('image')->where(['id'=>$id])->get();
        $path = public_path().'/uploads/'.$img[0]['image'];
        unlink($path);

        $post = Post::where('id',$id)->delete();
        
        return $this->sendResponce($post,'Post Deleted Successfully');        
    }
}
