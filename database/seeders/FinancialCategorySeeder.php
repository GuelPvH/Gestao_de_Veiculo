<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\FinancialTransactionType;
use App\Models\FinancialCategory;
use Illuminate\Database\Seeder;

final class FinancialCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Receita de projeto', 'slug' => 'receita-projeto', 'type' => FinancialTransactionType::Income],
            ['name' => 'Hospedagem', 'slug' => 'hospedagem', 'type' => FinancialTransactionType::Expense],
            ['name' => 'Domínio', 'slug' => 'dominio', 'type' => FinancialTransactionType::Expense],
            ['name' => 'Software', 'slug' => 'software', 'type' => FinancialTransactionType::Expense],
            ['name' => 'Marketing', 'slug' => 'marketing', 'type' => FinancialTransactionType::Expense],
            ['name' => 'Impostos', 'slug' => 'impostos', 'type' => FinancialTransactionType::Expense],
            ['name' => 'Infraestrutura', 'slug' => 'infraestrutura', 'type' => FinancialTransactionType::Expense],
        ];

        foreach ($categories as $category) {
            FinancialCategory::updateOrCreate(['slug' => $category['slug']], $category);
        }
    }
}
