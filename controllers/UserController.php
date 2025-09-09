<?php

namespace app\controllers;

use Yii;
use app\models\User;
use yii\rest\Controller;
use \Firebase\JWT\JWT;

class UserController extends Controller
{
    public function actionRegister()
    {
        $model = new User();
        
        $model->load(Yii::$app->request->post(), '');

        $model->setPassword(Yii::$app->request->post('senha'));
        $model->generateAuthKey();

        if ($model->save()) {
            Yii::$app->response->statusCode = 201; // Created
            return [
                'message' => 'Usuário criado com sucesso!',
                'id' => $model->id,
                'email' => $model->email,
            ];
        } else {
            Yii::$app->response->statusCode = 422; // Unprocessable Entity
            return ['errors' => $model->getErrors()];
        }
    }

    public function actionLogin()
    {
        $email = Yii::$app->request->post('email');
        $password = Yii::$app->request->post('senha');

        $user = User::findByEmail($email);

        if ($user && $user->validatePassword($password)) {
            $secretKey = Yii::$app->params['jwtSecretKey'];
            $issuer = "http://localhost:8080";
            $audience = "http://localhost:8080";
            $issuedAt = time();
            $expire = $issuedAt + 86400; // Token expira em 24 horas

            $payload = [
                'iss' => $issuer,
                'aud' => $audience,
                'iat' => $issuedAt,
                'exp' => $expire,
                'uid' => $user->getId(),
            ];

            $token = JWT::encode($payload, $secretKey, 'HS256');

            return [
                'message' => 'Usuário autenticado com sucesso!',
                'token' => $token
            ];
        }

        Yii::$app->response->statusCode = 401; // Unauthorized
        return ['error' => 'Credenciais inválidas.'];
    }
}