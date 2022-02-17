<?php
namespace App\Helpers;

class SortableTable {


    public static function th($field, $title) {
        $url = request()->url();
        $sort_field = request()->get('sort_field', null);
        $sort_order = request()->get('sort_order', null);

        if($field === $sort_field) {
            $icon = $sort_order == 'desc' ? ' &uarr;' : ' &darr;';
            $params = [
                'sort_field' => $field,
                'sort_order' => $sort_order == 'desc' ? 'asc' : 'desc'
            ];
        } else {
            $icon = '';
            $params = [
                'sort_field' => $field,
                'sort_order' => 'asc'
            ];
        }
        return '<a href="'.request()->fullUrlWithQuery($params).'">'.$title.$icon.'</a>';
    }


    public static function orderBy(&$query, $sort_fields) {
        $sort_field = request()->get('sort_field');
        if(isset($sort_fields[$sort_field])) {
            $sort_order = request()->get('sort_order', 'asc');
            $query->orderBy($sort_fields[$sort_field], $sort_order);
            // workaround for showPaginated sort
            if($sort_field != 'id' && isset($sort_fields['id'])) {
                $query->orderBy($sort_fields['id'], 'asc');
            }
        }
    }

}