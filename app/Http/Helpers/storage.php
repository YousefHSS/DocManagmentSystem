<?php

function clearStorage($path = null)
{
    $files = glob(storage_path($path ?? 'app/UploadedDocs/*'));
    foreach ($files as $file) {
        if (is_file($file)) {
            unlink($file);
        }
    }
}

