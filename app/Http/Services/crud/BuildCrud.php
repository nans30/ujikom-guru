<?php

namespace App\Http\Services\crud;

use App\Http\Services\crud\exceptions\FileException;
use App\Http\Services\crud\exceptions\TableException;
use App\Http\Services\tool\CommonTool;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Artisan;

/**
 * Sistem Pembangunan CURD Cepat
 * Class BuildCurd
 * @package EasyAdmin\curd
 */
class BuildCrud
{
    /**
     * Direktori saat ini
     * @var string
     */
    protected string $dir;

    /**
     * Direktori root
     * @var string
     */
    protected string $rootDir;

    /**
     * Pemisah
     * @var string
     */
    protected string $DS = DIRECTORY_SEPARATOR;

    protected string $controllerNamespace;
    protected string $controllerName;
    protected string $dataTableName;
    protected string $repositoryName;
    protected string $requestName;
    protected string $modelFilename;
    protected string $modelName;

    protected string $modulName;
    protected string $routeName;

    /**
     * Daftar file yang dihasilkan
     * @var array
     */
    protected array $fileList = [];

    protected bool $force = false;

    /**
     * Inisialisasi
     * BuildCurd constructor.
     */
    public function __construct()
    {
        $this->dir         = __DIR__;
        $this->rootDir     = base_path();
        return $this;
    }

    public function setName($name): static
    {
        $this->modelFilename = ucfirst(CommonTool::lineToHump($name));
        $this->controllerNamespace = "App\Http\Controllers\Admin";
        $this->controllerName = ucfirst($name) . 'Controller';
        $this->dataTableName = ucfirst($name) . 'DataTable';
        $this->repositoryName = ucfirst($name) . 'Repository';
        $this->requestName = ucfirst($name) . 'Request';

        $this->modulName = Str::snake($name);
        $this->routeName = Str::kebab($this->modulName);
        return $this;
    }

    /**
     * Membuat filelist
     * @return array
     */
    public function getFileList(): array
    {
        return $this->fileList;
    }

    /**
     * Proses membuat file
     * @return $this
     */
    public function render(): static
    {
        $this->renderModel();
        $this->renderDataTable();
        $this->renderRequest();
        $this->renderRepository();
        $this->renderController();
        $this->renderView();
        $this->renderPolicies();

        return $this;
    }

    // Membuat File Model dan Migration
    protected function renderModel(): static
    {
        // Path untuk file model utama
        $modelFile = "{$this->rootDir}{$this->DS}app{$this->DS}Models{$this->DS}{$this->modelFilename}.php";

        // Ambil dan isi template model
        $template = $this->getTemplate("model{$this->DS}main");
        $modelValue = CommonTool::replaceTemplate($template, [
            'modelName' => $this->modelFilename,
            'modulName' => $this->modulName,
        ]);

        $this->fileList[$modelFile] = $modelValue;

        // PROSES PEMBUATAN FILE MIGRATION
        Artisan::call('make:migration', [
            'name' => 'create_' . $this->modulName . 's_table'
        ]);

        // Ambil file migration terbaru
        $migrationPath = database_path('migrations');
        $migrations = glob($migrationPath . '/*.php');
        $latestMigrationFile = collect($migrations)->sortDesc()->first();
        $latestMigration = $latestMigrationFile ? basename($latestMigrationFile) : null;

        if ($latestMigrationFile && is_file($latestMigrationFile)) {
            unlink($latestMigrationFile);
        }

        // Buat ulang file migration dengan template custom
        $migrationFile = "{$this->rootDir}{$this->DS}database{$this->DS}migrations{$this->DS}$latestMigration";
        $migrationValue = CommonTool::replaceTemplate(
            $this->getTemplate("migration{$this->DS}migration"),
            [
                'modulName' => $this->modulName,
            ]
        );
        $this->fileList[$migrationFile] = $migrationValue;

        // PROSES PEMBUATAN FILE ROUTE
        $routeFile = base_path('routes/admin.php');
        $routeContent = file_get_contents($routeFile);

        $customRoutes = "\n// {$this->modulName}\n"
            . "Route::resource('{$this->modulName}', {$this->controllerNamespace}\\{$this->controllerName}::class);\n";

        // Tambahkan setelah tag khusus
        $pattern = '/(\/\/MODUL_GENERATE_JANGAN_DIHAPUS\s*\n)/';
        if (preg_match($pattern, $routeContent)) {
            $routeContent = preg_replace($pattern, "$1" . $customRoutes, $routeContent, 1);
        } else {
            $routeContent .= "\n//MODUL_GENERATE_JANGAN_DIHAPUS\n" . $customRoutes;
        }

        file_put_contents($routeFile, $routeContent);

        return $this;
    }


