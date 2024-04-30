<?php

namespace App\Services;

use App\Helper\TodoResponse;
use Brevo\Client\Api\TransactionalEmailsApi;
use Brevo\Client\Configuration;
use Brevo\Client\Model\SendSmtpEmail;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;

class Brevo
{
    public static function sendMail($template_name, $name, $email)
    {
        $temp = DB::table('templates')->where('template_name', $template_name)->first();
        $config = Configuration::getDefaultConfiguration()->setApiKey('api-key', env('BREVO_API_KEY'));

        $apiInstance = new TransactionalEmailsApi(
            new Client(),
            $config
        );
        $sendSmtpEmail = new SendSmtpEmail([
            'subject' => $temp->subject,
            'sender' => ['name' => 'no-reply', 'email' => 'no-reply@laravel.com'],
            'replyTo' => ['name' => 'no-reply', 'email' => 'no-reply@laravel.com'],
            'to' => [['name' => $name, 'email' => $email]],
            'htmlContent' => str_replace('[[name]]', $name, $temp->template)
        ]);

        try {
            $result = $apiInstance->sendTransacEmail($sendSmtpEmail);
            $data = [
                'code' => 200,
                'message' => 'Email sent successfully', 
                'messageId' => $result['messageId'],
            ];
            return $data;
        } catch (Exception $e) {
            return $e->getMessage();
        }
        // $config = Configuration::getDefaultConfiguration()->setApiKey('api-key', env('BREVO_API_KEY'));

        // $apiInstance = new TransactionalEmailsApi(
        //     new Client(),
        //     $config
        // );
        // $templateId = 8;
        // $sendTestEmail = new SendTestEmail();
        // $sendTestEmail['emailTo'] = array('zaeemansari87@gmail.com', 'salfia43@gmail.com');

        // try {
        //     $apiInstance->sendTestTemplate($templateId, $sendTestEmail);
        // } catch (Exception $e) {
        //     echo 'Exception when calling TransactionalEmailsApi->sendTestTemplate: ', $e->getMessage(), PHP_EOL;
        // }
    }
}
