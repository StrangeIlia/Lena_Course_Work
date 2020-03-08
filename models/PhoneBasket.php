<?php

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "PhoneBaskets".
 *
 * @property int $id
 * @property int $phone_id
 * @property int $basket_id
 *
 * @property Phone $phone
 * @property Basket $basket
 */
class PhoneBasket extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'PhoneBaskets';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['phone_id', 'basket_id'], 'required'],
            [['phone_id', 'basket_id'], 'integer'],
            [['phone_id'], 'exist', 'skipOnError' => true, 'targetClass' => Phone::className(), 'targetAttribute' => ['phone_id' => 'id']],
            [['basket_id'], 'exist', 'skipOnError' => true, 'targetClass' => Basket::className(), 'targetAttribute' => ['basket_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'phone_id' => 'Phone ID',
            'basket_id' => 'Basket ID',
        ];
    }

    /**
     * Gets query for [[Phone]].
     *
     * @return ActiveQuery
     */
    public function getPhone()
    {
        return $this->hasOne(Phone::className(), ['id' => 'phone_id']);
    }

    /**
     * Gets query for [[Basket]].
     *
     * @return ActiveQuery
     */
    public function getBasket()
    {
        return $this->hasOne(Basket::className(), ['id' => 'basket_id']);
    }
}
