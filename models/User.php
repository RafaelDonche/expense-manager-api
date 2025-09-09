<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use yii\behaviors\TimestampBehavior;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;

class User extends ActiveRecord implements IdentityInterface
{
    public static function tableName()
    {
        return '{{%user}}';
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    public function rules()
    {
        return [
            [['email', 'password_hash', 'auth_key'], 'required'],
            [['email'], 'email'],
            [['email'], 'unique'],
        ];
    }

    // Métodos da IdentityInterface
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        $secretKey = Yii::$app->params['jwtSecretKey'];

        try {
            $decoded = JWT::decode($token, new Key($secretKey, 'HS256'));
            // Procura o usuário pelo ID contido no token
            return static::findOne(['id' => $decoded->uid]);
        } catch (Exception $e) {
            // Se o token for inválido (expirado, assinatura incorreta, etc), retorna null
            return null;
        }
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAuthKey()
    {
        return $this->auth_key;
    }

    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    // Métodos de ajuda para senhas e authKey
    public static function findByEmail($email)
    {
        return static::findOne(['email' => $email]);
    }

    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }
}