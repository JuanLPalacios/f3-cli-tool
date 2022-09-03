<?php
namespace Helpers;

class TemplateHelper extends \Prefab {
	function var($input) {
        return "{{ @${input} }}";
    }
}