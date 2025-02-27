<?php

namespace Models\EP;

use Core\Model;

class PDOCFileData extends Model
{
    /**
     * @param array $filters
     * @param string $limit
     *
     * @return array
     */
    public function selectPDOCFileData(array $filters = [], string $limit = ''): array
    {

        // how many files have been waiting for more than one day in the other folder
        $ftp_h = ftp_connect('ftp.whittoncompanies.com') or die('Failed to connect!');
        ftp_pasv($ftp_h, true);
        $ftp_login = ftp_login($ftp_h, 'ProphetDOC|pd_ftpuser', 'we+are-WHITTON');

        $old_other_file_count = -999;
        $old_payroll_file_count = -999;
        $old_payroll_review_file_count = -999;
        $old_payroll_index_file_count = -999;

        if ($ftp_login) {
            $old_other_file_count = 0;
            $old_payroll_file_count = 0;
            $old_payroll_review_file_count = 0;
            $old_payroll_index_file_count = 0;

            // scan the OTHER directory
            $dir = 'BATCH/WHITTON/OTHER';
            if ($file_list = ftp_nlist($ftp_h, $dir)) {
                // All files
                foreach ($file_list as $file) {
                    $isdir = ftp_size($ftp_h, $file) == -1;
                    if (!$isdir) {
                        $file_ts = ftp_mdtm($ftp_h, $file);

                        $days_old = (time() - $file_ts) / 86400;
                        if ($days_old > 1) {
                            //echo date("m/d/Y H:i:s", $file_ts), '</br>';
                            $old_other_file_count++;
                        }
                    }
                }
            }

            // scan the PAYROLL directory
            $dir = 'BATCH/WHITTON/PAYROLL';
            if ($file_list = ftp_nlist($ftp_h, $dir)) {
                // All files
                foreach ($file_list as $file) {
                    $isdir = ftp_size($ftp_h, $file) == -1;
                    if (!$isdir) {
                        $file_ts = ftp_mdtm($ftp_h, $file);

                        $days_old = (time() - $file_ts) / 86400;
                        if ($days_old > 5) {
                            //echo date("m/d/Y H:i:s", $file_ts), '</br>';
                            $old_payroll_file_count++;
                        }

                        // file is not indexed
                        if (strpos($file, '+X}') > 0) {
                            $file_ts = ftp_mdtm($ftp_h, $file);

                            $days_old = (time() - $file_ts) / 86400;
                            if ($days_old > 0.5) {
                                //echo date("m/d/Y H:i:s", $file_ts), '</br>';
                                $old_payroll_index_file_count++;
                            }
                        }
                    }
                }
            }

            // scan the PAYROLL/REVIEW directory
            $dir = 'BATCH/WHITTON/PAYROLL/REVIEW';
            if ($file_list = ftp_nlist($ftp_h, $dir)) {
                // All files
                foreach ($file_list as $file) {
                    $isdir = ftp_size($ftp_h, $file) == -1;
                    if (!$isdir) {
                        $file_ts = ftp_mdtm($ftp_h, $file);

                        $days_old = (time() - $file_ts) / 86400;
                        if ($days_old > 10) {
                            //echo date("m/d/Y H:i:s", $file_ts), '</br>';
                            $old_payroll_review_file_count++;
                        }
                    }
                }
            }
        }
        ftp_close($ftp_h);

        $query[] = (object) [
            'OLD_OTHER_FILE_COUNT' => $old_other_file_count,
            'OLD_PAYROLL_FILE_COUNT' => $old_payroll_file_count,
            'OLD_PAYROLL_INDEX_FILE_COUNT' => $old_payroll_index_file_count,
            'OLD_PAYROLL_REVIEW_FILE_COUNT' => $old_payroll_review_file_count
        ];

        return [$query, 1];
    }
}
