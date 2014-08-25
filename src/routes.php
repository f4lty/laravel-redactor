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
    // Check for incoming file
    if (Input::hasFile('file')) { 
        $mineTypes = array('image/png', 'image/jpg', 'image/gif', 'image/jpeg', 'image/pjpeg' );
        // Make sure the image matches a mimetype
        if(in_array(Input::file('file')->getMimeType(), $mineTypes)) { 
            // Upload th image
            $path = Input::file('file')->move('upload/images/' . str_random(6), Input::file('file')->getClientOriginalName()); 
            return Response::json(array(
                'filelink' => (string)$path)
            );
        }
    }
});

Route::post('redactor/upload/file', function() {
    // Check for incoming file
    if (Input::hasFile('file')) {
         // Upload th image
        $path = Input::file('file')->move('upload/files/' . str_random(6), Input::file('file')->getClientOriginalName());
        return Response::json(array(
            'filelink' => (string)$path,
            'filename' => basename($path)
            )
        );
    }
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