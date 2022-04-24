<?php

namespace console\controllers;


use console\helpers\Logger;
use console\helpers\Process;
use console\helpers\SmsHelper;
use console\models\Sms;
use Yii;
use yii\console\Controller;
use yii\db\Query;

/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Xolmat Ravshanov
 * @github https://github.com/xolmatravshanov
 */
class NotifyFlyController extends Controller
{


    public function options($actionID)
    {
        return ['userId'];
    }

    public function __construct($id, $module, $config = [])
    {
        parent::__construct($id, $module, $config);
    }

    public function actionNotify()
    {

        $logger = new Logger('NotifyFlyController');

        while (true) {

            $tickets = self::getTikcets();


            foreach ($tickets as $ticket) {

                $string = $ticket['phone'] . "foreach" . PHP_EOL;
                $logger->setLog($string);

                echo $ticket['phone'] . PHP_EOL;

                $message = 'Message From Console';
                $logger->setLog($message . 'STATIC MESSAGE FROM FOREACH' . PHP_EOL);
                if ($ticket['phone']) {
                    $logger->setLog('PHONE Exits' . PHP_EOL);
                    $smsSender = new SmsHelper($ticket['phone'], $message, $ticket['id']);
                    $responseCode = $smsSender->sendRequest();

                    try {
                        $smsModel = new Sms();
                        $smsModel->message = $message;
                        $smsModel->error = 1;
                        $logger->setLog('INSIDE TRY CATCH' . PHP_EOL);
                        if ($responseCode == 200) {
                            $smsModel->error = 0;
                        }

                        $smsModel->phone = $ticket['phone'];
                        $smsModel->status = 1;
                        $smsModel->created_at = date('Y-m-d H:i:s');
                        $smsModel->created_by = $ticket['id'];
                        $smsModel->save(false);

                        Yii::$app->db->createCommand("UPDATE ticket SET notified=:notified WHERE id=:id")
                            ->bindValue(':notified', 1)
                            ->bindValue(':id', $ticket['ticket_id'])
                            ->execute();

                    } catch (\Exception $exception) {
                        $message = $exception->getMessage();
                        $logger->setLog($message);
                    }
                }

                echo "SLEEP INSIDE FOREACH  " . PHP_EOL;

                sleep(5);

            }

            echo "SLEEP INSIDE WHILE " . PHP_EOL;

            sleep(60);
        }

    }

    public static function getTikcets()
    {

        $now = date('Y-m-d H:i:s');
        $timeEarly = date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s") . " -10 minutes"));

        $tickets = (new Query())
            ->select('cl.phone, cl.first_name, cl.last_name, cl.id, tck.id as ticket_id')
            ->from('ticket tck')
            ->innerJoin('client cl', 'tck.client_id = cl.id')
            ->where([
                'between', 'flight_date', $timeEarly, $now
            ])
            ->andWhere(['cl.send_newsletter' => 1])
            ->andWhere(['tck.notified' => 0])
            ->all();


        return $tickets;

    }

    public function actionLogger()
    {
        $logger = new Logger('NotifyFlyController');
        $logger->setLog('test Console');
    }

    public function actionStart()
    {
        //windows cd projectRoot | yii notify-fly/notify
        (new Process())->start();
        //linux cd projectRoot |php yii notify-fly/notify
    }

    public function actionSmsSendTest()
    {
        $smsSender = new SmsHelper('998934631525', 'test', 200);
        $responseCode = $smsSender->sendRequest();
        var_dump($responseCode);
    }
}
