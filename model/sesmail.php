<?php
/**
 * Created by PhpStorm.
 * User: m454k1
 * Date: 2017/10/26
 * Time: 17:38
 */

namespace Goteo\Model {

    use Aws\Ses\SesClient,
        Aws\Ses\Exception\SesException,
//        Goteo\Library\Template,
        Goteo\Library\Text,
        Goteo\Library\Message;

    class SESMail {

        public
            $sesClient;

        public function __construct () {
            try {
                $this->sesClient = SesClient::factory(array(
                    'version'     => 'latest',
                    'credentials' => [
                        'key'     => AWS_SES_ACCESS,
                        'secret'  => AWS_SES_SECERET,
                    ],
                    'region'  => 'us-west-2'
                ));
            } catch (SesException $exc) {
                die($exc->getMessage());
            }
        }

        /*
         *
         * $$sendinfo = array(
         *  'to' => array(<アドレス>),
         *  'replyTo' => array(<アドレス>)
         * )
         *
         */
        public function sendMail ($sendinfo,$subject,$content,$contentHtml='') {
            $params = array(
                'Source' => AWS_SES_SOURCE,
                'Destination' => array(
                    'ToAddresses' => $sendinfo['to']
                ),
                'Message' => array(
                    'Subject' => array(
                        'Data' => $subject,
                        'Charset' => AWS_SES_CHARSET,
                    ),
                    'Body' => array(
                        'Text' => array(
                            'Data' => $content,
                            'Charset' => AWS_SES_CHARSET,
                        ),
                        'Html' => array(
                            'Data' => $contentHtml,
                            'Charset' => AWS_SES_CHARSET,
                        ),
                    ),
                ),
            );

            // replyTo追加
            if (is_array($sendinfo->replyTo)){
                array_merge($params,array(
                    'ReplyToAddresses' => $sendinfo['replyTo']
                ));
            }

            $result = $this->sesClient->sendEmail($params);

        }

        /*
         *  sendEmail parameter syntax
         *
            $result = $client->sendEmail([
                'ConfigurationSetName' => '<string>',
                'Destination' => [ // REQUIRED
                    'BccAddresses' => ['<string>', ...],
                    'CcAddresses' => ['<string>', ...],
                    'ToAddresses' => ['<string>', ...],
                ],
                'Message' => [ // REQUIRED
                    'Body' => [ // REQUIRED
                        'Html' => [
                            'Charset' => '<string>',
                            'Data' => '<string>', // REQUIRED
                        ],
                        'Text' => [
                            'Charset' => '<string>',
                            'Data' => '<string>', // REQUIRED
                        ],
                    ],
                    'Subject' => [ // REQUIRED
                        'Charset' => '<string>',
                        'Data' => '<string>', // REQUIRED
                    ],
                ],
                'ReplyToAddresses' => ['<string>', ...],
                'ReturnPath' => '<string>',
                'ReturnPathArn' => '<string>',
                'Source' => '<string>', // REQUIRED
                'SourceArn' => '<string>',
                'Tags' => [
                    [
                        'Name' => '<string>', // REQUIRED
                        'Value' => '<string>', // REQUIRED
                    ],
                    // ...
                ],
            ]);
        */
    }
}
