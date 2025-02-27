<?php

namespace Traits\EP;

use Models\EP\DocumentData;
use Helpers\Date;

trait DocumentTrait
{
    /**
     * @param array $filters
     * @param string $limit
     *
     * @return array
     */
    private function getDocumentData(array $filters = [], string $limit = ''): array
    {
        $results = (new DocumentData())->selectDocumentData($filters, $limit);

        foreach ($results[0] as $i => $result) {
            $data['results'][$i] = $this->buildDocumentItem($result);
        }

        $data['num_results'] = $results[1];

        // Strict single result
        if ($limit === 'ROWS 1') {
            if (empty($results[0])) {
                return array(); // no data
            } else {
                return $data['results'][0];
            }
        }

        return $data;
    }

    /**
     * @param array $filters
     *
     * @return array|null
     */
    private function getDocumentItem(array $filters): ?array
    {
        return $this->getDocumentData($filters, 'ROWS 1');
    }

    /**
     * @param $result (std object)
     *
     * @return array
     */
    private function buildDocumentItem($result): array
    {
        $file_name = htmlentities(trim($result->DOCUMENT_FILENAME));
        $file_type = strtoupper(pathinfo($file_name)['extension']);
        $server_id = (int)$result->DOCUMENT_SERVERID;
        $prefix    = strtoupper(substr($file_name,0,4));

        if ($prefix == 'HTTP') {
            $doc_name_href = $file_name;
        } else {
            $type_add = '';
            if ($file_type == 'TIF') { $type_add = '&type=pdf'; }
            $doc_name_href = '<a href="/document/download/' . $server_id . '?file_name=' . sanitizeFilename($file_name) . $type_add . '&open_file=1" target="_blank">' . htmlentities(trim($result->DOCUMENT_NAME)) . '</a>';
        }


        $item = [
            'item' => [
                'item_id' => (int)$result->DOCUMENT_SEQNO,
                'doc_name' => htmlentities(trim($result->DOCUMENT_NAME)),
                'doc_name_href' => $doc_name_href,
                'file_name' => $file_name,
                'file_type' => $file_type,
                'doc_date' => Date::formatDate($result->DOCUMENT_MODIFIEDON, 'm/d/Y h:i:s A'),
                'server_id' => $server_id,
                'type_id' => (int)$result->DATA_TYPE,
                'data_id' => (int)$result->DATA_ID,
                'data_num' => (int)$result->DATA_NUM,
            ]
        ];

        return $item;
    }
}
