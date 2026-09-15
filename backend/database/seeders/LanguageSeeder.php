<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Language;

class LanguageSeeder extends Seeder
{
    public function run(): void
    {
        Language::firstOrCreate(
            ['code' => 'es'],
            ['name' => 'Spanish', 'native_name' => 'Español', 'script' => 'Latn', 'is_active' => true]
        );

        Language::firstOrCreate(
            ['code' => 'en'],
            ['name' => 'English', 'native_name' => 'English', 'script' => 'Latn', 'is_active' => true]
        );

        Language::firstOrCreate(
            ['code' => 'qu'],
            ['name' => 'Quechua', 'native_name' => 'Runa Simi', 'script' => 'Latn', 'is_active' => true]
        );

        Language::firstOrCreate(
            ['code' => 'ay'],
            ['name' => 'Aymara', 'native_name' => 'Aymar aru', 'script' => 'Latn', 'is_active' => true]
        );

        Language::firstOrCreate(
            ['code' => 'fr'],
            ['name' => 'French', 'native_name' => 'Français', 'script' => 'Latn', 'is_active' => true]
        );

        Language::firstOrCreate(
            ['code' => 'de'],
            ['name' => 'German', 'native_name' => 'Deutsch', 'script' => 'Latn', 'is_active' => true]
        );

        Language::firstOrCreate(
            ['code' => 'pt'],
            ['name' => 'Portuguese', 'native_name' => 'Português', 'script' => 'Latn', 'is_active' => true]
        );

        Language::firstOrCreate(
            ['code' => 'la'],
            ['name' => 'Latin', 'native_name' => 'Lingua Latina', 'script' => 'Latn', 'is_active' => true]
        );

        Language::firstOrCreate(
            ['code' => 'it'],
            ['name' => 'Italian', 'native_name' => 'Italiano', 'script' => 'Latn', 'is_active' => true]
        );

        Language::firstOrCreate(
            ['code' => 'ru'],
            ['name' => 'Russian', 'native_name' => 'Русский', 'script' => 'Cyrl', 'is_active' => true]
        );

        Language::firstOrCreate(
            ['code' => 'zh'],
            ['name' => 'Chinese', 'native_name' => '中文', 'script' => 'Hans', 'is_active' => true]
        );

        Language::firstOrCreate(
            ['code' => 'ja'],
            ['name' => 'Japanese', 'native_name' => '日本語', 'script' => 'Jpan', 'is_active' => true]
        );
    }
}
