<?php


namespace app\modules\api\controllers;


use app\models\Basket;
use app\models\Phone;
use app\models\PhoneBasket;
use Yii;
use yii\filters\AccessControl;

class BasketsController extends BaseController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['access'] = [
            'class' => AccessControl::className(),
            'rules' => [
                [
                    'actions' => ['purchase', 'append_phone', 'remove_phone', 'get_phones'],
                    'allow' => true,
                    'roles' => ['@'],
                ],
            ],
        ];

        return $behaviors;
    }

    public function actionPurchase()
    {
        $basket = $this->getBasket();
        if ($basket !== null) {
            $user = Yii::$app->user->identity;
            $phones = $basket->getPhones();
            if (count($phones) !== 0) {
                $cost = 0;
                foreach ($phones as $phone) {
                    if ($phone->count > 0)
                        $cost += $phone->cost;
                }
                if ($user->score < $cost)
                    return ['result' => 'Не достаточно денег на счету'];

                $user->score -= $cost;
                $user->save(false);
                foreach ($phones as $phone) {
                    if ($phone->count > 0) {
                        $phone->count -= 1;
                        $phone->save(false);
                    }
                }
            }
            $basket->delete();
        }
        return ['result' => 'Покупка успешна'];
    }

    private function createBasket()
    {
        $basket = $this->getBasket();
        if ($basket === null)
            return new Basket();
        else
            return $basket;
    }

    private function getBasket()
    {
        $user = Yii::$app->user->identity;
        $baskets = $user->getBaskets();
        if (count($baskets) !== 0)
            return $baskets[0];
        return null;
    }

    public function actionAppend_Phone($id)
    {
        $basket = $this->createBasket();
        $phone = Phone::findOne($id);
        if ($phone !== null) {
            $basket->save();
            $phone_basket = new PhoneBasket();
            $phone_basket->phone_id = $id;
            $phone_basket->basket_id = $basket->id;
            $phone_basket->save();
        }
    }

    public function actionRemove_Phone($id)
    {
        $basket = $this->getBasket();
        if ($basket !== null) {
            $phone_basket = $basket->getPhoneBasket($id);
            if ($phone_basket !== null)
                $phone_basket->delete();
        }
    }

    public function actionGet_Phones()
    {
        $basket = $this->getBasket();
        if ($basket !== null)
            return $basket->getPhones();
        else
            return [];
    }


}