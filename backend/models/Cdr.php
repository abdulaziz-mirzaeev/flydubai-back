<?php

namespace backend\models;

use common\models\User;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "cdr".
 *
 * @property string $calldate
 * @property string $clid
 * @property string $src
 * @property string $dst
 * @property string $dcontext
 * @property string $channel
 * @property string $dstchannel
 * @property string $lastapp
 * @property string $lastdata
 * @property int $duration
 * @property int $billsec
 * @property string $disposition
 * @property int $amaflags
 * @property string $accountcode
 * @property string $uniqueid
 * @property string $userfield
 * @property string $did
 * @property string $recordingfile
 * @property string $cnum
 * @property string $cnam
 * @property string $outbound_cnum
 * @property string $outbound_cnam
 * @property string $dst_cnam
 * @property string $linkedid
 * @property string $peeraccount
 * @property int $sequence
 */
class Cdr extends \backend\models\BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cdr';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('cdr');
    }


    public static function primaryKey()
    {
        return [
            'uniqueid'
        ];
    }


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['calldate'], 'safe'],
            [['duration', 'billsec', 'amaflags', 'sequence'], 'integer'],
            [['clid', 'src', 'dst', 'dcontext', 'channel', 'dstchannel', 'lastapp', 'lastdata', 'cnum', 'cnam', 'outbound_cnum', 'outbound_cnam', 'dst_cnam', 'peeraccount'], 'string', 'max' => 80],
            [['disposition'], 'string', 'max' => 45],
            [['accountcode'], 'string', 'max' => 20],
            [['uniqueid', 'linkedid'], 'string', 'max' => 32],
            [['userfield', 'recordingfile'], 'string', 'max' => 255],
            [['did'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'calldate' => 'Calldate',
            'clid' => 'Clid',
            'src' => 'Src',
            'dst' => 'Dst',
            'dcontext' => 'Dcontext',
            'channel' => 'Channel',
            'dstchannel' => 'Dstchannel',
            'lastapp' => 'Lastapp',
            'lastdata' => 'Lastdata',
            'duration' => 'Duration',
            'billsec' => 'Billsec',
            'disposition' => 'Disposition',
            'amaflags' => 'Amaflags',
            'accountcode' => 'Accountcode',
            'uniqueid' => 'Uniqueid',
            'userfield' => 'Userfield',
            'did' => 'Did',
            'recordingfile' => 'Recordingfile',
            'cnum' => 'Cnum',
            'cnam' => 'Cnam',
            'outbound_cnum' => 'Outbound Cnum',
            'outbound_cnam' => 'Outbound Cnam',
            'dst_cnam' => 'Dst Cnam',
            'linkedid' => 'Linkedid',
            'peeraccount' => 'Peeraccount',
            'sequence' => 'Sequence',
        ];
    }

    const status_callcenter = [
        'ordered' => 'Сделал заказ',
        'about_ticket' => 'Узнал информацию по билетам, визам, турпакетам, карго',
        'about_company' => 'Узнал информацию о компании',
        'placed_reservation' => 'Поставил бронь',
    ];

    /**
     * @param $number
     * @return mixed
     *
     *
     *
     * •
     * •
     * •
     */

    public static function getCallerId($number)
    {
        //change astProduction
        return Yii::$app->asterisk->createCommand('SELECT outboundcid FROM users WHERE extension=:extension')
            ->bindValue(':extension', $number)
            ->queryOne();
    }

    public static function getBySql($sql)
    {
        return Yii::$app->cdr->createCommand($sql)->queryAll();
    }

    public static function check($number = null, $from = null, $to = null)
    {

        $r = [];

        if (!$number)
            return $r;

        $callerId = self::getCallerId($number);

        $callerId = ArrayHelper::getValue($callerId, 'outboundcid');

        $incomingSql = "SELECT s.calldate, s.uniqueid, s.src, s.dst, s.userfield,s.disposition FROM cdr AS s WHERE  
          dst=$number AND s.calldate IN ( SELECT  p.calldate FROM cdr p INNER JOIN ( SELECT MAX(calldate) AS max_date
          FROM cdr  WHERE cdr.calldate BETWEEN '$from' AND '$to'  GROUP BY src
          ORDER BY calldate ASC ) m ON calldate = m.max_date ) 
          GROUP BY
            s.src";

        if ($callerId) {
            $outgoingSql = "SELECT s.calldate, s.uniqueid, s.src, s.userfield, s.dst, s.disposition FROM cdr AS s WHERE  
          src=$callerId AND s.calldate IN ( SELECT  p.calldate FROM cdr p INNER JOIN ( SELECT MAX(calldate) AS max_date
          FROM cdr  WHERE cdr.calldate BETWEEN '$from' AND '$to'  GROUP BY dst
          ORDER BY calldate ASC ) m ON calldate = m.max_date ) 
          GROUP BY
            s.dst";
        }

        $incomings = self::getBySql($incomingSql);

        if ($callerId)
            $outgoings = self::getBySql($outgoingSql)->all();

        if (!$outgoings)
            $outgoings = [];

        foreach ($incomings as $keyIn => $incoming) {
            if ($callerId) {
                foreach ($outgoings as $keyOut => $outgoing) {
                    if ($incoming['src'] === $outgoing['dst']) {
                        if (strtotime($incoming['calldate']) > strtotime($outgoing['calldate'])) {
                            unset($outgoings[$keyOut]);
                        } else {
                            unset($incomings[$keyIn]);
                        }
                    }
                }
            }
        }

        foreach ($incomings as $incoming) {
            if ($incoming['disposition'] === 'ANSWERED')
                continue;
            $r[] = [
                'client' => $incoming['src'],
                'operator' => $incoming['dst'],
                'calldate' => $incoming['calldate'],
                'uniqueid' => $incoming['uniqueid'],
                'disposition' => $incoming['disposition'],
                'userfield' => $incoming['userfield'],

            ];
        }

        if ($callerId) {
            foreach ($outgoings as $outgoing) {
                if ($outgoing['disposition'] === 'ANSWERED')
                    continue;
                $r[] = [
                    'client' => $outgoing['dst'],
                    'operator' => $outgoing['src'],
                    'calldate' => $outgoing['calldate'],
                    'uniqueid' => $outgoing['uniqueid'],
                    'disposition' => $outgoing['disposition'],
                    'userfield' => $outgoing['userfield'],
                ];
            }
        }

        return $r;
    }

    public static function getMissedCallsByExt($from, $to, $extention)
    {

        // extentionga keluvchi nomerlarni maxmalini olib keladi agar dispotion ANSWERED bo'lsa o'tkazib yuboramiz
        //
        $return = [];

        if (!$from && !$to && !$extention)
            return $return;

        $sql = "SELECT s.calldate, s.uniqueid, s.src, s.dst, s.userfield, s.disposition
            FROM cdr AS s WHERE dst = \"$extention\" AND s.calldate IN ( SELECT p.calldate FROM cdr p
	        INNER JOIN ( SELECT MAX(calldate) AS max_date FROM cdr WHERE
	         cdr.calldate BETWEEN \"$from\" AND \"$to\" GROUP BY src ORDER BY calldate ASC ) m
	          ON calldate = m.max_date ) GROUP BY s.src";

        $incomings = self::getBySql($sql);

        foreach ($incomings as $incoming) {

            if ($incoming['disposition'] === 'ANSWERED')
                continue;

            $r = [
                'src' => $incoming['src'],
                'dst' => $incoming['dst'],
                'calldate' => $incoming['calldate'],
                'uniqueid' => $incoming['uniqueid'],
                'disposition' => $incoming['disposition'],
                'userfield' => $incoming['userfield'],
            ];
            $return[] = $r;
        }

        return $return;

    }

    public static function getAllCallsByExt($from, $to, $extention)
    {

        // bitta exstention tegishli hammasini olib keladi
        $return = [];
        if (!$from && !$to && !$extention)
            return $return;

        $sql = " SELECT `calldate`,  `src` , `dst`, `disposition`, `userfield` FROM `cdr` WHERE  `dst` = \"$extention\" AND
            `calldate`  BETWEEN  \"$from\" AND \"$to\" ORDER BY `calldate` DESC";

        $incomings = self::getBySql($sql);

        foreach ($incomings as $incoming) {

            $r = [
                'client' => $incoming['src'],
                'operator' => $incoming['dst'],
                'calldate' => $incoming['calldate'],
                'uniqueid' => $incoming['uniqueid'],
                'disposition' => $incoming['disposition'],
                'userfield' => $incoming['userfield'],
            ];

            $return[] = $r;

        }

        return $return;
    }

    public static function getCountStatus($from, $to, $number)
    {
        if (!$number)
            return;

        $sql = "SELECT COUNT(DISTINCT `uniqueid`) as `count` , `userfield` as `status` , `dst` as `number` FROM `cdr`  WHERE `calldate` BETWEEN  \"$from\" AND \"$to\" 
                    AND `userfield` != '' AND `dst` = \"$number\" GROUP BY `userfield`";

        $result = self::getBySql($sql);

        return $result;

    }

    public static function getStats($from, $to)
    {
        $return = [];

        $users = User::find()->select('operator.number')->rightJoin('operator', 'operator.id=user.id')->all();

        foreach ($users as $user) {

            if (!$user->number)
                continue;

            $temp = self::getCountStatus($from, $to, $user->number);
            if (!$temp)
                continue;

            $return[] = $temp;

        }

        return $return;
    }


    public static function getStatuses()
    {
        return self::status_callcenter;
    }

     //gets times
    public static function getDateTime($day)
    {

        $time = [
            'from' => null,
            'to' => null,
        ];


        switch (true) {
            case $day === 'first':
                $time['from'] = $to = date('Y-m-d H:i:s', strtotime('today'));
                $time['to'] = $from = date('Y-m-d H:i:s', strtotime($to . '+1 days'));
                return $time;
                break;

            case $day === 'second':
                $time['to'] = $to = date('Y-m-d H:i:s', strtotime('today'));
                $time['from'] = $from = date('Y-m-d H:i:s', strtotime($to . '-1 days'));
                return $time;
                break;

            case $day === 'third':
                $d = date('Y-m-d H:i:s', strtotime('today'));
                $time['to'] = $to = date('Y-m-d H:i:s', strtotime($d . '-1 days'));
                $time['from'] = $from = date('Y-m-d H:i:s', strtotime($to . '-1 days'));
                return $time;
                break;

        }

        return $time;
    }


    public static function getInfo(){
        return [
            'calldate' => 'Дата и время вызова',
            'src' => 'Идентификатор вызывающего абонента',
            'dst' => 'Пункт назначения вызова',
            'userfield' => 'Статусы',
            'uniqueid' => 'Уникальный идентификатор канала',
            'disposition' => 'Состояние обработки вызова',
        ];
    }



}
