<?php

namespace app\models;

use yii\db\ActiveQuery;

/**
 * This is the model class for table "Phones".
 *
 * @property int $id
 * @property string $mark Марка телефона
 * @property float $cost Стоимость телефона
 * @property int $count Их количество
 * @property string $preview Вид телефона
 * @property string $created_at
 * @property string $updated_at
 *
 * @property PhoneContainers[] $phoneContainers
 * @property Container[] $containers
 */
class Phone extends BaseActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'Phones';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['mark', 'cost', 'count', 'preview', 'created_at', 'updated_at'], 'required'],
            [['cost'], 'number'],
            [['count'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['mark'], 'string', 'max' => 30],
            [['preview'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'mark' => 'Mark',
            'cost' => 'Cost',
            'count' => 'Count',
            'preview' => 'Preview',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[PhoneContainers]].
     *
     * @return ActiveQuery
     */
    public function getPhoneContainers()
    {
        return $this->hasMany(PhoneContainers::className(), ['phone_id' => 'id']);
    }

    /**
     * Gets query for [[Containers]].
     *
     * @return ActiveQuery
     */
    public function getContainers()
    {
        return $this->hasMany(Container::className(), ['id' => 'container_id'])->viaTable('PhoneContainers', ['phone_id' => 'id']);
    }
}
