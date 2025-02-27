<?php

namespace Controllers\EP;

use Core\Controller;
use Core\Logger;
use Core\View;
use Helpers\AwsS3;

class AwsS3Controller extends Controller
{

    public function url(string $bucket)
    {
        $data = $this->registry(false);

        $file = $data['params']['file'];
        $pw   = $data['params']['pw'];

        $s3_url  = AwsS3::prepareS3URL($file, 'whittoncompanies.com.' . $bucket, $pw);
        $headers = ['Location: ' . $s3_url];
        View::addHeaders($headers);
        View::sendHeaders();
    }
}
