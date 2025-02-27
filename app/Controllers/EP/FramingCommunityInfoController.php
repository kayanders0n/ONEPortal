<?php

namespace Controllers\EP;

use Core\Controller;
use Core\Logger;
use Core\View;
use Helpers\Request;
use Helpers\Response;
use Traits\EP\FramingCommunityInfoTrait;

class FramingCommunityInfoController extends Controller
{
    use FramingCommunityInfoTrait;

    public function list()
    {
        $data = $this->registry();

        //if (Request::isAjax()) {

            $data['filters'] = [
                'builder_id'   => $data['params']['builder_id'] ?? null
            ];

            $summarize = (bool) ($data['params']['summarize'] ?? null);

            // Get Framing Lot Inventory Data
            $results = $this->getFramingCommunityInfoData($data['filters'], $data['limit']);

            if ($summarize) {
                $builder_lot_count = [];

                foreach ($results['results'] as $key => $item) {
                    $item_id = (int)$item['builder']['id'];
                    if (!empty($builder_lot_count[$item_id])) {
                        $builder_lot_count[$item_id]['total_jobsites'] += $item['project']['jobsite_count'];
                        $builder_lot_count[$item_id]['total_jobs'] += $item['project']['job_count'];
                        $builder_lot_count[$item_id]['total_lots_remaining'] += $item['project']['lots_remaining'];

                    } else {
                        $summary = [
                            'builder_name' => $item['builder']['name'],
                            'total_jobsites' => $item['project']['jobsite_count'],
                            'total_jobs' => $item['project']['job_count'],
                            'total_lots_remaining' => $item['project']['lots_remaining'],
                        ];
                        $builder_lot_count[$item_id] = $summary;
                    }
                }
                $results['num_results'] = count($builder_lot_count);
                $results['results'] = $builder_lot_count;
            }

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
        //}
    }
}
