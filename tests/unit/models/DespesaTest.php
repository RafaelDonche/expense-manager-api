<?php


namespace Unit;

use app\models\Despesa;
use \UnitTester;

class DespesaTest extends \Codeception\Test\Unit
{

    protected UnitTester $tester;

    /**
     * Testa se a validação falha corretamente quando um campo obrigatório está faltando.
     */
    public function testValidacaoFalhaComDadosFaltando()
    {
        $despesa = new Despesa();
        // Deixamos a 'descricao' vazia de propósito
        $despesa->categoria = 'lazer';
        $despesa->valor = 100;
        $despesa->data = '10/09/2025';

        // Verificamos que o método validate() retorna falso
        $this->assertFalse($despesa->validate(), 'A validação deveria falhar sem a descrição.');
        // E verificamos que existe uma mensagem de erro para o campo 'descricao'
        $this->assertTrue($despesa->hasErrors('descricao'), 'Deveria haver um erro no campo de descrição.');
    }

    /**
     * Testa se a validação passa quando todos os dados estão corretos.
     */
    public function testValidacaoPassaComDadosCorretos()
    {
        $despesa = new Despesa();
        $despesa->descricao = 'Ingresso para o cinema';
        $despesa->categoria = 'Lazer';
        $despesa->valor = 50.50;
        $despesa->data = '10/09/2025';

        // Verificamos que o método validate() retorna verdadeiro
        $this->assertTrue($despesa->validate(), 'A validação deveria passar com os dados corretos.');
    }
}
