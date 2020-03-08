<?php

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

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
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
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
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * Gets query for [[PhoneBaskets]].
     *
     * @return ActiveQuery
     */
    public function getPhoneBaskets()
    {
        return $this->hasMany(PhoneBasket::className(), ['basket_id' => 'id']);
    }

    /**
     * Gets query for [PhoneBasket].
     *
     * @return array|ActiveRecord
     */
    public function getPhoneBasket($phone_id)
    {
        return $this->hasOne(PhoneBasket::className(), ['basket_id' => 'id'])->where(['phone_id' => $phone_id])->one();
    }

    public function getPhones()
    {
        $phone_baskets = $this->getPhoneBaskets();
        $tmp_massive = [];
        foreach ($phone_baskets->all() as $phone_basket) {
            $phone = null;
            if (!key_exists($phone_basket['phone_id'], $tmp_massive)) {
                $phone = Phone::findOne($phone_basket['phone_id']);
                $phone->count = 1;
                $tmp_massive[$phone_basket['phone_id']] = $phone;
            } else $tmp_massive[$phone_basket['phone_id']]->count += 1;
        }
        $result = [];
        foreach ($tmp_massive as $phone)
            $result[] = $phone;
        return $result;
//        return $this->hasMany(Phone::className(), ['id' => 'phone_id'])
//            ->viaTable(PhoneBasket::tableName(), ['basket_id' => 'id']);
    }
}
