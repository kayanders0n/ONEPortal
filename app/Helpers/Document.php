<?php

namespace Helpers;

class Document
{
    /**
     * @param $extension
     *
     * @return string
     */
    public static function getFileType($extension): string
    {
        $images = ['jpg', 'gif', 'png', 'bmp'];
        $docs   = ['txt', 'rtf', 'doc', 'docx', 'pdf'];
        $apps   = ['zip', 'rar', 'exe', 'html'];
        $video  = ['mpg', 'wmv', 'avi', 'mp4'];
        $audio  = ['wav', 'mp3'];
        $db     = ['sql', 'csv', 'xls', 'xlsx'];

        if (in_array($extension, $images, true)) {
            return 'Image';
        }

        if (in_array($extension, $docs, true)) {
            return 'Document';
        }

        if (in_array($extension, $apps, true)) {
            return 'Application';
        }

        if (in_array($extension, $video, true)) {
            return 'Video';
        }

        if (in_array($extension, $audio, true)) {
            return 'Audio';
        }

        if (in_array($extension, $db, true)) {
            return 'Database/Spreadsheet';
        }

        return 'Other';
    }

    public static function getFileMimeType($extension): string {

        $mime_type = 'application/octet-stream';

        switch ($extension) {
            case 'asf': $mime_type = 'video/x-ms-asf'; break;
            case 'mov': $mime_type = 'video/quicktime'; break;
            case 'mp3': $mime_type = 'video/mpeg'; break;
            case 'avi': $mime_type = 'video/x-msvideo'; break;
            case 'pdf': $mime_type = 'application/pdf'; break;
            case 'jpg': $mime_type = 'image/jpeg'; break;
            case 'tif': $mime_type = 'image/tiff'; break;
            case 'doc': $mime_type = 'application/msword'; break;
            case 'docx': $mime_type = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'; break;
            case 'mht': $mime_type = 'message/rfc822'; break;
            case 'xls': $mime_type = 'application/vnd.ms-excel'; break;
            case 'xlsx': $mime_type = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'; break;
            case 'txt': $mime_type = 'text/plain'; break;
        }

        return $mime_type;
    }

