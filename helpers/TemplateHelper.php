<?php
namespace Helpers;

class TemplateHelper extends \Prefab {
    
    function var($input) {
        return "{{ @${input} }}";
    }
        

	/**
	*	Map data type of argument to a PDO constant
	*	@return int
	*	@param $val scalar
	**/
	function inputType($type) {
		switch ($type) {
			case 'boolean':
				return 'checkbox';
			case 'integer':
				return 'number';
			case 'float':
				return 'number';
            case 'text':
                return 'textarea';
            default:
                return 'text';
		}
	}
        

	/**
	*	Map data type of argument to a PDO constant
	*	@return int
	*	@param $val scalar
	**/
	function input($field_name, $field) {
        preg_match('/(\\w+)\\((\\d+)\\)/', $field['type'], $matches, PREG_OFFSET_CAPTURE);
        $type = $matches[1][0];
        $inputType = $this->inputType($type);
        $size = $matches[2][0];
        $html_field_name = str_replace(['"', "'"], "", $field_name);
		switch ($inputType) {
			case 'checkbox':
				return "<input type=\"${inputType}\" id=\"${html_field_name}\" name=\"${html_field_name}\" value=\"{{ @${field_name}? 'checked=\"true\"' : '' }}\"/>";
			case 'number':
				return "<input type=\"${inputType}\" id=\"${html_field_name}\" name=\"${html_field_name}\" value=\"{{ @${field_name} }}\"/>";
			case 'textarea':
				return "<textarea id=\"${html_field_name}\" name=\"${html_field_name}\">{{ @${field_name} }}</textarea>";
			default:
				return "<input type=\"${inputType}\" id=\"${html_field_name}\" name=\"${html_field_name}\" value=\"{{ @${field_name} }}\"/>";
		}
	}
}