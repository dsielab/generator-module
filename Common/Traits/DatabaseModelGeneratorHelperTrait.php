<?php
/**
 * Created by PhpStorm.
 * User: hurkel
 * Date: 1/04/19
 * Time: 15:44
 */

namespace Modules\Generator\Common\Traits;

use Exception;
use Illuminate\Support\Facades\DB;

trait DatabaseModelGeneratorHelperTrait
{
    /**
     * @param $targetModule
     * @return false|string
     * @throws Exception
     */
    protected function generate($targetModule)
    {
        $contentFile = [];
        $skippedTables = $this->skippedTables($targetModule);
        $objectsDB = $this->dataMapping();
        foreach ($objectsDB as $keyTableName => $tables) {
            if (in_array($keyTableName, $skippedTables)) {
                continue;
            }

            $columns = [];
            $modelClassName = $this->getClassName($keyTableName);
            foreach ($tables["columns"] as $columnName => $selected_column) {
                $_column = [];
                $_column["name"] = $selected_column["name"];

                if ($selected_column['extra_information'] === 'auto_increment') {
                    $_column["dbType"] = 'increments';
                } elseif (strlen($selected_column["selected_length"]) > 0) {
                    $_column["dbType"] = $this->getTypeData($selected_column["selected_data_type"]) . ", " . $selected_column["selected_length"];
                } else {
                    $_column["dbType"] = $this->getTypeData($selected_column["selected_data_type"]);
                }

                if (!in_array(
                    $_column["name"],
                    ['id', 'created_at', 'updated_at', 'deleted_at', 'user_creator', 'rol_creator'])
                ) {

                    $_column["fillable"] = "true";

                    if ($selected_column["is_nullable"] === "NO") {
                        $_column["validations"] = "required";
                    }
                }

                $columns[] = $_column;
            }
            $contentFile[$modelClassName] = $columns;

        }

        return empty($contentFile) ? false : json_encode($contentFile);
    }

    /**
     * Return data type
     *
     * @param $type
     * @return string
     * @throws Exception
     */
    protected function getTypeData($type)
    {
        switch ($type) {
            case "character varying":
            case "varchar":
            case "text":
                return "string";
            case "integer":
            case "int":
            case "smallint":
                return "integer";
            case "tinyint":
                return "tinyInteger";
            case "date":
                return "date";
            case "datetime":
                return "dateTime";
            case "bit":
                return "binary";
            case "bool":
            case "boolean":
                return "boolean";
            case "timestamp":
            case "time":
            case "enum":
            case "float":
            case "decimal":
            case "double":
                return $type;
            default:
                throw new Exception("Data type (" . $type . ") is not defined!!");
        }
    }

    /**
     * @param $targetModule
     * @return array
     */
    protected function skippedTables($targetModule)
    {
        $composer = require base_path() . "/vendor/composer/autoload_classmap.php";
        $modelInModuleNamespace = str_replace('{Module}', $targetModule, app('config')->get('modules.generator.namespace.model'));

        $models = array_filter(array_keys($composer), function ($value) use ($modelInModuleNamespace) {
            $searchApp = strpos($value, 'App\\');

            $splitModelNamespace = explode('\\', $value);
            $splittedNamespaceLength = count($splitModelNamespace);
            if (($searchApp !== false && $splittedNamespaceLength === 2) || strpos($value, $modelInModuleNamespace) !== false) {
                $modelName = $splitModelNamespace[$splittedNamespaceLength - 1];

                return $value === 'App\\'.$modelName || $value === $modelInModuleNamespace.'\\'.$modelName;
            }

            return false;
        });

        $tablesFromModels = [];
        foreach ($models as $model) {
            if (!trait_exists($model)) {
                $tablesFromModels[] = (new $model)->getTable();
            }
        }

        // Skipping Laravel default models
        $mergedArray = array_merge($tablesFromModels, [
            'migrations', 'password_resets', 'users'
        ]);

        sort($mergedArray);

        return $mergedArray;
    }

    /**
     * Extraction and mapping of database tables
     * @return array
     */
    protected function dataMapping()
    {
        $objectsDB = [];
        $resultDB = $this->getTableObject();
        foreach ($resultDB as $row) {
            if (!isset($objectsDB[$row->selected_table])) {
                $objectsDB[$row->selected_table] = [];
            }
            if (!isset($objectsDB[$row->selected_table]["columns"])) {
                $objectsDB[$row->selected_table]["columns"] = [];
            }
            if (!isset($objectsDB[$row->selected_table]["columns"][$row->selected_column])) {
                $objectsDB[$row->selected_table]["columns"][$row->selected_column] = [];
            }
            if (!isset($objectsDB[$row->selected_table]["columns"][$row->selected_column]["name"])) {
                $objectsDB[$row->selected_table]["columns"][$row->selected_column]["name"] = $row->selected_column;
            }
            if (!isset($objectsDB[$row->selected_table]["columns"][$row->selected_column]["selected_data_type"])) {
                $objectsDB[$row->selected_table]["columns"][$row->selected_column]["selected_data_type"] = $row->selected_data_type;
            }
            if (!isset($objectsDB[$row->selected_table]["columns"][$row->selected_column]["selected_length"])) {
                $objectsDB[$row->selected_table]["columns"][$row->selected_column]["selected_length"] = $row->selected_length;
            }
            if (!isset($objectsDB[$row->selected_table]["columns"][$row->selected_column]["is_nullable"])) {
                $objectsDB[$row->selected_table]["columns"][$row->selected_column]["is_nullable"] = $row->is_nullable;
            }
            if (!isset($objectsDB[$row->selected_table]["columns"][$row->selected_column]["extra_information"])) {
                $objectsDB[$row->selected_table]["columns"][$row->selected_column]["extra_information"] = $row->extra_information;
            }
        }

        return $objectsDB;
    }

    /**
     * Obtain models from database
     * @return array
     */
    protected function getTableObject()
    {
        $sql = "SELECT 
                TABLE_SCHEMA AS selected_table_schema,
                TABLE_NAME AS selected_table,
                COLUMN_NAME AS selected_column,
                COLUMN_DEFAULT AS default_value,
                IS_NULLABLE AS is_nullable,
                DATA_TYPE AS selected_data_type, 
                CHARACTER_MAXIMUM_LENGTH AS selected_length,
                EXTRA AS extra_information
            FROM information_schema.COLUMNS
            WHERE TABLE_SCHEMA = :schemaName;";

        $dbName = app('config')->get('database');
        $dbName = $dbName['connections'][$dbName['default']]['database'];
        return DB::select($sql, [
            "schemaName" => $dbName
        ]);
    }

    /**
     * Returns class name
     *
     * @param $classKey
     * @return mixed
     */
    protected function getClassName($classKey)
    {
        return str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $classKey)));
    }
}