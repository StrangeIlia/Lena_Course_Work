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
                    $purchase_phone = Phone::findOne($phone->id);
                    if ($purchase_phone->count >= $phone->count)
                        $cost += $phone->cost * $phone->count;
                    else
                        return [
                            'result' => 'Недостаточно телефонов следущего типа ' . $phone->mark .
                                '. Требуется ' . $phone->count . ', имеется ' . $purchase_phone->count
                        ];
                }
                if ($user->score < $cost)
                    return ['result' => 'Не достаточно денег на счету'];

                $user->score -= $cost;
                $user->save(false);
                foreach ($phones as $phone) {
                    $purchase_phone = Phone::findOne($phone->id);
                    $purchase_phone->count -= 1;
                    $purchase_phone->save(false);
                }
            }
            $basket->delete();
        }
        return ['result' => 'Покупка успешна'];
    }

    private function createBasket()
    {
        $basket = $this->getBasket();
        if ($basket === null) {
            $basket = new Basket();
            $basket->user_id = Yii::$app->user->identity->getId();
        }
        return $basket;
    }

    private function getBasket()
    {
        $user = Yii::$app->user->identity;
        return $user->getBaskets()->one();
    }

    public function actionAppend_phone($id)
    {
        $basket = $this->createBasket();
        $phone = Phone::findOne($id);
        if ($phone !== null) {

            $basket->save(false);
            $phone_basket = new PhoneBasket();
            $phone_basket->phone_id = $id;
            $phone_basket->basket_id = $basket->id;
            $phone_basket->save(false);
            return ['result' => 'Заказ успешно добавлен в корзину'];
        }
        return ['result' => 'Нет телефона с таким id'];
    }

    public function actionRemove_phone($id)
    {
        $basket = $this->getBasket();
        if ($basket !== null) {
            $phone_basket = $basket->getPhoneBasket($id);
            if ($phone_basket !== null)
                $phone_basket->delete();
            else
                return ['result' => 'Нет телефона с таким id'];
        }
        return ['result' => 'Заказ успешно удален из корзины'];
    }

    public function actionGet_phones()
    {
        $basket = $this->getBasket();
        if ($basket !== null)
            //return $basket->getPhoneBaskets()->all();
            //return $basket->getPhones()->all();
            return $basket->getPhones();
        else
            return [];
    }


}