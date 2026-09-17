<?php
namespace App\Core;

class FileUploader
{
    public static function upload(array $file): ?string
    {
        if (!isset($file['error']) || $file['error'] === UPLOAD_ERR_NO_FILE) return null;
        if ($file['error'] !== UPLOAD_ERR_OK) throw new \RuntimeException("อัปโหลดไฟล์ล้มเหลว");

        if ($file['size'] > 5 * 1024 * 1024) throw new \InvalidArgumentException("ขนาดไฟล์ต้องไม่เกิน 5MB");

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmp_name']);
        $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];

        if (!isset($allowed[$mime])) throw new \InvalidArgumentException("รองรับเฉพาะ JPG, PNG, WEBP เท่านั้น");

        $fileName = bin2hex(random_bytes(12)) . '.' . $allowed[$mime];
        $targetDir = dirname(__DIR__, 2) . '/storage/uploads/ticket-images';
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

        if (!move_uploaded_file($file['tmp_name'], $targetDir . '/' . $fileName)) {
            throw new \RuntimeException("บันทึกไฟล์ไม่สำเร็จ");
        }

        return $fileName;
    }
}
