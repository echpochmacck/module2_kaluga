<?php

namespace app\modules\api\controllers;

use app\models\Category;
use app\models\File;
use app\models\Order;
use app\models\Product;
use app\models\Role;
use app\models\Status;
use app\models\User;
use Yii;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\Cors;

class OrderController extends \yii\rest\ActiveController
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
                'get-order' => [
                    'Access-Control-Allow-Credentials' => true,
                ]
            ]
        ];

        $auth = [
            'class' => HttpBearerAuth::class,
            'only' => ['get-order']
        ];
        // re-add authentication filter
        $behaviors['authenticator'] = $auth;
        // avoid authentication on CORS-pre-flight requests (HTTP OPTIONS method)
        $behaviors['authenticator']['except'] = ['options'];

        return $behaviors;
    }


    public function actionGetCategories()
    {
        return [
            'data' => Category::find()->asArray()->all()
        ];
    }

    public function actionGetOrders()
    {
        $orders = Order::find()
            ->select(['order.id as id', 'status.name as status'])
            ->innerJoin('status', 'status.id = order.status_id')
            ->asArray()->all();
        $result = [];
        foreach ($orders as $order) {
            $str = "CONCAT('" . Yii::$app->request->getHostInfo() . "/images/', image_url) as image_url";
            $model = Order::findOne($order['id']);
            $product = Product::findOne($model->product_id);
            $files = File::find()
                ->select([$str])
                ->where(['product_id' => $model->product_id, 'default' => 0])
                ->asArray()->all();
            $files = array_map(fn($file) => $file['image_url'], $files);
            $defaultImg = File::find()->select([$str])->where(['product_id' => $model->product_id, 'default' => 1])->asArray()->one();

            $order['product'] = [
                    'id' => $product->id,
                    'name' => $product->name,
                    'description' => $product->description,
                    'price' => $product->price,
                    'default_img' => $defaultImg ? array_values($defaultImg) : '',
                    'image_urls' => $files,
            ];
            $result[] = $order;

        }

        return $this->asJson($result);


    }

    public function actoinHook($id)
    {
//       $product = Product::findOne($id);
//       if ($product) {
//            $order= new Order();
//            $order->user_id = Yii::$app->user->identity->id;
//            $order->product_id = $product->id;
//            $order->status_id = Status::getStatusId('не оплачено');
//
//       } else {
//           Yii::$app->response->statusCode = 404;
//           return'';
//       }
    }

}
