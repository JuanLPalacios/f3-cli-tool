<?php
namespace Helpers;

class ConfigHelper extends \Prefab {
	function renderConfig($section, $filter = []) {
        $value =array_filter($section, function($val, $key) use ($filter) {
            return !in_array($key, $filter);
        }, ARRAY_FILTER_USE_BOTH);
        //var_dump(array_keys($value));
        return join(
            '', 
            array_map(
                function ($val, $key){
                    return $this->renderValue("{$key}", $val); 
                },
                $value, 
                array_keys($value)
            )
        );
	}

    function renderValue($key, $value) {
        switch (gettype($value)) {
            case "boolean":
                $r_value = $value? 'TRUE' : 'FALSE';
                break;
            case "string":
                $r_value = '"' . addslashes($value) . '"';
                break;
            case "array":
                return join(
                    '', 
                    array_map(
                        function ($val, $subkey) use($key) {
                            return $this->renderValue("{$key}[{$subkey}]", $val);
                        }, 
                        $value, 
                        array_keys($value)
                    )
                );
            case "object":
                throw new Exception('not implemented');
                break;
            case "resource":
                throw new Exception('not implemented');
                break;
            case "resource (closed)":
                throw new Exception('not implemented');
                break;
            case "NULL":
                $r_value = 'NULL';
                break;
            case "unknown type":
                throw new Exception('not implemented');
                break;
            
            default:
                $r_value = $value;
                break;
        }
        return "{$key} = $r_value\n";
    }
}