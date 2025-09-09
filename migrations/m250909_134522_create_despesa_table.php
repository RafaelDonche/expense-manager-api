<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%despesa}}`.
 */
class m250909_134522_create_despesa_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%despesa}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'descricao' => $this->string()->notNull(),
            'categoria' => "ENUM('Alimentação', 'Transporte', 'Lazer') NOT NULL",
            'valor' => $this->decimal(10, 2)->notNull(),
            'data' => $this->date()->notNull(),
            'created_at' => $this->dateTime()->notNull(),
            'updated_at' => $this->dateTime()->notNull(),
        ]);

        // Cria um índice para a coluna `user_id`
        $this->createIndex(
            'idx-despesa-user_id',
            '{{%despesa}}',
            'user_id'
        );

        // Adiciona a chave estrangeira para a tabela `user`
        $this->addForeignKey(
            'fk-despesa-user_id',
            '{{%despesa}}',
            'user_id',
            '{{%user}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Remove a chave estrangeira primeiro
        $this->dropForeignKey(
            'fk-despesa-user_id',
            '{{%despesa}}'
        );

        // Remove o índice
        $this->dropIndex(
            'idx-despesa-user_id',
            '{{%despesa}}'
        );

        $this->dropTable('{{%despesa}}');
    }
}
