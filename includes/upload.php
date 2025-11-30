<?php
// Image upload handler
function uploadImage($file, $folder = 'uploads') {
    // Create uploads directory if it doesn't exist
    if (!is_dir($folder)) {
        mkdir($folder, 0755, true);
    }

    // Validate file
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $filename = $file['name'];
    $filesize = $file['size'];
    $fileerror = $file['error'];
    $filetmp = $file['tmp_name'];

    // Get file extension
    $fileext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

    // Validate extension
    if (!in_array($fileext, $allowed)) {
        return ['success' => false, 'message' => 'Invalid file type. Allowed: JPG, PNG, GIF, WebP'];
    }

    // Validate file size (max 5MB)
    if ($filesize > 5000000) {
        return ['success' => false, 'message' => 'File too large. Max 5MB'];
    }

    // Check for upload errors
    if ($fileerror !== 0) {
        return ['success' => false, 'message' => 'Error uploading file'];
    }

    // Generate unique filename
    $newfilename = uniqid('img_', true) . '.' . $fileext;
    $filedestination = $folder . '/' . $newfilename;

    // Move file
    if (move_uploaded_file($filetmp, $filedestination)) {
        return [
            'success' => true,
            'filename' => $newfilename,
            'path' => $filedestination,
            'url' => '/' . $filedestination
        ];
    } else {
        return ['success' => false, 'message' => 'Failed to move uploaded file'];
    }
}

// Delete image
function deleteImage($filename, $folder = 'uploads') {
    $filepath = $folder . '/' . $filename;
    if (file_exists($filepath)) {
        unlink($filepath);
        return true;
    }
    return false;
}
?>
