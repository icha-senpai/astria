<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class AstriaMakeResource extends Command
{
    protected $signature = 'astria:make:resource {module} {name}';
    protected $description = 'Create a Filament Resource (CRUD) inside a module (with Model + Migration)';

    public function handle(): int
    {
        $module = Str::studly($this->argument('module'));
        $name   = Str::studly($this->argument('name'));

        $base   = base_path("modules/{$module}");
        if (! is_dir($base)) {
            $this->error("Module {$module} not found.");
            return self::FAILURE;
        }


        $modelNs = "Modules\\{$module}\\Models";
        $modelDir = "{$base}/Models";
        $table = Str::snake(Str::pluralStudly($name));
        $model = "{$modelNs}\\{$name}";
        $resourceNs = "Modules\\{$module}\\Filament\\Resources";
        $resourceDir = "{$base}/Filament/Resources/{$name}Resource";

        // Make dirs
        foreach ([$modelDir, $resourceDir . '/Pages'] as $d) {
            if (! is_dir($d)) File::makeDirectory($d, 0755, true);
        }

        // Model
        File::put("{$modelDir}/{$name}.php", <<<PHP
<?php
namespace {$modelNs};

use Illuminate\\Database\\Eloquent\\Model;

class {$name} extends Model
{
    protected \$table = '{$table}';
    protected \$guarded = [];
}
PHP);

        // Migration
        $stamp = date('Y_m_d_His');
        $migDir = "{$base}/database/migrations";
        if (! is_dir($migDir)) File::makeDirectory($migDir, 0755, true);
        File::put("{$migDir}/{$stamp}_create_{$table}_table.php", <<<PHP
<?php
use Illuminate\\Database\\Migrations\\Migration;
use Illuminate\\Database\\Schema\\Blueprint;
use Illuminate\\Support\\Facades\\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('{$table}', function (Blueprint \$table) {
            \$table->id();
            \$table->string('title');
            \$table->text('content')->nullable();
            \$table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('{$table}');
    }
};
PHP);

        // Resource
        File::put("{$base}/Filament/Resources/{$name}Resource.php", <<<PHP
<?php
namespace {$resourceNs};

use {$model};
use Filament\\Forms;
use Filament\\Resources\\Form;
use Filament\\Resources\\Table;
use Filament\\Resources\\Resource;
use Filament\\Tables;
use {$resourceNs}\\Pages\\Manage{$name}s;

class {$name}Resource extends Resource
{
    protected static ?string \$model = {$name}::class;

    public static function getNavigationGroup(): ?string { return '{$module}'; }
    public static function getNavigationLabel(): string { return '{$name}s'; }
    protected static ?string \$navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form \$form): Form
    {
        return \$form->schema([
            Forms\\Components\\TextInput::make('title')->required()->maxLength(255),
            Forms\\Components\\Textarea::make('content')->rows(6),
        ]);
    }

    public static function table(Table \$table): Table
    {
        return \$table
            ->columns([
                Tables\\Columns\\TextColumn::make('id')->sortable(),
                Tables\\Columns\\TextColumn::make('title')->searchable()->sortable(),
                Tables\\Columns\\TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([])
            ->actions([
                Tables\\Actions\\EditAction::make(),
                Tables\\Actions\\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\\Actions\\BulkActionGroup::make([
                    Tables\\Actions\\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Manage{$name}s::route('/'),
        ];
    }
}
PHP);

        // Resource Page
        File::put("{$resourceDir}/Pages/Manage{$name}s.php", <<<PHP
<?php
namespace {$resourceNs}\\Pages;

use {$resourceNs}\\{$name}Resource;
use Filament\\Resources\\Pages\\ManageRecords;

class Manage{$name}s extends ManageRecords
{
    protected static string \$resource = {$name}Resource::class;

    protected function getHeaderActions(): array
    {
        return [ \\Filament\\Actions\\CreateAction::make() ];
    }
}
PHP);

        $this->info("✅ Resource {$name} created in module {$module}");
        $this->line("Next:");
        $this->line("  • php artisan migrate");
        $this->line("  • Open /admin and look under {$module} → {$name}s");
        return self::SUCCESS;
    }
}
