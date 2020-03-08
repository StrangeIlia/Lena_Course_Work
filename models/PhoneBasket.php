<?php

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "PhoneBaskets".
 *
 * @property int $phone_id
 * @property int $basket_id
 *
 * @property Phones $phone
 * @property Baskets $basket
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
            [['phone_id'], 'exist', 'skipOnError' => true, 'targetClass' => Phones::className(), 'targetAttribute' => ['phone_id' => 'id']],
            [['basket_id'], 'exist', 'skipOnError' => true, 'targetClass' => Baskets::className(), 'targetAttribute' => ['basket_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
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
        return $this->hasOne(Phones::className(), ['id' => 'phone_id']);
    }

    /**
     * Gets query for [[Basket]].
     *
     * @return ActiveQuery
     */
    public function getBasket()
    {
        return $this->hasOne(Baskets::className(), ['id' => 'basket_id']);
    }
}
