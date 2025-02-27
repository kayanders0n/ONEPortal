<?php

namespace Helpers;

class Mail
{
    /**
     * @param string $to
     * @param string $from
     * @param string $subject
     * @param string $html_message
     * @param null|array $attachments
     *
     * @return bool
     */
    public static function send(string $to, string $from, string $subject, string $html_message, array $attachments = null): bool {
        $headers = 'MIME-Version: 1.0'."\r\n";
        $headers .= "From: $from\r\nReply-To: $from\r\n";

        $random_hash = md5(date('r', time()));
        $headers .= "Content-Type: multipart/mixed; boundary=\"tww-mixed-".$random_hash."\"\r\n";

        $message = '--tww-mixed-'.$random_hash."\r\n";
        $message .= 'Content-Type: multipart/alternative; boundary="tww-alt-'.$random_hash."\"\r\n\r\n";
        $message .= '--tww-alt-'.$random_hash."\r\n";
        $message .= 'Content-Type: text/plain; charset="iso-8859-1"'."\r\n";
        $message .= 'Content-Transfer-Encoding: 7bit'."\r\n\r\n";
        $message .= 'If you are reading this your client does not support HTML.'."\r\n";
        $message .= '--tww-alt-'.$random_hash."\r\n";
        $message .= 'Content-Type: text/html; charset="iso-8859-1"'."\r\n";
        $message .= 'Content-Transfer-Encoding: 7bit'."\r\n\r\n";
        $message .= $html_message."\r\n";
        $message .= "\r\n\r\n";
        $message .= '--tww-alt-'.$random_hash.'--'."\r\n";

        if (isset($attachments)) {
            foreach ($attachments as $attachment) {
                $message .= '--tww-mixed-' . $random_hash . "\r\n";

                $message .= "Content-Type: application/octet-stream; name=\"" . $attachment['name'] . "\"\n" .
                    "Content-Description: " . $attachment['name'] . "\n" .
                    "Content-Disposition: attachment;\n" . " filename=\"" . $attachment['name'] . "\"; size=" . $attachment['size'] . ";\n" .
                    "Content-Transfer-Encoding: base64\n\n" . $attachment['data'] . "\n\n";
            }

            $message .= '--tww-mixed-' . $random_hash . '--' . "\r\n";
        }

        $to = trim($to);
        $to = ltrim($to, ',');
        $to = rtrim($to, ',');

        if (!mail($to, $subject, $message, $headers)) {
            logger(__METHOD__)->error('Mail Send Failure', ['to'=>$to, 'subject'=>$subject, 'error'=>error_get_last()['message']]);
            return false;
        } else {
            //logger(__METHOD__)->debug('Mail Worked', ['to'=>$to, 'subject'=>$subject]);
            return true;
        }
    }

    /**
     * @param $input
     *
     * @return int
     */
    public static function isEmail($input): int
    {
        return preg_match('/([\w\-]+\@[\w\-]+\.[\w\-]+)/', $input);
    }
}

