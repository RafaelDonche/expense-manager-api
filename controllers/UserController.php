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
        
        if (empty($model->email) || empty(Yii::$app->request->post('password'))) {
            Yii::$app->response->statusCode = 400; // Bad Request
            return ['error' => 'Email and password are required.'];
        }

        $model->setPassword(Yii::$app->request->post('password'));
        $model->generateAuthKey();

        if ($model->save()) {
            Yii::$app->response->statusCode = 201; // Created
            return [
                'message' => 'User created successfully!',
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
        $password = Yii::$app->request->post('password');

        $user = User::findByEmail($email);

        if ($user && $user->validatePassword($password)) {
            $secretKey = Yii::$app->params['jwtSecretKey'];
            $issuer = "http://localhost:8080";
            $audience = "http://localhost:8080";
            $issuedAt = time();
            $expire = $issuedAt + 3600; // Token expira em 1 hora

            $payload = [
                'iss' => $issuer,
                'aud' => $audience,
                'iat' => $issuedAt,
                'exp' => $expire,
                'uid' => $user->getId(),
            ];

            $token = JWT::encode($payload, $secretKey, 'HS256');

            return [
                'message' => 'User authenticated successfully!',
                'token' => $token
            ];
        }

        Yii::$app->response->statusCode = 401; // Unauthorized
        return ['error' => 'Invalid credentials.'];
    }
}