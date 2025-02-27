<?php

namespace Traits\EP;

use Models\EP\EstimatorReportCardData;
use Helpers\Date;

trait EstimatorReportCardTrait
{

    private $company_id = 0;
    private $estimator_id = 0;
    private $builders = array();

    /**
     * @param array $filters
     * @param string $limit
     *
     * @return array
     */
    private function getEstimatorReportCardData(array $filters = [], string $limit = ''): array
    {
        $this->company_id   = (int) ($filters['company_id'] ?? null);
        $this->estimator_id = (int) ($filters['estimator_id'] ?? null);

        $results = (new EstimatorReportCardData())->selectEstimatorReportCardData($filters, $limit);

        foreach ($results[0] as $i => $result) {
            $data['results'][$i] = $this->buildEstimatorReportCardItem($result);
        }

        $data['builders'] = $this->buildEstimatorReportCardBuilders();

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
    private function getEstimatorReportCardItem(array $filters): ?array
    {
        return $this->getEstimatorReportCardData($filters, 'ROWS 1');
    }


    /**
     * @param $result (std object)
     *
     * @return array
     */
    private function buildEstimatorReportCardItem($result): array
    {
        $status_name = htmlentities(trim($result->STATUS_NAME));

        // ----- JOB STATS -----
        $startcount = (int)$result->JOB_ACTIVITY;
        $jobsite_count = (int)$result->JOBSITE_COUNT;
        $job_count = (int)$result->JOB_COUNT;
        $jobactivity_style = '';

        if ($startcount > 0) {
            $backcolor = '#104d89';
            $fontcolor = 'white';
            if ($startcount >= 50) {
                // nothing use default
            } else if ($startcount >= 40) {
                $backcolor = '#1873cd';
            } else if ($startcount >= 30) {
                $backcolor = '#2e8ae6';
            } else if ($startcount >= 20) {
                $backcolor = '#4899ea';
            } else if ($startcount >= 10) {
                $backcolor = '#a4ccf4';
                $fontcolor = 'black';
            } else {
                $backcolor = '#d1e6fa';
                $fontcolor = 'black';
            }
            $jobactivity_style .=' <span style="float: right; background-color: '.$backcolor.'; color: '.$fontcolor.'; text-align: center; width: 30px;" title="Starts in last 45 days">'.$startcount.'</span>';
        }

        $jobstats_style = '';
        $jobstats_title = '';

        if ($jobsite_count<10){
            if (($status_name=='Job') && ($jobsite_count == 0)) {
                $jobstats_style = 'background-color: #cc0000; color: white';
                 $jobstats_title = 'Missing lot list!';
            } else {
                $jobstats_style = 'color: #cc0000';
                $jobstats_title = 'Suspect missing lot list!';
            }
        } else if (($jobsite_count > 15) && ($job_count == $jobsite_count)) {
            $jobstats_style = 'background-color: orange; color: white';
            $jobstats_title = 'Possible completed community!';
        }

        // ----- COST DATE -----

        $proposal_costdate = Date::formatDate($result->PROPOSAL_COSTDATE, 'm/d/Y');
        $proposal_costdate_style = '';
        $proposal_costdate_title = '';

        if ($proposal_costdate != '') {
            $date1 = strtotime(date('m/d/Y'));
            $date2 = strtotime($proposal_costdate);
            $diff = ($date1 - $date2) / 86400; // one day in seconds
            if ($this->company_id == CONCRETE_ENTITYID) {
                if ($diff > 365) {
                    $proposal_costdate_style = 'background-color: orange; color: white';
                    $proposal_costdate_title = 'More than 12 months since last re-cost!';
                }
            } else {
                if ($diff > 180) {
                    $proposal_costdate_style = 'background-color: orange; color: white';
                    $proposal_costdate_title = 'More than 6 months since last re-cost!';
                }
            }
        } else {
            $proposal_costdate = '&nbsp;';
            if ($status_name=='Job') {
                $proposal_costdate_style = 'background-color: #cc0000';
                $proposal_costdate_title = 'Missing cost date!';
            }
        }

        // ----- CONTRACT DATE -----

        $contractdate = Date::formatDate($result->PROPOSAL_CONTRACTDATE, 'm/d/Y');
        $proposal_contractdate_style = '';
        $proposal_contractdate_title = '';

        if ($contractdate != '') {
            $date1 = strtotime(date('m/d/Y'));
            $date2 = strtotime($contractdate);
            $diff = ($date1 - $date2) / 86400; // one day in seconds

            switch ($this->company_id) {
                case PLUMBING_ENTITYID:
                    // 12 months
                    if ($diff > 365) {
                        $proposal_contractdate_style = 'background-color: #cc0000; color: white';
                        $proposal_contractdate_title = 'More than 12 months since last price change!';
                    }
                    break;
                case CONCRETE_ENTITYID:
                    // 12 months
                    if ($diff > 365) {
                        $proposal_contractdate_style = 'background-color: #cc0000; color: white';
                        $proposal_contractdate_title = 'More than 12 months since last price change!';
                    }
                    break;
                case FRAMING_ENTITYID:
                    // 4 months
                    if ($diff > 120) {
                        $proposal_contractdate_style = 'background-color: #cc0000; color: white';
                        $proposal_contractdate_title = 'More than 4 months since last price change!';
                    }
                    break;
                case DOORTRIM_ENTITYID:
                    // 12 months
                    if ($diff > 365) {
                        $proposal_contractdate_style = 'background-color: #cc0000; color: white';
                        $proposal_contractdate_title = 'More than 12 months since last price change!';
                    }
                    break;
            }
        } else {
            $contractdate = '&nbsp;';
            if ($status_name=='Job') { // Proposal is required
                $proposal_contractdate_style = 'background-color: #cc0000';
                $proposal_contractdate_title = 'Missing last price change date!';
            }
        }

        // ----- CONTRACT DATE ICONS -----

        $contractcount = (int)$result->SIGNED_CONTRACT_COUNT;
        $signedcontracticon = '';

        if ($contractcount == 0) {
            $signedcontracticon = ' <i class="fas fa-file-signature" style="color: orange; float: right; font-size: 1.5em; margin-right: 2px;" title="Missing signed contracts!"></i>';
            // background color white on image to make sure they can see it on red pricing dates
        }

        $price_margin = round((float)$result->PRICE_MARGIN * 100, 2);
        $pricemarginicon = '';

        if ($price_margin <= 3) {
            $pricemarginicon = ' <i class="fas fa-exclamation-circle" style="float: right; color: #cc0000; font-size: 1.5em; margin-right: 2px;" title="Margins are in danger!"></i>';
            // background color white on image to make sure they can see it on red pricing dates
        }

        $priceincrease = 0;

        switch ($this->company_id) {
            case PLUMBING_ENTITYID:
                $priceincrease = (int)$result->USERINT01;
                break;
            case CONCRETE_ENTITYID:
                $priceincrease = (int)$result->USERINT02;
                break;
            case FRAMING_ENTITYID:
                $priceincrease = (int)$result->USERINT03;
                break;
            case DOORTRIM_ENTITYID:
                //nothing yet
                break;
        }

        if ($priceincrease == 1) {
            $priceincreaseicon = ' <i id="increase-icon" class="fas fa-arrow-alt-circle-up" style="float: right; color: forestgreen; font-size: 1.5em; margin-right: 2px;" title="Price Increase Done!"></i>';
        } else if ($priceincrease == 2) {
            $priceincreaseicon = ' <i id="increase-icon" class="far fa-arrow-alt-circle-up" style="float: right; color: dimgray; font-size: 1.5em; margin-right: 2px;" title="Price Increase Pending..."></i>';
        } else {
            $priceincreaseicon = ' <i id="increase-icon"></i>';
        }
        $proposal_contractdate_flags = $priceincreaseicon.$pricemarginicon.$signedcontracticon;

        // ----- OPTIONS DATE -----

        $options_costdate = Date::formatDate($result->OPTIONS_COSTDATE, 'm/d/Y');
        $options_costdate_style = '';
        $options_costdate_title = '';

        if ($options_costdate != '') {
            $date1 = strtotime(date('m/d/Y'));
            $date2 = strtotime($options_costdate);
            $diff = ($date1 - $date2) / 86400; // one day in seconds
            if ($this->company_id == CONCRETE_ENTITYID) {
                if ($diff > 365) {
                    $options_costdate_style = 'background-color: orange; color: white';
                    $options_costdate_title = 'More than 12 months since last option cost date!';
                }
            } else {
                if ($diff > 180) {
                    $options_costdate_style = 'background-color: orange; color: white';
                    $options_costdate_title = 'More than 6 months since last option cost date!';
                }
            }
        } else {
            $options_costdate = '&nbsp;';
            if ($status_name=='Job') {
                $options_costdate_style = 'background-color: #cc0000';
                $options_costdate_title = 'Missing option cost date!';
            }
        }

        // ----- OPTIONS DATE ICON -----

        $options_markup_count = (int)$result->OPTIONS_MARKUP_COUNT;
        $optionmargincounticon = '';

        if ($options_markup_count > 0) {
            $optionmargincounticon = ' <i class="fas fa-exclamation-circle" style="float: right; color: #cc0000; font-size: 1.5em;" title="Option Margins are in danger! ('.$options_markup_count.')"></i>';
            // background color white on image to make sure they can see it on red option dates
        }

        $options_costdate_flags = $optionmargincounticon;

        // ----- BILLING ADJ STYLE -----

        $billing_adj = (int)$result->BILLING_ADJ;

        $billing_adj_style = '';
        if ($billing_adj > 0) {
            if ($billing_adj >= 50) {
                $billing_adj_style = 'background-color: #cc0000; color: white';
            } else if ($billing_adj >= 40) {
                $billing_adj_style = 'background-color: #de5959; color: white';
            } else if ($billing_adj >= 30) {
                $billing_adj_style = 'background-color: #e68080; color: white';
            } else if ($billing_adj >= 20) {
                $billing_adj_style = 'background-color: #eda6a6';
            } else if ($billing_adj >= 10) {
                $billing_adj_style = 'background-color: #f5cccc';
            } else {
                $billing_adj_style = 'background-color: #f7d9d9';
            }
        }

        // ----- PO REVIEW STYLE -----

        $po_review_needed = (int)$result->PO_REVIEW_NEEDED;

        $po_review_style = '';
        if ($po_review_needed > 0) {
            if ($po_review_needed >= 11) {
                $po_review_style = 'background-color: #cc0000; color: white';
            } else if ($po_review_needed >= 9) {
                $po_review_style = 'background-color: #de5959; color: white';
            } else if ($po_review_needed >= 7) {
                $po_review_style = 'background-color: #e68080; color: white';
            } else if ($po_review_needed >= 5) {
                $po_review_style = 'background-color: #eda6a6';
            } else if ($po_review_needed >= 3) {
                $po_review_style = 'background-color: #f5cccc';
            } else {
                $po_review_style = 'background-color: #f7d9d9';
            }
        }

        // ----- STYLES -----

        $styles_array = array(
            'jobstats_style' => $jobstats_style,
            'jobstats_title' => $jobstats_title,
            'proposal_costdate_style' => $proposal_costdate_style,
            'proposal_costdate_title' => $proposal_costdate_title,
            'proposal_contractdate_style' => $proposal_contractdate_style,
            'proposal_contractdate_title' => $proposal_contractdate_title,
            'options_costdate_style' => $options_costdate_style,
            'options_costdate_title' => $options_costdate_title,
            'billing_adj_style' => $billing_adj_style,
            'po_review_style' => $po_review_style
        );

        // ----- COMMUNITY LINKED ID -----

        $project_num = (int)$result->PROJECT_NUM;
        $plu_linkproject_num = (int)$result->PLU_LINKPROJECT_NUM;
        $con_linkproject_num = (int)$result->CON_LINKPROJECT_NUM;
        $fra_linkproject_num = (int)$result->FRA_LINKPROJECT_NUM;
        $linkedproject = '';

        switch ($this->company_id) {
            case PLUMBING_ENTITYID:
                if ($plu_linkproject_num != 0) {
                    $linkedproject = ' <span style="font-size: 0.75em;" title="Linked to Project# ' . $plu_linkproject_num . '">(' . $plu_linkproject_num . ')</span> ';
                }
                break;
            case CONCRETE_ENTITYID:
                if ($con_linkproject_num != 0) {
                    $linkedproject = ' <span style="font-size: 0.75em;" title="Linked to Project# ' . $con_linkproject_num . '">(' . $con_linkproject_num . ')</span> ';
                }
                break;
            case FRAMING_ENTITYID:
                if ($fra_linkproject_num != 0) {
                    $linkedproject = ' <span style="font-size: 0.75em;" title="Linked to Project# ' . $fra_linkproject_num . '">(' . $fra_linkproject_num . ')</span> ';
                }
                break;
            case DOORTRIM_ENTITYID:
                //need to add to data
                break;
        }

        // ----- PROJECT NAME ICONS -----

        $project_name = htmlentities(trim($result->PROJECT_NAME));

        $builder_name = htmlentities(trim($result->BUILDER_NAME));
        $project_glprefix = htmlentities(trim($result->PROJECT_GLPREFIX));
        $tucsonicon = '';

        $istucson = (strpos(strtoupper($builder_name), 'TUCSON') > 0);
        if ($istucson && ($project_glprefix != '00020')) {
            $tucsonicon = ' <i class="fas fa-map-marker-alt" style="float: right; color: mediumblue; font-size: 1.5em; margin-right: 2px;" title="Missing Tucson Coding!"></i>';
        }

        $project_latitude = htmlentities(trim($result->PROJECT_LATITUDE));
        $project_longitude = htmlentities(trim($result->PROJECT_LONGITUDE));
        $gpsicon = '';

        if (($project_latitude=='') || ($project_longitude=='')) {
            $gpsicon = ' <i class="fas fa-globe" style="float: right; color: lightslategray; font-size: 1.5em; margin-right: 2px;" title="Missing GPS coordinates!"></i>';
        }

        $project_address1 = htmlentities(trim($result->PROJECT_ADDRESS1));
        $addressicon = '';

        if ($project_address1=='') {
            $addressicon = ' <i class="fas fa-map-marked-alt" style="float: right; color: salmon; font-size: 1.5em; margin-right: 2px;" title="Missing project address!"></i>';
        }

        $project_crossroads = htmlentities(trim($result->PROJECT_CROSSROADS));
        $crossroadsicon = '';

        if ($project_crossroads=='') {
            $crossroadsicon = ' <i class="fas fa-map-signs" style="float: right; color: peru; font-size: 1.5em; margin-right: 2px;" title="Missing Crossroads!"></i>';
        }

        $projectestvalue = 0;
        switch ($this->company_id) {
            case PLUMBING_ENTITYID:
                $projectestvalue = (float)$result->PLU_EST_VALUE;
                break;
            case CONCRETE_ENTITYID:
                $projectestvalue = (float)$result->CON_EST_VALUE;
                break;
            case FRAMING_ENTITYID:
                $projectestvalue = (float)$result->FRA_EST_VALUE;
                break;
            case DOORTRIM_ENTITYID:
                //need to add to data
                break;
        }
        $builder_isblanketprelien = (int)$result->BUILDER_ISBLANKETPRELIEN;
        $projectestvalueicon = '';

        if (($projectestvalue < 1000) && ($status_name=='Job') && ($builder_isblanketprelien == 1)) {
            $projectestvalueicon = ' <i class="fas fa-search-dollar" style="float: right; color: springgreen; font-size: 1.5em; margin-right: 2px;" title="Project estimated value is missing!"></i>';
        }

        $rb_fldcon_count = (int)$result->RB_FLDCON_COUNT;
        $rbfldcontractsicon = '';

        if (($this->company_id == PLUMBING_ENTITYID) && ($rb_fldcon_count == 0)) {
            $rbfldcontractsicon = ' <i class="far fa-clipboard" style="float: right; color: mediumpurple; font-size: 1.5em; margin-right: 2px;" title="Missing Field Contracts!"></i>';
            // background color white on image to make sure they can see it on red pricing dates
        }

        $rb_plans_count = (int)$result->RB_PLANS_COUNT;
        $rbplansicon = '';

        if ($rb_plans_count == 0) {
            $rbplansicon = ' <i class="fas fa-home" style="float: right; color: teal; font-size: 1.5em; margin-right: 2px;" title="Missing Redbook Plans!"></i>';
            // background color white on image to make sure they can see it on red pricing dates
        }

        $project_changereq_count = (int)$result->PROJECT_CHANGEREQ_COUNT;
        $changereqicon = '';

        if ($project_changereq_count != 0) {
            $changereqicon = ' <i class="fas fa-exclamation-circle" style="float: right; color: dodgerblue; font-size: 1.5em; margin-right: 2px;" title="Uncompleted Change Requests!"></i>';
        }

        $project_note = nl2br(htmlentities($result->PROJECT_NOTE));
        $projectnoteicon = '';

        if ($project_note != '') {
            $projectnoteicon = ' <i class="far fa-sticky-note" style="float: right; color: goldenrod; font-size: 1.5em; margin-right: 2px;" title="Project has notes."></i>';
        }

        $project_flags = $tucsonicon.$gpsicon.$addressicon.$crossroadsicon.$projectestvalueicon.$rbfldcontractsicon.$rbplansicon.$changereqicon.$projectnoteicon;

        $estimator_id = (int)$result->ESTIMATOR_SEQNO;

        // ----- ITEMS -----

        $item = [
            'item' => [
                'item_id' => (int)$result->PROJECT_SEQNO,
                'project_num' => $project_num,
                'project_name' => $project_name,
                'project_flags' => $project_flags,
                'project_linked' => $linkedproject,
                'builder_id' => (int)$result->BUILDER_SEQNO,
                'builder_name' => $builder_name,
                'estimator_id' => $estimator_id,
                'estimator_name' => htmlentities(trim($result->ESTIMATOR_NAME)),
                'jobsite_count' => $jobsite_count,
                'job_count' => $job_count,
                'options_costdate' => $options_costdate,
                'proposal_costdate' => $proposal_costdate,
                'proposal_contractdate' => $contractdate,
                'proposal_price_increase' => $priceincrease,
                'proposal_contractdate_flags' => $proposal_contractdate_flags,
                'options_costdate_flags' => $options_costdate_flags,
                //'project_changereq_count' => $project_changereq_count,
                //'price_margin' => $price_margin,
                'startcount' => $startcount,
                //'options_markup_count' => $options_markup_count,      // included in options_costdate
                //'rb_fldcon_count' => $rb_fldcon_count,
                //'rb_plans_count' => $rb_plans_count,
                //'signed_contract_count' => $contractcount,
                'billing_adj' => $billing_adj,
                'po_review_needed' => $po_review_needed,
                'linkedproject' => $linkedproject,
                //'builder_isblanketprelien' => $builder_isblanketprelien,
                //'projectestvalue' => $projectestvalue,
                'project_crossroads' => $project_crossroads,
                'project_address1' => $project_address1,
                'project_zip' => htmlentities(trim($result->PROJECT_ZIP)),
                'project_latitude' => $project_latitude,
                'project_longitude' => $project_longitude,
                //'project_glprefix' => $project_glprefix,
                'created_on' => Date::formatDate($result->PROJECT_CREATEDON, 'm/d/Y h:i:s A'),
                'modified_on' => Date::formatDate($result->PROJECT_MODIFIEDON, 'm/d/Y h:i:s A'),
                'created_by' => htmlentities(trim($result->PROJECT_CREATEDBY)),
                'modified_by' => htmlentities(trim($result->PROJECT_MODIFIEDBY)),
                'project_note' => $project_note,
                //'status_name' => $status_name,
                //'userint01' => $userint01,
                //'userint02' => $userint02,
                //'userint03' => $userint03,
                'jobactivity_style' => $jobactivity_style,
                'styles_array' => $styles_array
            ]
        ];

        $add_builder = false;
        if ($this->estimator_id) {
            if ($estimator_id == $this->estimator_id) {
                $add_builder = true;
            }
        } else {
            $add_builder = true;
        }
        if ($add_builder) {
            $this->builders[(int)$result->BUILDER_SEQNO] = htmlentities(trim($result->BUILDER_NAME));
        }


        return $item;
    }

    private function buildEstimatorReportCardBuilders(): array
    {
        $list = array(); asort($this->builders);
        foreach ($this->builders as $key=>$name) {
            array_push($list, ['builder'=> ['item_id'=>$key, 'name'=>$name]]);
        }

        return $list;
    }
}
