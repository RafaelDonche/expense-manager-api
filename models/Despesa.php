<?php

namespace app\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\helpers\Url;
use yii\web\Link;
use yii\web\Linkable;

/**
 * This is the model class for table "despesa".
 *
 * @property int $id
 * @property int $user_id
 * @property string $description
 * @property string $category
 * @property float $value
 * @property string $data
 * @property int $created_at
 * @property int $updated_at
 *
 * @property User $user
 */
class Despesa extends ActiveRecord implements Linkable
{
    public static function tableName()
    {
        return 'despesa';
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'value' => new Expression('NOW()'),
            ],
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
            [['descricao'], 'required', 'message' => 'O campo "descricao" é obrigatório.'],
            [['categoria'], 'required', 'message' => 'O campo "categoria" é obrigatório.'],
            [['valor'], 'required', 'message' => 'O campo "valor" é obrigatório.'],
            [['data'], 'required', 'message' => 'O campo "data" é obrigatório.'],
            [['valor'], 'number', 'message' => 'O campo "valor" deve ser um número.'],
            [['data'], 'date', 'format' => 'php:d/m/Y', 'message' => 'O campo "data" deve ser uma data válida no formato dia/mês/ano.'],
            [['descricao'], 'string', 'max' => 255, 'message' => 'O campo "descricao" deve ter no máximo 255 caracteres.'],
            [
                ['categoria'], 
                'in', 
                'range' => ['Alimentação', 'Transporte', 'Lazer'], 
                'message' => 'O campo "categoria" deve ser uma das seguintes opções: Alimentação; Transporte; Lazer.'
            ],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function fields()
    {
        $fields = [
            'id',
            'descricao',
            'categoria',
            'valor',
            'data',
        ];

        if (Yii::$app->controller->action->id == 'view') {
            $fields['criado_em'] = function ($model) {
                return Yii::$app->formatter->asDatetime($model->created_at, 'php:d/m/Y H:i:s');
            };
            $fields['atualizado_em'] = function ($model) {
                return Yii::$app->formatter->asDatetime($model->updated_at, 'php:d/m/Y H:i:s');
            };
        }

        return $fields;
    }

    public function getLinks()
    {
        return [
            Link::REL_SELF => Url::to(['despesas/'.$this->id], true),
        ];
    }

    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }
        $dateObject = \DateTime::createFromFormat('d/m/Y', $this->data);
        if ($dateObject) {
            $this->data = $dateObject->format('Y-m-d');
        }
        return true;
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        $dateObject = \DateTime::createFromFormat('Y-m-d', $this->data);
        if ($dateObject) {
            $this->data = $dateObject->format('d/m/Y');
        }
    }

    public function afterFind()
    {
        parent::afterFind();
        $dateObject = \DateTime::createFromFormat('Y-m-d', $this->data);
        if ($dateObject) {
            $this->data = $dateObject->format('d/m/Y');
        }
    }
}