<?php

namespace Controllers\EP;

use Core\Controller;
use Core\View;
use Helpers\Request;
use Helpers\Response;
use Traits\EP\JobTrait;

class JobNotesController extends Controller
{
    use JobTrait;

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
            'slug' => 'jobs/notes',
            'title' => 'Job Data - ' . $job_num,
            'description' => 'Job Data',
            'author' => ''
        ];

        // Page Nav to add

        $data['page_nav']['Detail'] = '/jobs/' . $job_num;
        $data['page_nav']['Tickets'] = '/jobs/' . $job_num . '/tickets';
        $data['page_nav']['Documents'] = '/jobs/' . $job_num . '/documents';
        $data['page_nav']['Q/A'] = '/jobs/' . $job_num . '/qa';
        $data['page_nav']['Notes'] = '/jobs/' . $job_num . '/notes';
        $data['page_nav']['Changes'] = '/jobs/' . $job_num . '/changes';

        if ($job_num != 0) {
            $job_id = $this->validateJobNum($job_num);
            if ($job_id > 0) {
                $data['page']['job_id'] = $job_id;
                $data['page']['job_num'] = $job_num;
            } else {
                $header = 'Location: /jobs?error=Job Not Found!';
                View::addHeader($header);
                View::sendHeaders(); // redirect to page
                exit;
            }
        }

        View::renderTemplate('ep/jobs/notes/index', $data);
    }
}
