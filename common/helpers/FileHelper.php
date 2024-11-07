<?php

namespace common\helpers;

class FileHelper
{
    // Upload files from: "data:image/png;base64,<Base64 data>"
    static public function saveBase64File($date, $direction, $base64Data)
    {
        $start = strtolower(substr($base64Data, 0, 11));
        if ($start != 'data:image/') return false;

        // Get information
        $infoEnd = strpos($base64Data, ','); // Find end of information
        $info = strtolower(substr($base64Data, 0, $infoEnd)); // Get information
        $clearBase64 = substr($base64Data, $infoEnd + 1); // Clean up base64 data

        // Get mine type
        $mimeEnd = strpos($info, ';');
        $mime = strtolower(substr($info, 5, $mimeEnd - 5));

        // Get extension
        $mimeParts = explode('/', $mime);
        $ext = $mimeParts[1] ?? '';
        if ($ext == 'jpeg') $ext = 'jpg';
        if (!in_array($ext, ['png', 'jpg', 'gif', 'tiff'])) return false;

        // Prepare file name
        $key = StringHelper::generateFileKey();

        // Get subdirectory
        $dir = \Yii::getAlias('@files') . '/' . $date . '/';
        if (!file_exists($dir)) {
            \yii\helpers\FileHelper::createDirectory($dir, 0775);
//            mkdir($dir); chmod($dir, 0775);
        }

        // File name
        $file = StringHelper::getSlug($direction) . '_' . time() . '_' . $key . '.' . $ext;

        if (file_exists($dir . $file)) return false;

        // Save file
        $ifp = fopen( $dir . $file, 'wb' );
        if (!$ifp) return false;

        fwrite($ifp, base64_decode($clearBase64));
        fclose($ifp);

        return '/' . $date . '/' . $file;
    }
}