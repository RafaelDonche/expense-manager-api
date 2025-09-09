<?php

namespace app\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\Url;
use yii\web\Link;
use yii\web\Linkable;

/**
 * This is the model class for table "expense".
 *
 * @property int $id
 * @property int $user_id
 * @property string $description
 * @property string $category
 * @property float $value
 * @property string $expense_date
 * @property int $created_at
 * @property int $updated_at
 *
 * @property User $user
 */
class Expense extends ActiveRecord implements Linkable
{
    public static function tableName()
    {
        return 'expense';
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
            [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'user_id',
                'updatedByAttribute' => null,
            ],
        ];
    }

    public function rules()
    {
        return [
            [['description', 'category', 'value', 'expense_date'], 'required'],
            [['value'], 'number'],
            [['expense_date'], 'date', 'format' => 'php:Y-m-d'],
            [['description'], 'string', 'max' => 255],
            [['category'], 'in', 'range' => ['alimentação', 'transporte', 'lazer']],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * Garante que os dados extras (como o nome do usuário) não sejam expostos na API
     */
    public function fields()
    {
        return [
            'id',
            'description',
            'category',
            'value',
            'expense_date',
        ];
    }

    public function getLinks()
    {
        return [
            Link::REL_SELF => Url::to(['expenses/'.$this->id], true),
        ];
    }
}