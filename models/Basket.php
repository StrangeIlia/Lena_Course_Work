<?php

namespace app\models;

use yii\db\ActiveQuery;

/**
 * This is the model class for table "Baskets".
 *
 * @property int $id
 * @property int $user_id Создатель корзины
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Users $user
 * @property PhoneBaskets[] $phoneBaskets
 */
class Basket extends BaseActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'Baskets';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'created_at', 'updated_at'], 'required'],
            [['user_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[User]].
     *
     * @return ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Users::className(), ['id' => 'user_id']);
    }

    /**
     * Gets query for [[PhoneBaskets]].
     *
     * @return ActiveQuery
     */
    public function getPhoneBaskets()
    {
        return $this->hasMany(PhoneBaskets::className(), ['basket_id' => 'id']);
    }

    public function getPhoneBasket($phone_id)
    {
        return $this->hasOne(PhoneBaskets::className(), ['basket_id' => 'id', 'phone_id' => $phone_id]);
    }

    public function getPhones()
    {
        return $this->hasMany(Phone::className(), ['id' => 'phone_id'])
            ->viaTable(PhoneBasket::tableName(), ['basket_id' => 'id']);
    }
}
