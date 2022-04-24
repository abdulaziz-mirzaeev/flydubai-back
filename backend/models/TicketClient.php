<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "ticket_client".
 *
 * @property int $id
 * @property int|null $ticket_id
 * @property int|null $client_id
 *
 * @property Client $client
 * @property Ticket $ticket
 */
class TicketClient extends \backend\models\BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ticket_client';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ticket_id', 'client_id', 'created_by'], 'integer'],
            [['created_at'], 'safe'],
            [['client_id'], 'exist', 'skipOnError' => true, 'targetClass' => Client::className(), 'targetAttribute' => ['client_id' => 'id']],
            [['ticket_id'], 'exist', 'skipOnError' => true, 'targetClass' => Ticket::className(), 'targetAttribute' => ['ticket_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'ticket_id' => 'Ticket ID',
            'client_id' => 'Client ID',
        ];
    }

    /**
     * Gets query for [[Client]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getClient()
    {
        return $this->hasOne(Client::className(), ['id' => 'client_id']);
    }

    /**
     * Gets query for [[Ticket]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTicket()
    {
        return $this->hasOne(Ticket::className(), ['id' => 'ticket_id']);
    }
}
