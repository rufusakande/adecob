<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;

return new class extends Migration {
    /**
     * Chiffrement at-rest des champs sensibles :
     *  - users.telephone
     *  - communes.access_code_plain
     *  - infrastructures.numero_telephone
     *
     * On élargit les colonnes en TEXT (le ciphertext Laravel est plus long que
     * la donnée), puis on backfille les valeurs existantes en clair en les
     * chiffrant avec APP_KEY (Crypt::encryptString).
     */
    public function up(): void
    {
        // 1. Élargir les colonnes pour accueillir le ciphertext.
        Schema::table('users', function (Blueprint $table) {
            $table->text('telephone')->nullable()->change();
        });

        if (Schema::hasColumn('communes', 'access_code_plain')) {
            Schema::table('communes', function (Blueprint $table) {
                $table->text('access_code_plain')->nullable()->change();
            });
        }

        if (Schema::hasColumn('infrastructures', 'numero_telephone')) {
            Schema::table('infrastructures', function (Blueprint $table) {
                $table->text('numero_telephone')->nullable()->change();
            });
        }

        // 2. Backfill : chiffrer les valeurs existantes (texte clair) en place.
        $this->encryptColumn('users', 'telephone');

        if (Schema::hasColumn('communes', 'access_code_plain')) {
            $this->encryptColumn('communes', 'access_code_plain');
        }

        if (Schema::hasColumn('infrastructures', 'numero_telephone')) {
            $this->encryptColumn('infrastructures', 'numero_telephone');
        }
    }

    public function down(): void
    {
        // Déchiffrer pour rollback propre.
        $this->decryptColumn('users', 'telephone');

        if (Schema::hasColumn('communes', 'access_code_plain')) {
            $this->decryptColumn('communes', 'access_code_plain');
        }

        if (Schema::hasColumn('infrastructures', 'numero_telephone')) {
            $this->decryptColumn('infrastructures', 'numero_telephone');
        }

        Schema::table('users', function (Blueprint $table) {
            $table->string('telephone', 255)->nullable()->change();
        });
    }

    protected function encryptColumn(string $table, string $column): void
    {
        DB::table($table)->whereNotNull($column)->orderBy('id')->chunkById(200, function ($rows) use ($table, $column) {
            foreach ($rows as $row) {
                $value = $row->{$column};
                if ($value === null || $value === '') {
                    continue;
                }
                // Si déjà chiffré (préfixe base64 Laravel), on saute.
                try {
                    Crypt::decryptString($value);
                    continue;
                } catch (\Throwable $e) {
                    // pas encore chiffré → on chiffre
                }
                DB::table($table)->where('id', $row->id)->update([
                    $column => Crypt::encryptString((string) $value),
                ]);
            }
        });
    }

    protected function decryptColumn(string $table, string $column): void
    {
        DB::table($table)->whereNotNull($column)->orderBy('id')->chunkById(200, function ($rows) use ($table, $column) {
            foreach ($rows as $row) {
                $value = $row->{$column};
                if ($value === null || $value === '') {
                    continue;
                }
                try {
                    $plain = Crypt::decryptString($value);
                } catch (\Throwable $e) {
                    continue;
                }
                DB::table($table)->where('id', $row->id)->update([$column => $plain]);
            }
        });
    }
};
