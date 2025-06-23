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

class ProductController extends \yii\rest\ActiveController
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
                'new-order' => [
                    'Access-Control-Allow-Credentials' => true,
                ]
            ]
        ];

        $auth = [
            'class' => HttpBearerAuth::class,
            'only' => ['new-order']
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

    public function actionGetCategoryProducts($id)
    {
        $products = Product::find()
            ->select(['id', 'name', 'description', 'price'])
            ->where(['category_id' => $id])
            ->asArray()->all();
        $resultProducts = [];
        if ($products) {
            foreach ($products as $product) {
                $str = "CONCAT('" . Yii::$app->request->getHostInfo() . "/images/', image_url) as image_url";
                $files = File::find()
                    ->select([$str])
                    ->where(['product_id' => $product['id'], 'default' => 0])
                    ->asArray()->all();
                $files = array_map(fn($file) => $file['image_url'], $files);
                $product['image_urls'] = $files;
                $defaultImg = File::find()->select([$str])->where(['product_id' => $product['id'], 'default' => 1])->asArray()->one();

                $product['default_img'] = $defaultImg ? array_values(
                    $defaultImg
                ) : '';
                $resultProducts[] = $product;

            }
            return [
                'data' => $resultProducts
            ];

        } else {
            return [
                'data' => $products
            ];
        }

    }

    public function actionGetProduct($id)
    {
        $product = Product::findOne($id);
        if ($product) {
            $str = "CONCAT('" . Yii::$app->request->getHostInfo() . "/images/', image_url) as image_url";

            $files = File::find()
                ->select([$str])
                ->where(['product_id' => $product->id, 'default' => 0])
                ->asArray()->all();
            $files = array_map(fn($file) => $file['image_url'], $files);
            $defaultImg = File::find()->select([$str])->where(['product_id' => $product['id'], 'default' => 1])->asArray()->one();

            return $this->asJson([
                'data' => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'description' => $product->description,
                    'price' => $product->price,
                    'default_img' => $defaultImg ? array_values($defaultImg) : '',
                    'image_urls' => $files,
                ]
            ]);
        } else {
            Yii::$app->response->statusCode = 404;
            return '';
        }
    }
//    public function actionTest()
//    {
//
//        $curl = curl_init();
//        curl_setopt($curl, CURLOPT_URL, $target);
//        curl_setopt($curl, CURLOPT_USERAGENT,'Opera/9.80 (Windows NT 6.2; Win64; x64) Presto/2.12.388 Version/12.15');
//        curl_setopt($curl, CURLOPT_HTTPHEADER,array('User-Agent: Opera/9.80 (Windows NT 6.2; Win64; x64) Presto/2.12.388 Version/12.15','Referer: http://someaddress.tld','Content-Type: multipart/form-data'));
//        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // stop verifying certificate
//        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
//        curl_setopt($curl, CURLOPT_POST, true); // enable posting
//        curl_setopt($curl, CURLOPT_POSTFIELDS, $imgdata); // post images
//        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true); // if any redirection after upload
//        $r = curl_exec($curl);
//        curl_close($curl);
//    }

    public function actionNewOrder($id)
    {
        $product = Product::findOne($id);
        if ($product) {
            $order = new Order();
            $order->user_id = Yii::$app->user->identity->id;
            $order->product_id = $product->id;
            $order->status_id = Status::getStatusId('не оплачено');

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'http://ejdamjn-m2.web.ru/public/api/payments',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS =>'{
    "price" : 12,
    "webhook_url":"http://vfresri-m2.web.ru/api/payment-webhook/"
}',
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json'
                ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);
            var_dump($response);
            die;
        } else {
            Yii::$app->response->statusCode = 404;
            return '';
        }
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
