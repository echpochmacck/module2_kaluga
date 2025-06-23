<?php

namespace app\modules\api\controllers;

use app\models\Role;
use app\models\User;
use Yii;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\Cors;

class UserController extends \yii\rest\ActiveController
{
    public $modelClass = '';
    public $enableCsrfValidation = '';

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        // remove authentication filter
        $auth = $behaviors['authenticator'];
        unset($behaviors['authenticator']);

        // add CORS filter
        $behaviors['corsFilter'] = [
            'class' => Cors::class,
            'cors' => [
                'Origin' => [((isset($_SERVER['HTTP_ORIGIN'])) ? $_SERVER['HTTP_ORIGIN'] : 'http://' . $_SERVER['REMOTE_ADDR'])],
                'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
                'Access-Control-Request-Headers' => ['*'],
            ],
            'actions' => [
                'logout' => [
                    'Access-Control-Allow-Credentials' => true,
                ]
            ]
        ];

        $auth = [
            'class' => HttpBearerAuth::class,
            'only' => ['logout']
        ];
        // re-add authentication filter
        $behaviors['authenticator'] = $auth;
        // avoid authentication on CORS-pre-flight requests (HTTP OPTIONS method)
        $behaviors['authenticator']['except'] = ['options'];

        return $behaviors;
    }


    public function actionRegister()
    {

        $model = new User();
        $model->load(Yii::$app->getRequest()->getBodyParams(), '');
        $model->scenario = 'register';
        if ($model->validate()) {
            $model->password = Yii::$app->getSecurity()->generatePasswordHash($model->password);
            $model->role_id = Role::getRoleId('user');
            $model->save(false);
            Yii::$app->response->statusCode = 201;
            return [
                'success' => true
            ];
        } else {
            Yii::$app->response->statusCode = 422;
            return $this->asJson([
                'errors' => $model->errors
            ]);
        }
    }

    public function actionLogin()
    {

        $model = new User();
        $model->load(Yii::$app->getRequest()->getBodyParams(), '');
        if ($model->validate()) {
            $user = User::findOne(['email' => $model->email]);
            if ($user && $user->validatePassword($model->password)) {
                $user->token = Yii::$app->getSecurity()->generateRandomString();

                $user->save(false);
                return [
                    'token' => $user->token
                ];
            } else {
                Yii::$app->response->statusCode = 422;
                return $this->asJson([
                    'errors' => [
                        'email' => [
                            'Invalid data'

                        ],
                    ]
                ]);
            }
        } else {
            Yii::$app->response->statusCode = 422;
            return $this->asJson([
                'errors' => $model->errors
            ]);
        }
    }


}
