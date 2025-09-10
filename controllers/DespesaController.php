<?php

namespace app\controllers;

use app\models\Despesa;
use yii\rest\ActiveController;
use yii\filters\auth\HttpBearerAuth;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Query;

use function Psy\debug;

class DespesaController extends ActiveController
{
    public $modelClass = 'app\models\Despesa';

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
                throw new \yii\web\ForbiddenHttpException('Você não pode acessar este recurso.');
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
        $categoria = Yii::$app->request->get('categoria');

        if ($categoria) {
            $query->andWhere(['categoria' => $categoria]);
        }

        // Filtro por período (dia, mês e ano)
        $ano = Yii::$app->request->get('ano');
        $mes = Yii::$app->request->get('mes');
        $dia = Yii::$app->request->get('dia');
            
        if ($ano !== null && (!is_numeric($ano))) {
            throw new \yii\web\BadRequestHttpException('Ano inválido.');
        }
        if ($mes !== null && (!is_numeric($mes) || $mes < 1 || $mes > 12)) {
            throw new \yii\web\BadRequestHttpException('Mês inválido.');
        }
        if ($dia !== null && (!is_numeric($dia) || $dia < 1 || $dia > 31)) {
            throw new \yii\web\BadRequestHttpException('Dia inválido.');
        }

        if ($ano) {
            $query->andWhere(['YEAR(data)' => $ano]);
        }
        if ($mes) {
            $query->andWhere(['MONTH(data)' => $mes]);
        }
        if ($dia) {
            $query->andWhere(['DAY(data)' => $dia]);
        }

        $orderColumn = Yii::$app->request->get('coluna_ordenacao', 'data');
        $orderType =  Yii::$app->request->get('tipo_ordenacao') == 'SORT_DESC' ? SORT_DESC : SORT_ASC;
        $currentPage = Yii::$app->request->get('pagina', 0);

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
                'page' => max(0, $currentPage - 1),
            ],
        ]);
    }
    
    /**
     * Sobrescreve o método afterAction para padronizar todas as respostas de sucesso da API.
     */
    public function afterAction($action, $result)
    {
        $rawResult = $result;
        $result = parent::afterAction($action, $result);

        if (Yii::$app->response->isSuccessful) {
            $data = $result;
            $message = '';
            switch ($action->id) {
                case 'create':
                    $message = 'Despesa criada com sucesso!';
                    break;
                case 'update':
                    $message = 'Despesa atualizada com sucesso!';
                    break;
                case 'delete':
                    $message = 'Despesa excluída com sucesso!';
                    $data = null;
                    Yii::$app->response->statusCode = 200;
                    break;
                case 'view':
                    $message = 'Despesa recuperada com sucesso.';
                    break;
                case 'index':
                    $message = 'Despesas listadas com sucesso.';
                    if (!empty($data)) {
                        $pagination = [
                            'total_items' => (int) $rawResult->pagination->totalCount,
                            'total_paginas' => $rawResult->pagination->getPageCount(),
                            'pagina_atual' => $rawResult->pagination->getPage() + 1,
                            'items_por_pagina' => $rawResult->pagination->getPageSize(),
                        ];
                    }
                    break;
            }

            $return = [
                'sucesso' => true,
                'mensagem' => $message,
            ];

            if (isset($pagination)) {
                $return['_paginacao'] = $pagination;
            }
            
            $return['dados'] = $data;

            return $return;
        }

        return $result;
    }
}