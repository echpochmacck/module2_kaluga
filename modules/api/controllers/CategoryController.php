<?php

namespace app\modules\api\controllers;

use app\models\Category;
use app\models\File;
use app\models\Product;
use app\models\Role;
use app\models\User;
use Yii;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\Cors;

class CategoryController extends \yii\rest\ActiveController
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
               $str = "CONCAT('".Yii::$app->request->getHostInfo(). "/images/', image_url) as image_url";
               $files = File::find()
                   ->select([$str])
                   ->where(['product_id' => $product['id'], 'default' =>0])
                   ->asArray()->all();
               $files = array_map(fn ($file)=>$file['image_url'], $files);
               $product['image_urls'] = $files;
               $defaultImg = File::find()->select([$str])->where(['product_id' => $product['id'], 'default' => 1])->asArray()->one();

               $product['default_img'] = $defaultImg ? array_values(
                   $defaultImg
               ) : '';
               $resultProducts[] = $product;

           }
           return$this->asJson([
               'data' => $resultProducts
           ]);

       } else {
           return $this->asJson([
               'data' => $products
           ]);
       }

   }

}
