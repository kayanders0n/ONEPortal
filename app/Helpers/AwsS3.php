<?php

namespace Helpers;

class AwsS3
{
    /**
     * @param string $file
     * @param string $bucket
     *
     * @return string
     */
    public static function prepareS3URL(string $file = '', string $bucket = '', $password = ''): string
    {

        $access = self::getBucketAccess($bucket, $password);

        $file = rawurlencode($file);
        $file = str_replace('%2F', '/', $file);
        $path = $bucket .'/'. $file;

        $expires = strtotime('+1 day');

        $stringToSign = self::getStringToSign('GET', $expires, "/$path");
        $signature = self::encodeSignature($stringToSign, $access['SecretKey']);

        $url = "https://s3.amazonaws.com/$bucket/$file";
        $url .= '?AWSAccessKeyId='.$access['KeyId']
            .'&Expires='.$expires
            .'&Signature='.$signature;

        return $url;
    }

    private static function getStringToSign(string $request_type, string $expires, string $uri) {
        return "$request_type\n\n\n$expires\n$uri";
    }


    private static function encodeSignature(string $s, string $key) {
        $s = utf8_encode($s);
        $s = hash_hmac('sha1', $s, $key, true);
        $s = base64_encode($s);
        return urlencode($s);
    }

    private static function getBucketAccess(string $bucket, $password = ''): array
    {
        $access = array('KeyId'=>'invalid','SecretKey'=>'invalid');

        $s3_bucket_secret = APP_PATH . '/config/secret/' . $bucket . '.secret';
        if (is_file($s3_bucket_secret)) {
            include $s3_bucket_secret;
        }

        return $access;
    }
}
