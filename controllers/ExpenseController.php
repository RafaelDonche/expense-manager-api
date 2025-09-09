<?php

namespace app\controllers;

use app\models\Expense;
use yii\rest\ActiveController;
use yii\filters\auth\HttpBearerAuth;
use Yii;
use yii\data\ActiveDataProvider;

class ExpenseController extends ActiveController
{
    public $modelClass = 'app\models\Expense';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
        ];
        return $behaviors;
    }

    public function checkAccess($action, $model = null, $params = [])
    {
        // Verifica se o usuário logado é o dono do modelo
        if ($action === 'view' || $action === 'update' || $action === 'delete') {
            if ($model->user_id !== Yii::$app->user->id) {
                throw new \yii\web\ForbiddenHttpException('You are not allowed to access this resource.');
            }
        }
    }

    public function actions()
    {
        $actions = parent::actions();

        // customiza a preparação do  data provider com o método "prepareDataProvider()"
        $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];

        return $actions;
    }
    
    public function prepareDataProvider()
    {
        $query = $this->modelClass::find()->where(['user_id' => Yii::$app->user->id]);
        $query->where(['user_id' => Yii::$app->user->id]);

        // Filtro por categoria
        $category = Yii::$app->request->get('category');

        if ($category) {
            $query->andWhere(['category' => $category]);
        }

        // Filtro por período (dia, mês e ano)
        $year = Yii::$app->request->get('year');
        $month = Yii::$app->request->get('month');
        $day = Yii::$app->request->get('day');
        if ($year) {
            $query->andWhere(['YEAR(expense_date)' => $year]);
        }
        if ($month) {
            $query->andWhere(['MONTH(expense_date)' => $month]);
        }
        if ($day) {
            $query->andWhere(['DAY(expense_date)' => $day]);
        }

        $orderColumn = Yii::$app->request->get('order_by_column', 'expense_date');
        $orderType =  Yii::$app->request->get('order_by_type') == 'SORT_DESC' ? SORT_DESC : SORT_ASC;
        $currentPage = Yii::$app->request->get('page', 0);

       return new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    $orderColumn => $orderType,
                    'created_at' => SORT_DESC
                ],
            ],
            'pagination' => [
                'pageSize' => 10,
                'page' => $currentPage,
            ],
        ]);
    }
    
    /**
     * Sobrescreve o método afterAction para padronizar todas as respostas de sucesso da API.
     */
    public function afterAction($action, $result)
    {
        $result = parent::afterAction($action, $result);

        // Verifica se a resposta foi bem-sucedida (status code 2xx)
        // O status de delete (204) também será capturado aqui.
        if (Yii::$app->response->isSuccessful) {
            $data = $result;
            $message = '';

            // Define uma mensagem padrão com base na ação executada
            switch ($action->id) {
                case 'create':
                    $message = 'Expense created successfully!';
                    break;
                case 'update':
                    $message = 'Expense updated successfully!';
                    break;
                case 'delete':
                    $message = 'Expense deleted successfully!';
                    $data = null;
                    Yii::$app->response->statusCode = 200;
                    break;
                case 'view':
                    $message = 'Expense retrieved successfully.';
                    break;
                case 'index':
                    $message = 'Expenses listed successfully.';
                    break;
            }

            return [
                'success' => true,
                'message' => $message,
                'data' => $data,
            ];
        }

        // Para respostas de erro, mantém o formato padrão do Yii, que já é bem estruturado.
        return $result;
    }
}