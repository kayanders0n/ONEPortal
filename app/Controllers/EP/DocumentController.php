<?php

namespace Controllers\EP;

use Core\Controller;
use Core\Logger;
use Core\View;
use Exception;
use Helpers\Request;
use Helpers\Response;
use Helpers\Document;
use Traits\EP\DocumentTrait;

class DocumentController extends Controller
{

    public function download(int $server_id)
    {
        $data = $this->registry();

        $file_name = $data['params']['file_name'] ?? null;
        $type = strtolower($data['params']['type'] ?? null);
        $is_zip = ((int) ($data['params']['is_zip'] ?? null) == 1);
        $open_file = ((int) ($data['params']['open_file'] ?? null) == 1);

        try {

            $ranges = null; $is_range = false;

            if ($_SERVER['REQUEST_METHOD']=='GET' && isset($_SERVER['HTTP_RANGE']) && $range = stristr(trim($_SERVER['HTTP_RANGE']),'bytes=')) {
                $range = substr($range,6);
                $boundary = 'g45d64df96bmdf4sdgh45hf5'; //set a random boundary
                $ranges = explode(',', $range);
                $is_range = true;

                Response::addStatus(206);
                Response::addHeader('Accept-Ranges: bytes');
            }

            if ($server_id && $file_name) {
                $document_data = Document::getServerDocument($server_id, $file_name, $type, $is_zip, $is_range);

                if ($type) { $file_name = Document::removeExtension($file_name) . '.' . $type; }
                $file_size =  strlen($document_data);

                if ($file_size == 0) {
                    throw new Exception('Unable to get document from server or cache');
                }
                $extension = Document::getExtension($file_name);
                $content_type = Document::getFileMimeType($extension);
                if ($type) { $content_type = Document::getFileMimeType($type); }

                if ($open_file) {
                    Response::addHeader("Content-Disposition: filename=\"$file_name\"");
                } else {
                    Response::addHeader("Content-Disposition: attachment; filename=\"$file_name\"");
                }

                ob_clean();
                if (!$ranges) {
                    Response::sendHeaders($content_type);
                    echo $document_data;
                } else {
                    if (count($ranges) > 1) {
                        //compute content length
                        $content_length = 0;
                        foreach ($ranges as $range){
                            self::setDocumentByteRange($range, $file_size, $first, $last);
                            $content_length += strlen("\r\n--$boundary\r\n");
                            $content_length += strlen("Content-type: $content_type\r\n");
                            $content_length += strlen("Content-range: bytes $first - $last / $file_size\r\n\r\n");
                            $content_length += $last - $first + 1;
                        }
                        $content_length += strlen("\r\n--$boundary--\r\n");

                        Response::addHeader("Content-Length: $content_length");
                        Response::sendHeaders("multipart/x-byteranges; boundary=$boundary");

                        //output the content for each range requested
                        foreach ($ranges as $range){
                            self::setDocumentByteRange($range, $file_size, $first, $last);
                            echo "\r\n--$boundary\r\n";
                            echo "Content-type: $content_type\r\n";
                            echo "Content-range: bytes $first - $last / $file_size\r\n\r\n";
                            echo substr($document_data, $first, $last - $first +1);
                        }
                        echo "\r\n--$boundary--\r\n";

                    } else {
                        /* A single range is requested. */
                        $range = $ranges[0];
                        self::setDocumentByteRange($range, $file_size, $first, $last);
                        Response::addHeader("Content-Length: ".($last - $first +1));
                        Response::addHeader("Content-Range: bytes $first - $last / $file_size");
                        Response::sendHeaders($content_type);
                        echo substr($document_data, $first, $last - $first +1);
                    }
                }
                ob_flush();
                exit;
            } else {
                throw new Exception('Missing server_id or file_name');
            }

        } catch (Exception $e) {
            logger(__METHOD__)->error('Document Download Error', ['error'=>$e]);
        }
    }


    private function setDocumentByteRange($range, $file_size, &$first, &$last) {
        /*
        Sets the first and last bytes of a range, given a range expressed as a string
        and the size of the file.

        If the end of the range is not specified, or the end of the range is greater
        than the length of the file, $last is set as the end of the file.

        If the begining of the range is not specified, the meaning of the value after
        the dash is "get the last n bytes of the file".

        If $first is greater than $last, the range is not satisfiable, and we should
        return a response with a status of 416 (Requested range not satisfiable).

        Examples:
        $range='0-499', $filesize=1000 => $first=0, $last=499 .
        $range='500-', $filesize=1000 => $first=500, $last=999 .
        $range='500-1200', $filesize=1000 => $first=500, $last=999 .
        $range='-200', $filesize=1000 => $first=800, $last=999 .

        */
        $dash = strpos($range,'-');
        $first = trim(substr($range,0,$dash));
        $last = trim(substr($range,$dash+1));
        if ($first=='') {
            //suffix byte range: gets last n bytes
            $suffix = $last;
            $last = $file_size-1;
            $first= $file_size - $suffix;
            if ($first < 0) $first=0;
        } else {
            if ($last == '' || $last > $file_size -1) $last = $file_size -1;
        }
        if($first > $last){
            //unsatisfiable range
            Response::addHeader('Status: 416 Requested range not satisfiable');
            Response::addHeader("Content-Range: */$file_size");
            exit;
        }
    }


    use DocumentTrait;

    public function list()
    {
        $data = $this->registry();

        if (Request::isAjax()) {

            $data['filters'] = [
                'item_id'  => $data['params']['item_id'] ?? null,
                'type_id'  => $data['params']['type_id'] ?? null,
                'data_id'  => $data['params']['data_id'] ?? null,
                'data_num' => $data['params']['data_num'] ?? null,
                'data_ids' => $data['params']['data_ids'] ?? null
            ];

            // Get Job Documents
            $results = $this->getDocumentData($data['filters'], $data['limit']);

            // Check Results
            if (empty($results['num_results'])) {
                Response::addStatus(200);
                $data = ['message' => 'No results found'];
            } else {

                Response::addStatus(200);
                $data = [
                    'message'     => Response::$status[200],
                    'results'     => $results['results'],
                    'num_results' => $results['num_results']
                ];
            }

            Response::sendHeaders();
            Response::json($data);

        }
    }

    use DocumentTrait;

    public function show(int $item_id)
    {
        if (Request::isAjax()) {

            if (empty($item_id)) {
                Response::addStatus(400);
                $data = ['message' => Response::$status[400]];
            } else {

                // Get Jobs Documents Data
                $result = $this->getDocumentItem(['item_id' => $item_id]);

                // Check Results
                if (empty($result)) {
                    Response::addStatus(200);
                    $data = ['message' => 'No results found'];
                } else {

                    Response::addStatus(200);
                    $data = [
                        'message' => Response::$status[200],
                        'result'  => $result
                    ];
                }
            }

            Response::sendHeaders();
            Response::json($data);
        }
    }

}
