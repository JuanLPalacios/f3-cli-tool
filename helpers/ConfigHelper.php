<?php
namespace Helpers;

class ConfigHelper extends \Prefab {
	function renderConfig($section, $filter) {
        return $this->renderValue(array_filter($section, function($val, $key) {
            return !in_array($key, $filter);
        }, ARRAY_FILTER_USE_BOTH));
	}

    function renderValue($key, $value) {
        switch ($gettype(value)) {
            case "boolean":
                $r_value = $value? 'TRUE' : 'FALSE';
                break;
            case "string":
                $r_value = '"' . addslashes($str) . '"';
                break;
            case "array":
                return join(
                    '', 
                    array_map(
                        fn($val, $subkey): string => $this->renderValue("{$key}[{$subkey}]", $val), 
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