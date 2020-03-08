<?php

namespace app\models;

use Yii;
use yii\base\Exception;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "Users".
 *
 * @property int $id
 * @property string $username
 * @property string $email
 * @property string $password
 * @property string $access_token
 * @property float $score Счет клиента
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Baskets[] $baskets
 */
class User extends BaseActiveRecord implements IdentityInterface
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'Users';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['username', 'email', 'password', 'access_token', 'score', 'created_at', 'updated_at'], 'required'],
            [['score'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['username', 'email', 'password'], 'string', 'max' => 30],
            [['access_token'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'email' => 'Email',
            'password' => 'Password',
            'access_token' => 'Access Token',
            'score' => 'Score',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }


    /**
     * Gets query for [[Baskets]].
     *
     * @return Baskets array
     */
    public function getBaskets()
    {
        return $this->hasMany(Baskets::className(), ['user_id' => 'id']);
    }

    /**
     * @inheritDoc
     */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    /**
     * @inheritDoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(["access_token" => $token]);
    }

    /**
     * @inheritDoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritDoc
     */
    public function getAuthKey()
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function validateAuthKey($authKey)
    {
        return false;
    }


    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username]);
    }


    public function validatePassword($password)
    {
        return $this->password == $password;
    }

    public static function getMinLengthPassword()
    {
        return 8;
    }

    public function checkPasswordLength()
    {
        return strlen($this->password) >= static::getMinLengthPassword();
    }

    public function generateAccessToken()
    {
        $this->access_token = Yii::$app->security->generateRandomString();
    }

    /**
     * Выдача токена и ключа авторизации
     * @param $insert
     * @return bool
     * @throws Exception
     */
    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }
        if ($insert) {
            if ($this->checkPasswordLength()) {
                $this->generateAccessToken();
                return true;
            } else return false;
        }
        return true;
    }
}