    // Membuat File DataTable
    protected function renderDataTable(): static
    {
        $dataTableFile = "{$this->rootDir}{$this->DS}app{$this->DS}DataTables{$this->DS}{$this->dataTableName}.php";

        $dataTableValue = CommonTool::replaceTemplate(
            $this->getTemplate("datatable{$this->DS}datatable"),
            [
                'dataTableName'    => $this->dataTableName,
                'modelFilename'    => $this->modelFilename,
                'modulName'        => $this->modulName,
                'routeName'        => $this->routeName,

            ]
        );
        $this->fileList[$dataTableFile] = $dataTableValue;
        return $this;
    }

    // Membuat File Request
    protected function renderRequest(): static
    {
        $requestTypes = ['Create', 'Update'];

        foreach ($requestTypes as $type) {
            $requestClassName = $type . $this->requestName;
            $requestFile = "{$this->rootDir}{$this->DS}app{$this->DS}Http{$this->DS}Requests{$this->DS}{$requestClassName}.php";

            $templateName = strtolower($type);

            $requestValue = CommonTool::replaceTemplate(
                $this->getTemplate("request{$this->DS}{$templateName}"),
                [
                    'requestName' => $requestClassName,
                    'modulName'   => $this->modulName,
                ]
            );

            $this->fileList[$requestFile] = $requestValue;
        }

        return $this;
    }

    // Membuat File Repository
    protected function renderRepository(): static
    {
        $repositoryFile = "{$this->rootDir}{$this->DS}app{$this->DS}Repositories{$this->DS}{$this->repositoryName}.php";

        $modulNameText = ucwords(str_replace('_', ' ', $this->modulName));

        $repositoryValue = CommonTool::replaceTemplate(
            $this->getTemplate("repository{$this->DS}repository"),
            [
                'repositoryName' => $this->repositoryName,
                'modelFilename'  => $this->modelFilename,
                'modulName'   => $this->modulName,
                'modulNameText'   => $modulNameText,
                'tableName'   => $this->modulName . 'Table',
            ]
        );
        $this->fileList[$repositoryFile] = $repositoryValue;
        return $this;
    }

    // Membuat File Controller
    protected function renderController(): static
    {
        $controllerFile = "{$this->rootDir}{$this->DS}app{$this->DS}Http{$this->DS}Controllers{$this->DS}Admin{$this->DS}{$this->controllerName}.php";

        $selectList = '';

        $controllerValue = CommonTool::replaceTemplate(
            $this->getTemplate("controller{$this->DS}controller"),
            [
                'controllerName'       => $this->controllerName,
                'controllerNamespace'  => 'Admin',
                'dataTableName'        => $this->dataTableName,
                'repositoryName'       => $this->repositoryName,
                'modelFilename'        => $this->modelFilename,
                'createRequestName'    => 'Create' . $this->requestName,
                'updateRequestName'    => 'Update' . $this->requestName,
                'modulName'            => $this->modulName,
                'selectList'           => $selectList,
            ]
        );

        $this->fileList[$controllerFile] = $controllerValue;

        return $this;
    }

