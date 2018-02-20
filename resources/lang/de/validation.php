<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted'             => ':attribute muss akzeptiert werden.',
    'active_url'           => ':attribute ist keine gültige URL.',
    'after'                => ':attribute muss ein Datum nach :date sein.',
    'after_or_equal'       => ':attribute muss ein Datum nach oder gleich :date sein.',
    'alpha'                => ':attribute darf nur Buchstaben enthalten.',
    'alpha_dash'           => ':attribute darf nur Buchstaben, Ziffern und Bindestriche enthalten.',
    'alpha_num'            => ':attribute darf nur Buchstaben und Ziffern enthalten.',
    'array'                => ':attribute muss ein Array sein.',
    'before'               => ':attribute muss ein Datum vor :date sein.',
    'before_or_equal'      => ':attribute muss ein Datum vor oder gleich :date sein.',
    'between'              => [
        'numeric' => ':attribute muss zwischen :min und :max liegen.',
        'file'    => ':attribute muss zwischen :min und :max Kilobyte sein.',
        'string'  => ':attribute muss zwischen :min und :max Zeichen lang sein.',
        'array'   => ':attribute muss zwischen :min und :max Einträge haben.',
    ],
    'boolean'              => ':attribute muss wahr oder unwahr sein.',
    'confirmed'            => ':attribute Bestätigung stimmt nicht überein.',
    'date'                 => ':attribute ist kein gültiges Datum.',
    'date_format'          => ':attribute hat nicht das Format :format.',
    'different'            => ':attribute und :other müssen sich unterscheiden.',
    'digits'               => ':attribute muss :digits Ziffern lang sein.',
    'digits_between'       => ':attribute muss zwischen :min und :max Ziffern lang sein.',
    'dimensions'           => ':attribute hat eine ungültige Bildgröße.',
    'distinct'             => ':attribute hat einen Wert mehrfach.',
    'email'                => ':attribute muss eine gültige E-Mail-Adresse sein.',
    'exists'               => 'Auswahl :attribute ist ungültige.',
    'file'                 => ':attribute muss eine Datei sein.',
    'filled'               => ':attribute field is required.',
    'image'                => ':attribute muss ein Bild sein.',
    'in'                   => 'Auswahl :attribute ist ungültig.',
    'in_array'             => ':attribute ist nicht in :other vorhanden.',
    'integer'              => ':attribute muss eine ganze Zahl sein.',
    'ip'                   => ':attribute muss eine gültige IP-Adresse sein.',
    'json'                 => ':attribute muss ein gültiger JSON-String sein.',
    'max'                  => [
        'numeric' => ':attribute darf nicht größer als :max sein.',
        'file'    => ':attribute darf nicht größer als :max Kilobyte groß sein.',
        'string'  => ':attribute darf nicht länger als :max Zeichen lang sein.',
        'array'   => ':attribute darf nicht mehr als :max Einträge haben.',
    ],
    'mimes'                => ':attribute muss eine Datei vom Dateityp :values sein.',
    'mimetypes'            => ':attribute muss eine Detai vom Dateityp :values sein.',
    'min'                  => [
        'numeric' => ':attribute muss mindestens :min sein.',
        'file'    => ':attribute muss mindestens :min Kilobyte groß sein.',
        'string'  => ':attribute muss mindestens :min Zeichen lang sein.',
        'array'   => ':attribute muss mindestens :min Einträge haben.',
    ],
    'not_in'               => 'Auswahl :attribute ist ungültig.',
    'numeric'              => ':attribute muss eine Zah sein.',
    'present'              => ':attribute muss angegeben werden.',
    'regex'                => ':attribute Format ist ungültig.',
    'required'             => ':attribute ist erforderlich.',
    'required_if'          => ':attribute ist erforderlich.',
    'required_unless'      => ':attribute ist erforderlich, wenn :other nicht :values ist.',
    'required_with'        => ':attribute ist erforderlich, wenn :values angegeben ist.',
    'required_with_all'    => ':attribute ist erforderlich, wenn :values angegeben ist.',
    'required_without'     => ':attribute ist erforderlich, wenn :values nicht angegeben ist.',
    'required_without_all' => ':attribute ist erforderlich, wenn keines von :values angegeben ist.',
    'same'                 => ':attribute and :other must match.',
    'size'                 => [
        'numeric' => ':attribute muss :size.',
        'file'    => ':attribute muss :size Kilobyte groß sein.',
        'string'  => ':attribute muss :size Zeichen lang sein.',
        'array'   => ':attribute muss :size Einträge haben.',
    ],
    'string'               => ':attribute muss eine Zeichenkette sein.',
    'timezone'             => ':attribute muss eine gültige Zeitzone sein.',
    'unique'               => ':attribute ist bereits vergeben.',
    'uploaded'             => ':attribute konnte nicht hochgeladen werden.',
    'url'                  => ':attribute Format ungültig.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    'value_different' => ':attribute und :other müssen sich unterscheiden.',
    'login' => 'Unterstriche und Großbuchstaben sind nicht in :attribute Wert erlaubt',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => [],

];
