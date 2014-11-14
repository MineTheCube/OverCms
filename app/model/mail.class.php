<?php

class Mail {
    
    public function __construct() {
    }

    public function send($toEmail, $subject, $message, $html = false, $fromEmail = null, $fromName = null) {
        
        $config = new Config;
        if ($fromEmail === null) {
            $fromEmail = $config->get('user->info->email', false);
            if ($fromEmail == false) {
                throw new Exception('EMAIL_INCORRECT');
                return false;
            }
        }
        
        if ($fromName === null) {
            $fromName = $config->get('user->info->name', false);
            if ($fromName == false) {
                throw new Exception('INVALID_DATA');
                return false;
            }
        }
        
        if (empty($fromName)) {
            throw new Exception('INVALID_DATA');
            return false;
        }
        
        if (!preg_match('/^[[:alnum:][:punct:]]{3,32}@[[:alnum:]-.$nonASCII]{3,32}\.[[:alpha:].]{2,5}$/', $toEmail)) {
            throw new Exception('EMAIL_INCORRECT');
            return false;
        }
    
        if (!preg_match('/^[[:alnum:][:punct:]]{3,32}@[[:alnum:]-.$nonASCII]{3,32}\.[[:alpha:].]{2,5}$/', $fromEmail)) {
            throw new Exception('EMAIL_INCORRECT');
            return false;
        }
        
        $fromName = htmlspecialchars($fromName);
        $subject = htmlspecialchars($subject);
        if (!$html) {
            $message = nl2br(htmlspecialchars($message));
        }
        $date = date('Y-m-d');
        $year = date('Y');
             
        // Random key
        $boundary = md5(uniqid(microtime(), TRUE));
         
        // Headers
        $headers = 'From: ' . $fromName. ' <' . $fromEmail . '>'."\r\n";
        $headers .= 'Mime-Version: 1.0'."\r\n";
        $headers .= 'Content-Type: multipart/mixed;boundary='.$boundary."\r\n";
        $headers .= "\r\n";
         
        // Default Message
        $msg = 'Sorry, your mail client does not support MIME mail.'."\r\n\r\n";
         
        // HTML Message
        $msg .= '--'.$boundary."\r\n";
        $msg .= 'Content-type: text/html; charset=utf-8'."\r\n\r\n";
        $msg .= '
        <div>
          <div>
            <table align="center" border="0" cellpadding="0" cellspacing="0" width="600">
              <tbody>
                <tr valign="top">
                  <td width="100%">
                    <table align="center" border="0" cellpadding="0" cellspacing="0" width="600">
                      <tbody>
                        <tr valign="top">
                          <td style="font-size:28px;font-weight: bold;font-family:arial,helvetica,sans-serif;color:#111111">
                            '.$fromName.'
                          </td>
                          <td style="font-size:11px;font-family:arial,helvetica,sans-serif;color:#333333" valign="middle" align="right">
                            '.$date.'<br>
                            '.$fromEmail.' 
                          </td>
                        </tr>
                      </tbody>
                    </table>
                    <div style="border-top: 2px solid #dddddd;margin-top: 10px;">
		      <div style="font-size:12px;font-family:arial,helvetica,sans-serif;color:#333333;padding-top:20px">
                        '.$message.'
	              </div>
                      <table align="center" border="0" cellpadding="0" cellspacing="0" style="font-family:arial,helvetica,sans-serif;color:#333333;margin-top:50px">
                        <tbody>
                          <tr>
                            <td style="font-size:11px;font-family:arial,helvetica,sans-serif;color:#757575">
                              Copyright © '.$year.' '.$fromName.' - All right reserved
                              <br>
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>'."\r\n";
          
        $msg .= '--'.$boundary."\r\n";             
        $result = mail($toEmail, $subject, $msg, $headers);
        return $result;
    }

}