    // Membuat File Policies
    protected function renderPolicies(): static
    {
        $policyClassName = "{$this->modelFilename}Policy";
        $policyFile = "{$this->rootDir}{$this->DS}app{$this->DS}Policies{$this->DS}{$policyClassName}.php";

        $policyValue = CommonTool::replaceTemplate(
            $this->getTemplate("policies{$this->DS}policies"),
            [
                'modelFilename' => $this->modelFilename,
                'modulName'     => $this->modulName,
            ]
        );

        $this->fileList[$policyFile] = $policyValue;

        return $this;
    }


    protected function renderView(): static
    {
        $modulNameText = ucwords(str_replace('_', ' ', $this->modulName));

        // Index
        $viewIndexFile = "{$this->rootDir}{$this->DS}resources{$this->DS}views{$this->DS}admin{$this->DS}{$this->modulName}{$this->DS}index.blade.php";
        $viewIndexValue = CommonTool::replaceTemplate(
            $this->getTemplate("view{$this->DS}index"),
            [
                'titleModul' => $modulNameText,
                'nameModul'  => $this->modulName,
            ]
        );
        $this->fileList[$viewIndexFile] = $viewIndexValue;

        // Field
        $viewFieldsFile = "{$this->rootDir}{$this->DS}resources{$this->DS}views{$this->DS}admin{$this->DS}{$this->modulName}{$this->DS}fields.blade.php";
        $viewFieldsValue = CommonTool::replaceTemplate(
            $this->getTemplate("view{$this->DS}fields"),
            [
                'titleModul' => $modulNameText,
                'nameModul'  => $this->modulName,
            ]
        );
        $this->fileList[$viewFieldsFile] = $viewFieldsValue;

        // Create
        $createFile = "{$this->rootDir}{$this->DS}resources{$this->DS}views{$this->DS}admin{$this->DS}{$this->modulName}{$this->DS}create.blade.php";
        $createValue = CommonTool::replaceTemplate(
            $this->getTemplate("view{$this->DS}create"),
            [
                'titleModul' => $modulNameText,
                'nameModul'  => $this->modulName,
            ]
        );
        $this->fileList[$createFile] = $createValue;

        // Edit
        $editFile = "{$this->rootDir}{$this->DS}resources{$this->DS}views{$this->DS}admin{$this->DS}{$this->modulName}{$this->DS}edit.blade.php";
        $editValue = CommonTool::replaceTemplate(
            $this->getTemplate("view{$this->DS}edit"),
            [
                'titleModul' => $modulNameText,
                'nameModul'  => $this->modulName,
            ]
        );
        $this->fileList[$editFile] = $editValue;

        return $this;
    }

    protected function check(): static
    {
        if ($this->force) {
            return $this;
        }
        foreach ($this->fileList as $key => $val) {
            if (is_file($key)) {
                throw new FileException("Berkas sudah ada{$key}");
            }
        }
        return $this;
    }

    public function create(): array
    {
        $this->check();
        foreach ($this->fileList as $key => $val) {

            // Tentukan apakah folder tersebut ada, buatlah jika tidak ada
            $fileArray = explode($this->DS, $key);
            array_pop($fileArray);
            $fileDir = implode($this->DS, $fileArray);
            if (!is_dir($fileDir)) {
                mkdir($fileDir, 0775, true);
            }

            // Create file
            file_put_contents($key, $val);
        }
        return array_keys($this->fileList);
    }

    /**
     * Delete Crud - To Do
     * @return array
     */
    public function delete(): array
    {
        $deleteFile = [];
        foreach ($this->fileList as $key => $val) {
            if (is_file($key)) {
                unlink($key);
                $deleteFile[] = $key;
            }
        }
        return $deleteFile;
    }

    protected function getTemplate($name): bool|string
    {
        return file_get_contents("{$this->dir}{$this->DS}templates{$this->DS}{$name}.code");
    }
}