<?php


namespace app\modules\api\controllers;


use app\models\Phone;
use Yii;
use yii\filters\AccessControl;

class PhonesController extends BaseActiveController
{
    public $modelClass = 'app\models\Phone';

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['bearerAuth']['optional'] = ['index', 'view'];

        $behaviors['access'] = [
            'class' => AccessControl::className(),
            'rules' => [
                [
                    'actions' => ['create', 'update', 'delete', 'purchase'],
                    'allow' => true,
                    'roles' => ['@'],
                ],
                [
                    'actions' => ['index', 'view'],
                    'allow' => true,
                    'roles' => ['@', '?'],
                ],
            ],
        ];

        return $behaviors;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['create']);
        return $actions;
    }

    public function actionCreate()
    {
        $phone = new Phone();
        if ($phone->load(Yii::$app->request->post(), '') && $phone->save(false))
            return true;
        else
            return false;
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