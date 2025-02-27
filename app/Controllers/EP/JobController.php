<?php

namespace Controllers\EP;

use Core\Controller;
use Core\View;
use Helpers\Request;
use Helpers\Response;
use Traits\EP\JobTrait;
use Models\EP\JobData;

class JobController extends Controller
{
    use JobTrait;

    public function show(int $item_id)
    {
        if (Request::isAjax()) {

            if (empty($item_id)) {
                Response::addStatus(400);
                $data = ['message' => Response::$status[400]];
            } else {

                // Get Jobs Data
                $result = $this->getJobsItem(['item_id' => $item_id]);

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

    public function find()
    {
        $data = $this->registry();

        try {
            // Do the thing
        } catch (\Exception $e) {
            $data['message'] = 'No results found';
        }

        // Page Object
        $data['page'] = [
            'slug'        => 'jobs/find',
            'title'       => 'Find Job',
            'description' => 'Find a Job',
            'author'      => ''
        ];

        View::renderTemplate('ep/jobs/find/index', $data);
    }

    public function lookup()
    {
        $data = $this->registry();

        try {
            // Do the thing
            $request = Request::filterRequest($_POST, true);

            $job_num = 0;
            if ($job_num == 0) {
                if (isset($request['job_num'])) {
                    $job_num = (int) $request['job_num'];
                }
            }

            $header = 'Location: /jobs?error=Job Not Found!';
            if ($job_num != 0) {
                if ($this->validateJobNum($job_num)) {
                    $header = 'Location: /jobs/' . $job_num;
                }
            }
            View::addHeader($header);
            View::sendHeaders(); // redirect to page
        } catch (\Exception $e) {
            $data['message'] = 'No results found';
        }
    }

    public function index($job_num)
    {
        $data = $this->registry();

        try {
            // Do the thing
        } catch (\Exception $e) {
            $data['message'] = 'No results found';
        }

        // Page Object
        $data['page'] = [
            'slug'        => 'jobs',
            'title'       => 'Job Data - ' . $job_num,
            'description' => 'Job Data',
            'author'      => ''
        ];

        // Page Nav to add

        $data['page_nav']['Detail']    = '/jobs/' . $job_num;
        $data['page_nav']['Tickets']   = '/jobs/' . $job_num . '/tickets';
        $data['page_nav']['Documents'] = '/jobs/' . $job_num . '/documents';
        $data['page_nav']['Q/A']       = '/jobs/' . $job_num . '/qa';
        $data['page_nav']['Notes']     = '/jobs/' . $job_num . '/notes';
        $data['page_nav']['Changes']   = '/jobs/' . $job_num . '/changes';

        if ($job_num != 0) {
            $job_id = $this->validateJobNum($job_num);
            if ($job_id > 0) {
                $data['page']['job_id']  = $job_id;
                $data['page']['job_num'] = $job_num;
            } else {
                $header = 'Location: /jobs?error=Job Not Found!';
                View::addHeader($header);
                View::sendHeaders(); // redirect to page
                exit;
            }
        }

        View::renderTemplate('ep/jobs/index', $data);
    }

    public function update(int $item_id)
    {
        if (Request::isAjax()) {
            $request = Request::filterRequest($_POST, true);

            if (empty($item_id)) {
                Response::addStatus(400);
                $data = ['message' => Response::$status[400]];
            } else {

                $request['item_id'] = $item_id;

                // Update Job Data
                $result = (new JobData())->updateJobData($request);

                // Check Results
                if (empty($result)) {
                    Response::addStatus(200);
                    $data = ['message' => 'Did not update'];
                } else {

                    Response::addStatus(200);
                    $data = [
                        'message' => Response::$status[200],
                        'updated' => (bool) $result
                    ];
                }
            }

            Response::sendHeaders();
            Response::json($data);
        }
    }
}
