<?php
namespace Helpers;

class StyleHelper extends \Prefab {
	function snakeCase($input) {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $input));
    }
}