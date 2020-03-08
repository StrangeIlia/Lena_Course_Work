<?php


namespace app\modules\api\controllers;


use app\models\LoginForm;
use app\models\User;
use Yii;
use yii\filters\AccessControl;

class UsersController extends BaseController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['bearerAuth']['except'] = ['registration'];

        $behaviors['access'] = [
            'class' => AccessControl::className(),
            'rules' => [
                [
                    'actions' => ['registration', 'login', 'username'],
                    'allow' => true,
                    'roles' => ['?', '@'],
                ],
                [
                    'actions' => ['logout'],
                    'allow' => true,
                    'roles' => ['@'],
                ],
            ],
        ];

        return $behaviors;
    }

    public function actionRegistration()
    {
        $user = new User();
        if ($user->load(Yii::$app->request->post(), '')) {
            if ($user->save()) {
                return [
                    'access_token' => $user->access_token,
                ];
            } elseif (User::findOne(['username' => $user->username])) {
                return [
                    'error' => 'Пользователь с таким логином уже существует',
                ];
            } elseif (User::findOne(['email' => $user->email])) {
                return [
                    'error' => 'Данная электронная почта уже привязана к другому аккаунту',
                ];
            } elseif (!$user->checkPasswordLength()) {
                return [
                    'error' => 'Пароль меньше требуемой длины (минимальная длина' . $user->getMinLengthPassword() . ' символов)',
                ];
            } else {
                return [
                    'error' => 'Неизвестная ошибка',
                ];
            }
        }

        return [
            'error' => 'Неверная форма отправки данных',
        ];
    }

    public function actionLogin($username, $password)
    {
        $model = new LoginForm();
        $user = User::findOne($username);
        if ($model->load(Yii::$app->request->post(), '') && $model->login()) {
            return ['access_token' => $model->getUser()['access_token']];
        }
        return ['error' => 'Неверный логин или пароль'];
    }

    public function actionUsername($id)
    {
        $user = null;
        if ($id === null)
            $user = Yii::$app->user->identity;
        else
            $user = User::findOne($id);

        if ($user === null)
            return ['error' => 'Не найден пользователь с таким id'];
        else
            return ['username' => $user->username];
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();
    }
}