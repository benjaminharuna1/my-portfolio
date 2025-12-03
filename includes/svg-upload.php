<?php
/**
 * SVG Upload Handler
 * Handles SVG file uploads with proper validation, cleaning, and storage
 */

class SVGUploader {
    private $svg_upload_dir = 'uploads/svg';
    private $max_file_size = 5242880; // 5MB
    private $allowed_extensions = ['svg'];
    
    /**
     * Initialize SVG upload directory
     */
    public function __construct() {
        $this->ensureUploadDirectory();
    }
    
    /**
     * Ensure SVG upload directory exists
     */
    private function ensureUploadDirectory() {
        if (!is_dir($this->svg_upload_dir)) {
            mkdir($this->svg_upload_dir, 0755, true);
        }
    }
    
    /**
     * Upload and process SVG file
     * @param array $file $_FILES array element
     * @return array Result array with success status and data
     */
    public function upload($file) {
        // Validate file exists
        if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
            return [
                'success' => false,
                'message' => 'No file uploaded',
                'error_code' => 'NO_FILE'
            ];
        }
        
        // Validate file size
        if ($file['size'] > $this->max_file_size) {
            return [
                'success' => false,
                'message' => 'File size exceeds 5MB limit',
                'error_code' => 'FILE_TOO_LARGE'
            ];
        }
        
        // Validate file extension
        $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($file_ext, $this->allowed_extensions)) {
            return [
                'success' => false,
                'message' => 'Invalid file type. Only SVG files allowed.',
                'error_code' => 'INVALID_TYPE'
            ];
        }
        
        // Read SVG content
        $svg_content = file_get_contents($file['tmp_name']);
        if ($svg_content === false) {
            return [
                'success' => false,
                'message' => 'Failed to read SVG file',
                'error_code' => 'READ_FAILED'
            ];
        }
        
        // Clean and process SVG
        $cleaned_svg = $this->cleanSVG($svg_content);
        
        if (empty($cleaned_svg)) {
            return [
                'success' => false,
                'message' => 'SVG content is empty after processing',
                'error_code' => 'EMPTY_CONTENT'
            ];
        }
        
        // Generate unique filename
        $filename = $this->generateFilename();
        $filepath = $this->svg_upload_dir . '/' . $filename;
        
        // Save SVG file
        if (!file_put_contents($filepath, $cleaned_svg)) {
            return [
                'success' => false,
                'message' => 'Failed to save SVG file to disk',
                'error_code' => 'SAVE_FAILED'
            ];
        }
        
        return [
            'success' => true,
            'message' => 'SVG uploaded successfully',
            'filename' => $filename,
            'filepath' => $filepath,
            'svg_content' => $cleaned_svg,
            'content_length' => strlen($cleaned_svg),
            'file_size' => filesize($filepath)
        ];
    }
    
    /**
 * Clean and process SVG content safely
 * - Removes scripts, inline events, comments, XML/DOCTYPE
 * - Preserves paths, groups, defs, symbols
 * - Replaces fills with "currentColor" for CSS styling
 * @param string $svg Raw SVG content
 * @return string Cleaned SVG content
 */
private function cleanSVG($svg) {
    // Ensure UTF-8 encoding
    if (!mb_check_encoding($svg, 'UTF-8')) {
        $svg = mb_convert_encoding($svg, 'UTF-8');
    }

    // Remove XML declaration & DOCTYPE
    $svg = preg_replace('/<\?xml.*?\?>/i', '', $svg);
    $svg = preg_replace('/<!DOCTYPE.*?>/is', '', $svg);

    // Remove comments
    $svg = preg_replace('/<!--.*?-->/is', '', $svg);

    // Remove <script> tags and content
    $svg = preg_replace('/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi', '', $svg);

    // Remove inline event handlers (onclick, onmouseover, etc.)
    $svg = preg_replace('/\s+on\w+\s*=\s*["\'][^"\']*["\']/i', '', $svg);

    // Remove <style> tags but keep the content of paths/groups
    $svg = preg_replace('/<style\b[^<]*(?:(?!<\/style>)<[^<]*)*<\/style>/gi', '', $svg);

    // Remove CDATA sections
    $svg = preg_replace('/<!\[CDATA\[.*?\]\]>/is', '', $svg);

    // Replace any existing fills with currentColor for CSS styling
    $svg = preg_replace('/fill="[^"]*"/i', 'fill="currentColor"', $svg);

    // Optional: replace stroke as well for consistent styling
    $svg = preg_replace('/stroke="[^"]*"/i', 'stroke="currentColor"', $svg);

    // Minify: remove excessive whitespace between tags
    $svg = preg_replace('/>\s+</', '><', $svg);

    // Trim any remaining whitespace
    $svg = trim($svg);

    // Return cleaned SVG
    return $svg;
}

    
    /**
     * Generate unique filename for SVG
     * @return string Unique filename
     */
    private function generateFilename() {
        return 'svg_' . uniqid() . '_' . time() . '.svg';
    }
    
    /**
     * Delete SVG file
     * @param string $filename Filename to delete
     * @return bool Success status
     */
    public function delete($filename) {
        // Validate filename (prevent directory traversal)
        if (strpos($filename, '..') !== false || strpos($filename, '/') !== false) {
            return false;
        }
        
        $filepath = $this->svg_upload_dir . '/' . $filename;
        
        if (file_exists($filepath)) {
            return unlink($filepath);
        }
        
        return false;
    }
    
    /**
     * Get SVG file path
     * @param string $filename Filename
     * @return string Full file path
     */
    public function getFilePath($filename) {
        return $this->svg_upload_dir . '/' . $filename;
    }
    
    /**
     * Get SVG upload directory
     * @return string Upload directory path
     */
    public function getUploadDir() {
        return $this->svg_upload_dir;
    }
    
    /**
     * Validate SVG file exists
     * @param string $filename Filename
     * @return bool File exists
     */
    public function fileExists($filename) {
        return file_exists($this->getFilePath($filename));
    }
}

// Create global instance
$svg_uploader = new SVGUploader();
?>