    /**
     * Create a human friendly measure of the size provided.
     *
     * @param  integer $bytes file size
     * @param  integer $precision precision to be used
     *
     * @return string             size with measure
     */
    public static function formatBytes($bytes, $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        $bytes = max($bytes, 0);
        $pow   = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow   = min($pow, count($units) - 1);

        $bytes /= 1024 ** $pow;

        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    /**
     * Converts a human readable file size value to a number of bytes that it
     * represents. Supports the following modifiers: K, M, G and T.
     * Invalid input is returned unchanged.
     *
     * Example:
     * <code>
     * $config->getBytesSize(10);          // 10
     * $config->getBytesSize('10b');       // 10
     * $config->getBytesSize('10k');       // 10240
     * $config->getBytesSize('10K');       // 10240
     * $config->getBytesSize('10kb');      // 10240
     * $config->getBytesSize('10Kb');      // 10240
     * // and even
     * $config->getBytesSize('   10 KB '); // 10240
     * </code>
     *
     * @param $value
     *
     * @return mixed
     */
    public static function getBytesSize($value)
    {
        return preg_replace_callback('/^\s*(\d+)\s*(?:([kmgt]?)b?)?\s*$/i', function ($m) {
            switch (strtolower($m[2])) {
                case 't':
                    $m[1] *= 1024;
                    break;
                case 'g':
                    $m[1] *= 1024;
                    break;
                case 'm':
                    $m[1] *= 1024;
                    break;
                case 'k':
                    $m[1] *= 1024;
                    break;
            }

            return $m[1];
        }, $value);
    }

    /**
     * Return the bytes file of a folder.
     *
     * @param string $path
     *
     * @return string
     */
    public static function getFolderSize($path): string
    {
        $io   = popen('/usr/bin/du -sb ' . $path, 'r');
        $size = (int) fgets($io, 80);
        pclose($io);

        return $size;
    }

    /**
     * Return the file type based on the filename provided.
     *
     * @param  string $file
     *
     * @return string
     */
    public static function getExtension($file): string
    {
        return pathinfo($file, PATHINFO_EXTENSION);
    }

    /**
     * @param $file
     *
     * @return mixed
     */
    public static function removeExtension($file)
    {
        if (strpos($file, '.')) {
            $file = pathinfo($file, PATHINFO_FILENAME);
        }

        return $file;
    }

    /**
     * @param $ref_id
     *
     * @return string
     */
    public static function splitId($ref_id): string
    {
        $split_id = '';
        while (mb_strlen($ref_id) > 0) {
            $split_id .= '/' . mb_substr($ref_id, 0, 1);
            $ref_id   = mb_substr($ref_id, 1, 99);
        }
        $split_id .= '/';

        return $split_id;
    }

    /**
     * @param $ref_id
     * @param $path
     *
     * @return string
     */
    public static function mkdirSplit($ref_id, $path): string
    {
        $split_dir = $path;
        while (mb_strlen($ref_id) > 0) {
            $split_dir .= '/' . mb_substr($ref_id, 0, 1);
            $ref_id    = mb_substr($ref_id, 1, 99);
            if (!file_exists($split_dir)) {
                mkdir($split_dir, 0775, true);
            }
        }

        return $split_dir;
    }

    /**
     * @param int $server_id
     * @param string $file_name
     * @param string $type
     * @param bool $is_zip
     * @param bool $is_range
     * @return string|null
     */
    // TODO add the unzip include file and class access that is needed
    public static function getServerDocument(int $server_id=0, string $file_name='', string $type='', bool $is_zip=false, bool $is_range=false): ?string {

        $temp_path = config('temp_path');
        $exe_path = config('cmd_exe_path');
        $doc_host = config('document_server');

        $extension = strtolower(Self::getExtension($file_name));
        $type = strtolower($type);

        $cached_file = $temp_path . 'doc_cache_' . $server_id . '.' . ($type ?: $extension);

        if (file_exists($cached_file)) {

            $data = file_get_contents($cached_file);

            // clean up old files
            foreach (glob($temp_path . 'doc_cache_*') as $file) {

                /*** 24 hours (86400 seconds) ***/
                if (time() - filectime($file) > 900) { // 15 minutes old
                    unlink($file);
                }
            }

            return $data;
        }

        if ($server_id) {
            $spec = array(
                0 => array("pipe", "r"),  // stdin is a pipe that the child will read from
                1 => array("pipe", "w"),  // stdout is a pipe that the child will write to
                2 => array("pipe", "w")
            );

            $data_file = tempnam($temp_path, 'data');

            $process = proc_open($exe_path."cmdGetDocClient.exe $doc_host $server_id > $data_file", $spec, $pipes, null, null);
            proc_close($process); // we must do there here so that it will wait for the process to finish before it can do anything else with the file it created

            // pipes are closed automatically when you close process
            $process = null;
            $pipes = null;

            if ($is_zip) {
                $temp_file = tempnam($temp_path, 'zip');

                $data = file_get_contents($data_file);
                unlink($data_file);

                file_put_contents($temp_file, $data);
                $zip = new unzipfile($temp_file);

                $data = $zip->unzip($file_name);

                $zip->close();
                unlink($temp_file);
            } else {
                $data = file_get_contents($data_file);
                unlink($data_file);
            }

            if ($type) {
                if ($extension == 'asf') { // this is a video file we can convert it using ffmpeg
                    if ($extension != $type) {
                        $temp_file = tempnam($temp_path, $extension);
                        // save the memory to the temp file
                        file_put_contents($temp_file, $data);
                        $new_file = $temp_file . '.' . $type;
                        $old_file = $temp_file. '.' . $extension;
                        rename($temp_file, $old_file);
                        $process = proc_open($exe_path."ffmpeg.exe -i $old_file -y $new_file", $spec, $pipes, $temp_path, null);
                        proc_close($process); // wait for process to finish and clean up

                        // pipes are closed automatically when you close process
                        $process = null;
                        $pipes = null;

                        $data = file_get_contents($new_file);

                        unlink($old_file);

                        rename($new_file, $temp_path . $server_id . '.' . $type);
                    }
                } else if ($extension == 'tif') {
                    if ($extension != $type) {
                        $temp_file = tempnam($temp_path, $extension);
                        file_put_contents($temp_file, $data);
                        $new_file = $temp_file . '.' . $type;
                        $old_file = $temp_file . '.' . $extension;
                        rename($temp_file, $old_file);

                        $process = proc_open($exe_path."T2PDF.exe $old_file $new_file", $spec, $pipes, $temp_path , null);
                        proc_close($process); // wait for process to finish and clean up

                        // pipes are closed automatically when you close process
                        $process = null;
                        $pipes = null;

                        $data = file_get_contents($new_file);

                        unlink($old_file);
                        unlink($new_file);
                    }
                } else if ($extension == 'jpg') {
                    if ($extension != $type) {
                        $temp_file = tempnam($temp_path, $extension);
                        file_put_contents($temp_file, $data);
                        $new_file = $temp_file . '.' . $type;
                        $old_file = $temp_file . '.' . $extension;
                        rename($temp_file, $old_file);

                        $process = proc_open($exe_path . "JPEG2PDF.exe $old_file -o $new_file", $spec, $pipes, $temp_path, null);
                        proc_close($process); // wait for process to finish and clean up

                        // pipes are closed automatically when you close process
                        $process = null;
                        $pipes = null;

                        $data = file_get_contents($new_file);

                        unlink($old_file);
                        unlink($new_file);
                    }
                } else {
                    // unknown extension type
                }
            }

            if ($is_range) { file_put_contents($cached_file, $data); }

            return $data;
        }
    }

}
