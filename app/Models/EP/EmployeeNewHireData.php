<?php

namespace Models\EP;

use Core\Model;
use FPDM;
use Helpers\Mail;
use Helpers\Numeric;
use Helpers\Date;
use Helpers\Location;

class EmployeeNewHireData extends Model
{
    /**
     * @param string $json_data
     *
     * @return array
     */
    public function submitNewHireData(string $json_data): array
    {

        $data = json_decode($json_data);

        //normalize the data
        $data->first_name     = ucwords(strtolower($data->first_name));
        $data->middle_initial = strtoupper($data->middle_initial);
        $data->last_name      = ucwords(strtolower($data->last_name));

        $data->address = ucwords(strtolower($data->address));
        $data->city    = ucwords(strtolower($data->city));
        $data->state   = strtoupper($data->state);

        $data->ssn    = Numeric::formatSSN($data->ssn);
        $data->ssn_nd = Numeric::formatSSN($data->ssn, false); // no dashes

        $data->home_phone = Numeric::formatPhone($data->home_phone);
        $data->cell_phone = Numeric::formatPhone($data->cell_phone);

        $data->date_of_birth    = Date::formatDate($data->date_of_birth,'M-d-Y');
        $data->date_of_birth_af = Date::formatDate($data->date_of_birth,'m/d/Y'); // alternate format

        $data->drivers_license        = strtoupper($data->drivers_license);
        $data->drivers_license_state  = strtoupper($data->drivers_license_state);
        $data->drivers_license_expire = Date::formatDate($data->drivers_license_expire,'M-d-Y');

        $pdf = new FPDM($_SERVER['DOCUMENT_ROOT'].'/assets/pdf/employee-new-hire.pdf');
        $pdf->support = 'pdftk';

        $fields = array(
            // A4
            'A4_Employee_Name'             => $data->first_name . ' ' . $data->middle_initial . ' ' . $data->last_name,
            'A4_Employee_SSN'              => $data->ssn,
            'A4_Employee_Address'          => $data->address,
            'A4_Employee_City'             => $data->city,
            'A4_Employee_State'            => $data->state,
            'A4_Employee_ZipCode'          => $data->zipcode,
            'A4_Form_Date'                 => date('M-d-Y'),
            // application
            'App_Employee_Name'             => $data->first_name . ' ' . $data->middle_initial . ' ' . $data->last_name,
            'App_Employee_SSN'              => $data->ssn,
            'App_Employee_Address'          => $data->address,
            'App_Employee_CityStateZipCode' => $data->city . ', ' . $data->state . '  ' . $data->zipcode,
            'App_Employee_HomePhone'        => $data->home_phone,
            'App_Employee_CellPhone'        => $data->cell_phone,
            'App_Employee_BirthDate'        => $data->date_of_birth,
            'App_Employee_DriversLicenseNum'        => $data->drivers_license,
            'App_Employee_DriversLicenseState'      => $data->drivers_license_state,
            'App_Employee_DriversLicenseExpiration' => $data->drivers_license_expire,
            'App_Form_Date'                => date('M-d-Y'),
            // Cellphone Driving
            'CD_Employee_Name'             => $data->first_name . ' ' . $data->middle_initial . ' ' . $data->last_name,
            'CD_Form_Date'                 => date('M-d-Y'),
            // Concentra
            'CC_Employee_Name'             => $data->first_name . ' ' . $data->middle_initial . ' ' . $data->last_name,
            'CC_Employee_SSN'              => $data->ssn,
            'CC_Employee_BirthDate'        => $data->date_of_birth,
            'CC_Employer_Name'             => 'Whitton Plumbing, Inc',
            'CC_AuthorizedBy_Name'         => 'Penny Calano',
            'CC_AuthorizedBy_Title'        => 'HR',
            'CC_AuthorizedBy_Phone'        => '(480) 892-6159',
            'CC_Form_Date'                 => date('M-d-Y'),
            // CV19
            'CV_Employee_Name'             => $data->first_name . ' ' . $data->middle_initial . ' ' . $data->last_name,
            'CV_Form_Date'                 => date('M-d-Y'),
            // Dress Code
            'DC_Employee_Name'             => $data->first_name . ' ' . $data->middle_initial . ' ' . $data->last_name,
            'DC_Form_Date'                 => date('M-d-Y'),
            // Driver Form
            'DF_Employee_Name'             => $data->first_name . ' ' . $data->middle_initial . ' ' . $data->last_name,
            'DF_Form_Date'                 => date('M-d-Y'),
            // Equipment Policy
            'EQP_Employee_Name'            => $data->first_name . ' ' . $data->middle_initial . ' ' . $data->last_name,
            'EQP_Form_Date'                => date('M-d-Y'),
            // Personal Tools Policy
            'PTP_Employee_Name'            => $data->first_name . ' ' . $data->middle_initial . ' ' . $data->last_name,
            'PTP_Form_Date'                => date('M-d-Y'),

            // Ethics Policy
            'ETP_Employee_Name'            => $data->first_name . ' ' . $data->middle_initial . ' ' . $data->last_name,
            'ETP_Form_Date'                => date('M-d-Y'),
            // Ethnicity
            'ETH_Employee_Name'            => $data->first_name . ' ' . $data->middle_initial . ' ' . $data->last_name,
            'ETH_Form_Date'                => date('M-d-Y'),
            // Flex Rate
            'FR_Employee_Name'             => $data->first_name . ' ' . $data->middle_initial . ' ' . $data->last_name,
            'FR_Form_Date'                 => date('M-d-Y'),
            // I-9
            'I9_Employee_LastName'         => $data->last_name,
            'I9_Employee_FirstName'        => $data->first_name,
            'I9_Employee_MiddleInitial'    => $data->middle_initial,
            'I9_Employee_LastName_2'       => $data->last_name,
            'I9_Employee_FirstName_2'      => $data->first_name,
            'I9_Employee_MiddleInitial_2'  => $data->middle_initial,
            'I9_Employee_Address'          => $data->address,
            'I9_Employee_City'             => $data->city,
            'I9_Employee_State'            => $data->state,
            'I9_Employee_ZipCode'          => $data->zipcode,
            'I9_Employee_BirthDate'        => $data->date_of_birth_af,
            'I9_Employee_SSN_1_3'          => substr($data->ssn_nd, 0, 3),
            'I9_Employee_SSN_4_5'          => substr($data->ssn_nd, 3, 2),
            'I9_Employee_SSN_6_9'          => substr($data->ssn_nd, 5, 4),
            //'I9_Employee_Email'            => $data->email,
            'I9_Employee_Phone'            => $data->home_phone,
            'I9_Form_Date'                 => date('m/d/Y'),
            // Immigration Compliance
            'IC_Employee_Name'             => $data->first_name . ' ' . $data->middle_initial . ' ' . $data->last_name,
            'IC_Form_Date'                 => date('M-d-Y'),
            // Orientation
            'OR_Employee_Name_Eng'         => $data->first_name . ' ' . $data->middle_initial . ' ' . $data->last_name,
            'OR_Form_Date_Eng'             => date('M-d-Y'),
            'OR_Employee_Name_Spn'         => $data->first_name . ' ' . $data->middle_initial . ' ' . $data->last_name,
            'OR_Form_Date_Spn'             => date('M-d-Y'),
            // Policy Anti Harassment
            'AH_Employee_Name'             => $data->first_name . ' ' . $data->middle_initial . ' ' . $data->last_name,
            'AH_Form_Date'                 => date('M-d-Y'),
            // Service Back Charges
            'BC_Employee_Name_Eng'         => $data->first_name . ' ' . $data->middle_initial . ' ' . $data->last_name,
            'BC_Form_Date_Eng'             => date('M-d-Y'),
            'BC_Employee_Name_Spn'         => $data->first_name . ' ' . $data->middle_initial . ' ' . $data->last_name,
            'BC_Form_Date_Spn'             => date('M-d-Y'),
            // Social Media
            'SM_Employee_Name'             => $data->first_name . ' ' . $data->middle_initial . ' ' . $data->last_name,
            'SM_Form_Date'                 => date('M-d-Y'),
            // Vehicle Gate time
            'VG_Employee_Name'             => $data->first_name . ' ' . $data->middle_initial . ' ' . $data->last_name,
            'VG_Form_Date'                 => date('M-d-Y'),
            'VG_Form_Date_2'               => date('M-d-Y'),
            // W-4
            'W4_Employee_FirstName_MI'     => $data->first_name . ' ' . $data->middle_initial,
            'W4_Employee_LastName'         => $data->last_name,
            'W4_Employee_SSN'              => $data->ssn,
            'W4_Employee_Address'          => $data->address,
            'W4_Employee_CityStateZipCode' => $data->city . ', ' . $data->state . '  ' . $data->zipcode,
            'W4_Form_Date'                 => date('M-d-Y'),
            // W-9
            'W9_Employee_Name'             => $data->first_name . ' ' . $data->middle_initial . ' ' . $data->last_name,
            'W9_Employee_Address'          => $data->address,
            'W9_Employee_CityStateZipCode' => $data->city . ', ' . $data->state . '  ' . $data->zipcode,
            'W9_Employee_SSN_1_3'          => substr($data->ssn_nd, 0, 3),
            'W9_Employee_SSN_4_5'          => substr($data->ssn_nd, 3, 2),
            'W9_Employee_SSN_6_9'          => substr($data->ssn_nd, 5, 4),
            'W9_Form_Date'                 => date('M-d-Y'),
            // Waive Benefits
            'WB_Employee_Name'             => $data->first_name . ' ' . $data->middle_initial . ' ' . $data->last_name,
            'WB_Form_Date'                 => date('M-d-Y'),
        );

        $status = 0; // start process

        $results = ['result'=>false, 'status'=>$status, 'message'=>'False Start!'];

        try {
            $pdf->Load($fields, false); // second parameter: false if field values are in ISO-8859-1, true if UTF-8
            $pdf->Merge(true);
            $status = 1; // ready for pdf output
        } catch (Exception $e) {
            logger(__METHOD__)->error('PDFTK Failure', ['error' => print_r($e, true)]);
            $results = ['result'=>false, 'status'=>$status, 'message'=>'Failed Load/Merge'];
        }

        $file_size = 0; $attachment = '';
        if ($status == 1) {
            try {
                $pdf_data = $pdf->Output('S', '');
                $attachment = chunk_split(base64_encode($pdf_data));
                $file_size = strlen($pdf_data);
                $status = 2; // generated PDF
            } catch (Exception $e) {
                logger(__METHOD__)->error('PDFTK Failure', ['error' => print_r($e, true)]);
                $results = ['result'=>false, 'status'=>$status, 'message'=>'Failed Output PDF'];
            }
        }
        $pdf->closeFile();
        unset($pdf);

        if ($status == 2) {
            $status = 3; // ready to email

            $filename = 'employee-new-hire.pdf';
            $attachments[] = array('data' => $attachment, 'size' => $file_size, 'name' => $filename);

            $to = 'hlangefeld@whittoncompanies.com,knealy@whittoncompanies.com,mnielsen@whittoncompanies.com';
            if (Location::getLocationByIPAddress() == 'TUCSON') {
                $to = 'ccota@whittoncompanies.com,knealy@whittoncompanies.com,mnielsen@whittoncompanies.com';
            }

            $from = 'no-reply@whittoncompanies.com';

            $subject = 'HR Employee New Hire Paperwork';
            $message = '<p>Attached is the Employee New Hire paperwork for:</p>';
            $message .=
                '<p>' . ucwords(strtolower($data->first_name)) . ' ' .
                strtoupper($data->middle_initial) . ' ' .
                ucwords(strtolower($data->last_name)) . ' ' .
                'DOB: ' . Date::formatDate($data->date_of_birth, 'M-d-Y') . '</p>';

            // save the data they sent
            logger(__METHOD__)->info('New Hire Data', ['data' => json_encode($data)]);

            if (Mail::send($to, $from, $subject, $message, $attachments)) {
                $results = ['result'=>true, 'status'=> $status, 'message'=>'Success!'];
            } else {
                $results = ['result'=>false, 'status'=>$status, 'message'=>'Failed Email!'];
            }
        }

        return $results;
    }
}

