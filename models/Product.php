<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "product".
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property float $price
 * @property int $category_id
 *
 * @property Category $category
 * @property File[] $files
 * @property Order[] $orders
 */
class Product extends \yii\db\ActiveRecord
{
    const SCENARIO_CREATE = 'create';
    const SCENARIO_CHECKFILE = 'check-file';

    public $array_files = [];
    public $select_img ;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'product';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'price', 'category_id'], 'safe', 'on' => self::SCENARIO_CHECKFILE ],
            [['name', 'price', 'category_id'], 'required', 'on' => self::SCENARIO_CREATE ],
            [['price'], 'number'],
            [['category_id', 'select_img'], 'integer'],
            ['price', 'match', 'pattern' => '/^[\d]{2}\.[\d]{2}$/', 'message' => 'формат xx.xx'],
            ['array_files', 'file', 'maxFiles' => 5, 'extensions' => 'png, jpg, jpeg', 'maxSize' => 1024 * 1024 * 3.5, 'skipOnEmpty' => true, 'on' => self::SCENARIO_CREATE],
            ['array_files', 'file', 'maxFiles' => 5, 'extensions' => 'png, jpg', 'maxSize' => 1024 * 1024 * 3.5, 'skipOnEmpty' => true, 'on' => self::SCENARIO_CHECKFILE],
            [['name', 'description'], 'string', 'max' => 255],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => Category::class, 'targetAttribute' => ['category_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'description' => 'Description',
            'price' => 'Price',
            'category_id' => 'Category ID',
        ];
    }

    /**
     * Gets query for [[Category]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Category::class, ['id' => 'category_id']);
    }

    /**
     * Gets query for [[Files]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFiles()
    {
        return $this->hasMany(File::class, ['product_id' => 'id']);
    }

    /**
     * Gets query for [[Orders]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrders()
    {
        return $this->hasMany(Order::class, ['product_id' => 'id']);
    }

    public function upload($file, $folder)
    {
        $file->saveAs('temp/' . $file->name);

        $img = file_get_contents('temp/' . $file->name);
        $im = imagecreatefromstring($img);

        $width = imagesx($im);
        $height = imagesy($im);
        $maxWidth = 300;
        $maxHeight = 300;

        $ratioWidth = $maxWidth / $width;
        $ratioHeight = $maxWidth / $width;

        $ratio = min($ratioWidth, $ratioHeight);
        $newWidth = (int)($ratio * $width);
        $newHeight = (int)($ratio * $height);

        $thumb = imagecreatetruecolor($newWidth, $newHeight);
        imagecopyresampled($thumb, $im, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        $path = Yii::$app->security->generateRandomString() . ".jpeg"  ;
        imagejpeg($thumb, "$folder/" . $path);
        imagedestroy($thumb);
        imagedestroy($im);
        return $path;
    }

}
