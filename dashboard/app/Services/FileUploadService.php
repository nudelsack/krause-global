<?php

namespace App\Services;

class FileUploadService
{
    private $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function upload($file, $dealId, $category)
    {
        // Validate file
        $validation = $this->validate($file);
        if (!$validation['valid']) {
            throw new \RuntimeException($validation['error']);
        }

        // Generate storage path
        $uploadPath = $this->getAbsolutePath($this->config['paths']['uploads']);
        $dealPath = $uploadPath . '/' . $dealId . '/' . $category;
        
        if (!is_dir($dealPath)) {
            mkdir($dealPath, 0755, true);
        }

        // Generate unique filename
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $timestamp = time();
        $random = bin2hex(random_bytes(4));
        $filename = "{$timestamp}_{$random}.{$ext}";
        
        $destinationPath = $dealPath . '/' . $filename;
        $relativePath = 'uploads/' . $dealId . '/' . $category . '/' . $filename;

        // Move uploaded file
        if (!move_uploaded_file($file['tmp_name'], $destinationPath)) {
            throw new \RuntimeException('Failed to move uploaded file');
        }

        // Calculate SHA256
        $sha256 = hash_file('sha256', $destinationPath);

        return [
            'original_filename' => $file['name'],
            'stored_path' => $relativePath,
            'mime_type' => $file['type'],
            'file_size' => $file['size'],
            'sha256' => $sha256,
        ];
    }

    public function validate($file)
    {
        if (!isset($file['error']) || is_array($file['error'])) {
            return ['valid' => false, 'error' => 'Invalid file upload'];
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['valid' => false, 'error' => 'Upload error: ' . $file['error']];
        }

        if ($file['size'] > $this->config['upload']['max_file_size']) {
            $maxMb = $this->config['upload']['max_file_size'] / 1048576;
            return ['valid' => false, 'error' => "File too large. Maximum: {$maxMb}MB"];
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);
        
        if (!in_array($mimeType, $this->config['upload']['allowed_mime_types'])) {
            return ['valid' => false, 'error' => 'File type not allowed'];
        }

        return ['valid' => true];
    }

    private function getAbsolutePath($path)
    {
        if ($path[0] === '/') {
            return $path;
        }
        return __DIR__ . '/../../' . $path;
    }
}
