<?php
// Image upload handler
function uploadImage($file, $folder = 'uploads') {
    try {
        // Create uploads directory if it doesn't exist
        if (!is_dir($folder)) {
            if (!mkdir($folder, 0755, true)) {
                throw new Exception("Failed to create uploads directory: $folder");
            }
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
            ErrorLogger::log("Invalid file type attempted: $fileext for file: $filename", 'WARNING');
            return ['success' => false, 'message' => 'Invalid file type. Allowed: JPG, PNG, GIF, WebP'];
        }

        // Validate file size (max 5MB)
        if ($filesize > 5000000) {
            ErrorLogger::log("File too large: $filename (" . ($filesize / 1024 / 1024) . "MB)", 'WARNING');
            return ['success' => false, 'message' => 'File too large. Max 5MB'];
        }

        // Check for upload errors
        if ($fileerror !== 0) {
            $error_messages = [
                UPLOAD_ERR_INI_SIZE => 'File exceeds upload_max_filesize',
                UPLOAD_ERR_FORM_SIZE => 'File exceeds MAX_FILE_SIZE',
                UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
                UPLOAD_ERR_NO_FILE => 'No file was uploaded',
                UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
                UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
                UPLOAD_ERR_EXTENSION => 'File upload stopped by extension'
            ];
            $error_msg = $error_messages[$fileerror] ?? 'Unknown upload error';
            ErrorLogger::log("Upload error for $filename: $error_msg", 'ERROR');
            return ['success' => false, 'message' => 'Error uploading file: ' . $error_msg];
        }

        // Generate unique filename
        $newfilename = uniqid('img_', true) . '.' . $fileext;
        $filedestination = $folder . '/' . $newfilename;

        // Move file
        if (move_uploaded_file($filetmp, $filedestination)) {
            ErrorLogger::log("File uploaded successfully: $newfilename", 'INFO');
            return [
                'success' => true,
                'filename' => $newfilename,
                'path' => $filedestination,
                'url' => '/' . $filedestination
            ];
        } else {
            throw new Exception("Failed to move uploaded file from $filetmp to $filedestination");
        }
    } catch (Exception $e) {
        ErrorLogger::log("Upload exception: " . $e->getMessage(), 'ERROR');
        return ['success' => false, 'message' => 'Failed to move uploaded file'];
    }
}

// Delete image
function deleteImage($filename, $folder = 'uploads') {
    try {
        $filepath = $folder . '/' . $filename;
        if (file_exists($filepath)) {
            if (unlink($filepath)) {
                ErrorLogger::log("File deleted successfully: $filename", 'INFO');
                return true;
            } else {
                throw new Exception("Failed to delete file: $filepath");
            }
        }
        return false;
    } catch (Exception $e) {
        ErrorLogger::log("Delete exception: " . $e->getMessage(), 'ERROR');
        return false;
    }
}
?>
