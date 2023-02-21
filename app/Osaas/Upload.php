<?php

    namespace App\Osaas;

    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\File;
    use Illuminate\Support\Facades\Storage;
    use Intervention\Image\ImageManager;


    class Upload
    {
        /**
         * Upload single.
         *
         * @param $image
         * @param $path
         * @param $formats
         * @return string
         */
        public static function image($image,$path,$formats = null)
        {
            $image_to_store = sha1(time().uniqid()).'.'.'jpg';
            $manager = new ImageManager(['driver' => 'gd']);
            if(!File::exists($path)) {
                Storage::makeDirectory($path);
            }
            $temp_image = public_path('storage/'.$path.'/'.$image_to_store);
            if($formats){
                $manager->make($image)->fit($formats[0], $formats[1])->save($temp_image);
            }else{
                $manager->make($image)->save($temp_image);
            }
            return $image_to_store;
        }

        public static function file($file,$path)
        {
            $uploadedFile = $file;
            $extension = $uploadedFile->getClientOriginalExtension();
            $filename = sha1(time().uniqid()).'.'.$extension;
            if(!File::exists($path)) {
                Storage::makeDirectory($path);
            }
            Storage::disk('public')->putFileAs(
                $path,
                $uploadedFile,
                $filename
            );
            return $filename;
        }
    }
