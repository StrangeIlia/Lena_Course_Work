<?php


namespace app\modules\api\controllers;


use app\models\Phone;
use yii\filters\AccessControl;

class PhonesController extends BaseActiveController
{
    public $modelClass = 'app/models/Phone';

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['access'] = [
            'class' => AccessControl::className(),
            'rules' => [
                [
                    'actions' => ['create', 'update', 'delete', 'purchase'],
                    'allow' => true,
                    'roles' => ['@'],
                ],
            ],
        ];

        return $behaviors;
    }

    public function actionPurchase($phoneId)
    {
        $phone = Phone::findOne($phoneId);
        $user = Yii::$app->user->identity;
        if ($phone === null)
            return ['result' => 'Не найдет телефон с таким id'];
        if ($user->score < $phone->cost)
            return ['result' => 'Не достаточно денег на счету'];

        $user->score -= $phone->cost;
        $phone->count -= 1;
        $user->save(false);
        $phone->save(false);
        return ['result' => 'Покупка успешна'];
    }
}