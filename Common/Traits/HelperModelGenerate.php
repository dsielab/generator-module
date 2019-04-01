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

trait HelperModelGenerate
{
    protected function init()
    {
        $contentFile = [];
        $tablesNoLoad = $this->arrTablesNoGenerate();
        $objectsDB = $this->dataMapping();
        foreach ($objectsDB as $ktableName => $tables) {
            if (!in_array($ktableName, $tablesNoLoad)) {
                $columns = [];
                $modelClassName = $this->getClassName($ktableName);
                foreach ($tables["columnas"] as $columnName => $columna) {
                    $_column = [];
                    $_column["name"] = $columna["nombre"];
                    if (strlen($columna["longitud"]) > 0) {
                        $_column["dbType"] = $this->getTypeData($columna["tipo"]) . ", " . $columna["longitud"];
                    } else {
                        $_column["dbType"] = $this->getTypeData($columna["tipo"]);
                    }
                    if (!in_array($_column["name"], ['id', 'created_at', 'updated_at', 'deleted_at',
                        'user_creator', 'rol_creator'])
                    ) {
                        $_column["fillable"] = "true";

                        if ($columna["is_nulable"] == "NO") {
                            $_column["validations"] = "required";
                        }
                    }

                    $columns[] = $_column;
                }
                $contentFile[$modelClassName] = $columns;
            }
        }

        file_put_contents(__DIR__ . "/models.json", json_encode($contentFile));
    }

    protected function getTypeData($type)
    {
        switch ($type) {
            case "varchar":
                return "string";
            case "integer":
                return "integer";
            case "int":
                return "integer";
            case "date":
                return "date";
            case "datetime":
                return "dateTime";
            case "timestamp":
                return "timestamp";
            case "bit":
                return "boolean";
            case "character varying":
                return "string";
            case "text":
                return "string";
            case "tinyint":
                return "boolean";
            default: {
                throw new Exception("Tipo de dato(" . $type . ") no identificado!!");
                die;
            }
        }
    }

    protected function arrTablesNoGenerate()
    {
        $composer = require base_path() . "/vendor/composer/autoload_classmap.php";
        $models = array_filter(array_keys($composer), function ($value) {
            $arrNS = explode('\\', $value);
            return count($arrNS) == 2 && in_array('App', explode('\\', $value));
        });
        $tablesFromModels = [];
        foreach ($models as $model){
            if(!trait_exists($model)) {
                $tablesFromModels[] = (new $model())->getTable();
            }
        }
        return $tablesFromModels;
    }

    /** Extraccion y mapeo de las tablas de la base de datos
     * @param $objectsDB
     */
    protected function dataMapping()
    {
        $objectsDB = [];
        $resultDB = $this->getTableObject();
        foreach ($resultDB as $row) {
            if (!isset($objectsDB[$row->tabla])) {
                $objectsDB[$row->tabla] = [];
            }
            if (!isset($objectsDB[$row->tabla]["columnas"])) {
                $objectsDB[$row->tabla]["columnas"] = [];
            }
            if (!isset($objectsDB[$row->tabla]["columnas"][$row->columna])) {
                $objectsDB[$row->tabla]["columnas"][$row->columna] = [];
            }
            if (!isset($objectsDB[$row->tabla]["columnas"][$row->columna]["nombre"])) {
                $objectsDB[$row->tabla]["columnas"][$row->columna]["nombre"] = $row->columna;
            }
            if (!isset($objectsDB[$row->tabla]["columnas"][$row->columna]["tipo"])) {
                $objectsDB[$row->tabla]["columnas"][$row->columna]["tipo"] = $row->tipo;
            }
            if (!isset($objectsDB[$row->tabla]["columnas"][$row->columna]["longitud"])) {
                $objectsDB[$row->tabla]["columnas"][$row->columna]["longitud"] = $row->longitud;
            }
            if (!isset($objectsDB[$row->tabla]["columnas"][$row->columna]["is_nulable"])) {
                $objectsDB[$row->tabla]["columnas"][$row->columna]["is_nulable"] = $row->is_nulable;
            }
        }

        return $objectsDB;
    }

    protected function getTableObject()
    {
        $sql = "SELECT TABLE_SCHEMA AS esquema, TABLE_NAME as tabla,COLUMN_NAME AS columna,
                   COLUMN_DEFAULT AS valor_defecto, IS_NULLABLE AS is_nulable, DATA_TYPE AS tipo, 
                   CHARACTER_MAXIMUM_LENGTH AS longitud
            FROM information_schema.COLUMNS
            where TABLE_SCHEMA = :schemaName;";

        return DB::select($sql, [
            "schemaName" => env('DB_DATABASE')
        ]);
    }

    protected function getClassName($classKey)
    {
        return str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $classKey)));
    }
}