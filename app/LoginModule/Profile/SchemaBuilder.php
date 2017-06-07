<?php

namespace App\LoginModule\Profile;

use App\User;

class SchemaBuilder {


    public function build($user, array $required_attributes, array $disabled_attributes = [], $all_visible = false) {
        $visible_attributes = $this->visibleAttributes($required_attributes, $all_visible);
        $required_attributes  = array_fill_keys($required_attributes, true);
        $disabled_attributes = array_fill_keys($disabled_attributes, true);
        $blocks = [];

        foreach($visible_attributes as $attribute) {
            $config = SchemaConfig::$attribute($user);
            $disabled = isset($disabled_attributes[$attribute]);
            $required = !$disabled && isset($required_attributes[$attribute]);
            $block = [
                'name' => isset($config['name']) ? $config['name'] : $attribute,
                'type' => $config['type'],
                'rule' => $this->rule($config, $required, $disabled),
                'required' => $required,
                'disabled' => $disabled,
                'options' => isset($config['options']) ? $config['options'] : null,
                'label' => isset($config['label']) ? $config['label'] : null,
                'help' => isset($config['help']) ? $config['help'] : null,
            ];
            $blocks[] = (object) $block;
        }
        return new Schema($blocks);
    }


    private function visibleAttributes(array $required_attributes, $all_visible = false) {
        $available = $this->availableAttributes();
        if($all_visible || count($required_attributes) == 0) {
            return $available;
        }
        return array_values(array_intersect($available, $required_attributes));
    }


    public static function availableAttributes() {
        return get_class_methods(SchemaConfig::class);
    }


    private function rule($config, $required, $disabled) {
        if($disabled) {
            return '';
        }
        $rule = '';
        if($required && isset($config['required']))  {
            $rule .= $config['required'].'|';
        }
        if(isset($config['valid'])) {
            $rule .= $config['valid'];
        }
        return $rule;
    }

}