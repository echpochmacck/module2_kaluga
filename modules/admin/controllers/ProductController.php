<?php

namespace app\modules\admin\controllers;

use app\models\File;
use app\models\Product;
use app\models\ProductSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

/**
 * ProductController implements the CRUD actions for Product model.
 */
class ProductController extends Controller
{
    /**
     * @inheritDoc
     */
//    public function behaviors()
//    {
//        return array_merge(
//            parent::behaviors(),
//            [
//                'verbs' => [
//                    'class' => VerbFilter::className(),
//                    'actions' => [
////                        'delete' => ['POST'],
//                    ],
//                ],
//            ]
//        );
//    }

    /**
     * Lists all Product models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new ProductSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Product model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
            'files' => File::find()->where(['product_id' => $id])->all(),
         ]);
    }

    public function actionCheckFile()
    {
        $model = new Product();
        $model->scenario = 'check-file';
        $model->array_files = UploadedFile::getInstances($model, 'array_files');
        $result = [];
        if ($model->validate()) {
            foreach ($model->array_files as $file) {
                $result[] = $model->upload($file, 'temp');
            }
            return $this->asJson($result);

        } else {

        }
    }

    /**
     * Creates a new Product model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Product();
        $model->scenario = 'create';
        if ($this->request->isPost) {
            if ($model->load($this->request->post())) {
                $model->array_files = UploadedFile::getInstances($model, 'array_files');
                $model->save(false);
                foreach ($model->array_files as $index => $file) {
                    $modelFile = new File();
                    $modelFile->product_id = $model->id;
                    $modelFile->image_url = $model->upload($file, 'images');
                    $modelFile->default = $model->select_img == $index ? 1 : 0;
                    $modelFile->save(false);
                }
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Product model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $files = File::find()->where(['product_id' => $id])->all();
        $model->array_files = UploadedFile::getInstances($model, 'array_files');

        if ($this->request->isPost && $model->load($this->request->post())) {
            $model->save(false);
            if ($model->array_files) {
                foreach ($files as $file) {
                    if (file_exists('/img/' . $file->image_url)) {
                        unlink('img/' . $file->image_url);
                    }
                    $file->delete();
                }
                foreach ($model->array_files as $index => $file) {
                    $modelFile = new File();
                    $modelFile->product_id = $model->id;
                    $modelFile->image_url = $model->upload($file, 'images');
                    $modelFile->default = $model->select_img == $index ? 1 : 0;
                    $modelFile->save(false);
                }

                $files = File::find()->where(['product_id' => $id])->all();

            }
        }

        return $this->render('update', [
            'model' => $model,
            'productFiles' => $files,
        ]);
    }

    /**
     * Deletes an existing Product model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id);
        $files = File::find()
            ->where(['product_id' => $id])
            ->all();
        if ($files) {
            foreach ($files as $file) {
                if (file_exists('/img/' . $file->image_url)) {
                    unlink('/img/' . $file->image_url);
                }
                $file->delete();
            }
        }
//        return $this->redirect(['index']);
    }

    /**
     * Finds the Product model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Product the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Product::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
