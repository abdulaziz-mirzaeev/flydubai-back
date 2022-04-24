<?php

namespace console\helpers;


use GuzzleHttp\Client;
use GuzzleHttp\Exception\InvalidArgumentException;


/**
 *
 * @author Xolmat Ravshanov
 * @github https://github.com/xolmatravshanov
 */
class SmsHelper
{

    /**
     * methods
     */
    const METHOD_POST = 'POST';
    const METHOD_GET = 'GET';

    /**
     * @var \GuzzleHttp\Client
     */
    private $client;

    /**
     * @var responseBody
     */
    public $responseBody;

    /**
     * @var api token
     */
    private $token;

    /**
     * @var $baseUrl
     */
    public $baseUrl = 'http://91.204.239.44';

    /**
     * @var int
     */
    public $timeout = 200;

    /**
     * @var string
     */
    private $login = 'flydubaiuz';

    /**
     * @var string
     */
    private $password = 'M5U5sy8Vi9';

    /**
     * @var string
     */
    public $reciverNumber = '+998934631525';

    /**
     * @var string
     */
    public $messageId = '12';

    /**
     * @var string 3700
     */
    public $text = '';

    /**
     * @var string
     */
    private $originator = '3700';

    /**
     * @var
     */
    private $response;

    /**
     * @var int
     */
    public $responseCode;
    private $root;

    private $logger;


    public function __construct($reciverNumber, $message, $message_id = 500)
    {

        $this->messageId = $message_id;
        $this->text = $message;
        $this->reciverNumber = $reciverNumber;

        // base object for sending request
        $this->client = new Client([
            'base_uri' => $this->baseUrl,
        ]);

        $this->logger = new Logger('SmsHelper');


    }

    public function sendRequest()
    {

        try {
            $this->response = $this->client->request(self::METHOD_POST, '/broker-api/send', [
                'auth' => [$this->login, $this->password],
                'json' => [
                    "messages" => [
                        [
                            "recipient" => $this->reciverNumber,
                            "message-id" => $this->messageId,
                            "sms" => [
                                "originator" => $this->originator,
                                "content" => [
                                    "text" => "$this->text"
                                ]
                            ],
                        ]
                    ]
                ]
            ]);
        } catch (InvalidArgumentException $exception) {
            $this->logger->setLog($exception->getMessage() . __METHOD__);
            return 500;
        }

        if ($this->response) {
            $this->responseCode = $this->response->getStatusCode();
            $content = $this->responseCode . "|responseCode|" . $this->reciverNumber . '|reciverNumber| ' . $this->messageId . '|messageId|' . $this->originator . '|originator| ' . $this->text . '|text| ' . date("Y-m-d H:i:s") . PHP_EOL;
            $this->logger->setLog($content . __METHOD__);
            return $this->responseCode;
        }

    }


}
