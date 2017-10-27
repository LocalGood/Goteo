<?php
/**
 * Created by PhpStorm.
 * User: m454k1
 * Date: 2017/10/26
 * Time: 17:38
 */

namespace Goteo\Library {

    use Aws\Ses\SesClient,
        Aws\Ses\Exception\SesException,
        Goteo\Core\View,
        Goteo\Core\Model;

    class SESMail {

        public
            $sesClient,
            $to,
            $content,
            $template;

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
         * $sendinfo = array(
         *  'to' => array(<アドレス>),          // 配列だが複数指定しない運用を想定
         *  'replyTo' => array(<アドレス>)      //
         * )
         */
        public function sendMail ($sendinfo,$subject,$content,$contentHtml='') {

            $this->to = $sendinfo['to'][0];
            $this->content = $content;

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
                            'Data' => $this->bodyText($content),
                            'Charset' => AWS_SES_CHARSET,
                        ),
                        'Html' => array(
                            'Data' => $this->bodyHTML($contentHtml),
                            'Charset' => AWS_SES_CHARSET,
                        ),
                    ),
                ),
            );

            // replyTo追加
            if (isset($sendinfo['replyTo']) && is_array($sendinfo['replyTo'])){
                $params['ReplyToAddresses'] = $sendinfo['replyTo'];
            }

            $result = $this->sesClient->sendEmail($params);

        }

        private function bodyText($content) {
            return strip_tags($content);
        }

        private function bodyHTML($content,$plain = false) {

            $viewData = array('content' => $content);

            // grabamos el contenido en la tabla de envios
            // especial para newsletter, solo grabamos un sinoves
            if ($this->template == 33) {
                if (!empty($_SESSION['NEWSLETTER_SENDID']) ) {
                    $sendId = $_SESSION['NEWSLETTER_SENDID'];
                } else {
                    $sql = "INSERT INTO mail (id, email, html, template) VALUES ('', :email, :html, :template)";
                    $values = array (
                        ':email' => 'any',
                        ':html' => $this->content,
                        ':template' => $this->template,
                        //':node' => $_SESSION['admin_node']
                    );
                    $query = Model::query($sql, $values);

                    $sendId = Model::insertId();
                    $_SESSION['NEWSLETTER_SENDID'] = $sendId;
                }
                $the_mail = 'any';
            } else {
                $sql = "INSERT INTO mail (id, email, html, template) VALUES ('', :email, :html, :template)";
                $values = array (
                    ':email' => $this->to,
                    ':html' => str_replace('cid:logo', SITE_URL.'/goteo_logo.png', $this->content),
                    ':template' => $this->template
                );
                $query = Model::query($sql, $values);

                $sendId = Model::insertId();
                $the_mail = $this->to;
            }

            if (!empty($sendId)) {
                // token para el sinoves
                $token = md5(uniqid()) . '¬' . $the_mail  . '¬' . $sendId;
                $viewData['sinoves'] = \SITE_URL . '/mail/' . base64_encode($token) . '/?email=' . $this->to;
            } else {
                $viewData['sinoves'] = \SITE_URL . '/contact';
            }
            $_SESSION['MAILING_TOKEN'] = $viewData['sinoves'];

            $viewData['baja'] = \SITE_URL . '/user/leave/?email=' . $this->to;

            if ($plain) {
                return strip_tags($this->content) . '

                ' . $viewData['sinoves'];
            } else {
                // para plantilla boletin
                if ($this->template == 33) {
                    $viewData['baja'] = \SITE_URL . '/user/leave/?unsuscribe=newsletter&email=' . $this->to;
                    return new View (GOTEO_PATH.'/view/email/newsletter.html.php', $viewData);
                } else {
                    return new View ('view/email/goteo.html.php', $viewData);
                }
            }
        }

        public static function getSended($filters = array(), $node = null, $limit = 9) {

            $values = array();
            $sqlFilter = '';
            $and = " WHERE";

            if (!empty($filters['user'])) {
                $sqlFilter .= $and . " mail.email LIKE :user";
                $and = " AND";
                $values[':user'] = "%{$filters['user']}%";
            }

            if (!empty($filters['template'])) {
                $sqlFilter .= $and . " mail.template = :template";
                $and = " AND";
                $values[':template'] = $filters['template'];
            }

            /*
            if ($node != \GOTEO_NODE) {
                $sqlFilter .= $and . " mail.node = :node";
                $and = " AND";
                $values[':node'] = $node;
            } else
            */
            if (!empty($filters['node'])) {
                $sqlFilter .= $and . " mail.node = :node";
                $and = " AND";
                $values[':node'] = $filters['node'];
            }

            if (!empty($filters['date_from'])) {
                $sqlFilter .= $and . " mail.date >= :date_from";
                $and = " AND";
                $values[':date_from'] = $filters['date_from'];
            }
            if (!empty($filters['date_until'])) {
                $sqlFilter .= $and . " mail.date <= :date_until";
                $and = " AND";
                $values[':date_until'] = $filters['date_until'];
            }

            $sql = "SELECT
                        mail.id as id,
                        mail.email as email,
                        mail.template as template,
                        DATE_FORMAT(mail.date, '%d/%m/%Y %H:%i') as date
                    FROM mail
                    $sqlFilter
                    ORDER BY mail.date DESC
                    LIMIT {$limit}";

            $query = Model::query($sql, $values);
            return $query->fetchAll(\PDO::FETCH_OBJ);

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
