<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%expense}}`.
 */
class m250909_134522_create_expense_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%expense}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'description' => $this->string()->notNull(),
            'category' => "ENUM('alimentação', 'transporte', 'lazer') NOT NULL",
            'value' => $this->decimal(10, 2)->notNull(),
            'expense_date' => $this->date()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);

        // Cria um índice para a coluna `user_id`
        $this->createIndex(
            'idx-expense-user_id',
            '{{%expense}}',
            'user_id'
        );

        // Adiciona a chave estrangeira para a tabela `user`
        $this->addForeignKey(
            'fk-expense-user_id',
            '{{%expense}}',
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
            'fk-expense-user_id',
            '{{%expense}}'
        );

        // Remove o índice
        $this->dropIndex(
            'idx-expense-user_id',
            '{{%expense}}'
        );

        $this->dropTable('{{%expense}}');
    }
}
