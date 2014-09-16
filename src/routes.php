<?php

function findRecursiveImages($path) {
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path), RecursiveIteratorIterator::CHILD_FIRST);
    foreach ($iterator as $path) {
        if ($path->isDir()) {
            //skip directories
            continue;
        } else {
            $files[] = $path->__toString();
        }
    }
    return $files;
}

Route::post('redactor/upload/image', function() {
    try {
        // Check for incoming file
        if (Input::hasFile('file')) { 
            // Make sure the image matches a mimetype
            if(in_array(Input::file('file')->getMimeType(), Config::get('laravel-redactor::config.image_mime_types'))) { 
                $destination = Config::get('laravel-redactor::config.image_upload_path') . str_random(6);
                // Upload th image
                $path = Input::file('file')->move($destination, Input::file('file')->getClientOriginalName()); 
                return Response::json(array(
                    'filelink' => '/' . (string)$path)
                );
            }
        }
    } catch (Exception $e) {
        return Response::json(array(
            'error' => 'Error uploading Image.',
        ));
    };
});

Route::post('redactor/upload/file', function() {
    try {
        // Check for incoming file
        if (Input::hasFile('file')) {
            $destination = Config::get('laravel-redactor::config.file_upload_path') . str_random(6);
             // Upload th image
            $path = Input::file('file')->move($destination, Input::file('file')->getClientOriginalName());
            return Response::json(array(
                    'filelink' => '/' . (string)$path,
                    'filename' => basename($path)
                )
            );
        }
    } catch (Exception $e) {
        return Response::json(array(
            'error' => 'Error uploading Image.',
        ));
    };
});

Route::get('redactor/images', function() {
    $images = findRecursiveImages('upload/images');

    $images = array_map(
        function ($image) {
            return array(
                'thumb' => $image,
                'image' => $image,
                'title' => basename($image),
                'folder' => dirname ($image)
            );
        }, $images);

    return Response::json($images);
});