<?php

namespace tests\unit\models;

use app\models\User;

class UserTest extends \Codeception\Test\Unit
{
    /**
     * Testa se a validação falha com dados inválidos ou ausentes.
     */
    public function testValidacaoFalhaComDadosInvalidos()
    {
        $user = new User();

        // 1. Testa campo obrigatório (email)
        $user->validate();
        $this->assertTrue($user->hasErrors('email'), 'Deveria haver um erro no campo de email ao estar vazio.');

        // 2. Testa formato de email inválido
        $user->email = 'email-invalido';
        $user->validate();
        $this->assertTrue($user->hasErrors('email'), 'Deveria haver um erro de formato de email.');
    }

    /**
     * Testa se a validação passa quando os dados estão corretos.
     * Este teste não verifica a unicidade do email, pois isso exigiria uma conexão com o banco de dados.
     */
    public function testValidacaoPassaComDadosCorretos()
    {
        $user = new User();
        $user->email = 'teste@exemplo.com';
        $user->setPassword('senha123');
        $user->generateAuthKey();
        
        $this->assertTrue($user->validate(), 'A validação deveria passar com dados corretos.');
    }

    /**
     * Testa se a definição de senha e a validação da mesma funcionam como esperado.
     */
    public function testSenhaEHashesFuncionamCorretamente()
    {
        $user = new User();
        $senhaPura = 'senhaSuperSecreta';

        // Define a senha
        $user->setPassword($senhaPura);

        // 1. Verifica se o hash foi gerado e é diferente da senha pura
        $this->assertNotEmpty($user->password_hash, 'O hash da senha não deveria estar vazio.');
        $this->assertNotEquals($senhaPura, $user->password_hash, 'O hash deve ser diferente da senha pura.');

        // 2. Verifica se a validação funciona com a senha correta
        $this->assertTrue($user->validatePassword($senhaPura), 'A validação deveria retornar verdadeiro para a senha correta.');

        // 3. Verifica se a validação falha com a senha errada
        $this->assertFalse($user->validatePassword('senhaErrada123'), 'A validação deveria retornar falso para a senha incorreta.');
    }
}
