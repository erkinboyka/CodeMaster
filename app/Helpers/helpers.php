<?php

use App\Services\I18nService;

if (!function_exists('t')) {
    function t(string $key, string $default = ''): string
    {
        return app(I18nService::class)->translate($key, $default);
    }
}

if (!function_exists('currentLang')) {
    function currentLang(): string
    {
        return app(I18nService::class)->currentLang();
    }
}

if (!function_exists('langUrl')) {
    function langUrl(string $lang): string
    {
        return app(I18nService::class)->langUrl($lang);
    }
}

if (!function_exists('normalizeMojibakeText')) {
    function normalizeMojibakeText(string $text): string
    {
        $mojibakeMap = [
            'Ð' => 'Д', 'Ñ' => 'Н',
        ];

        $result = $text;
        foreach ($mojibakeMap as $moji => $real) {
            $result = str_replace($moji, $real, $result);
        }

        if (preg_match('/[\x80-\xFF]{2,}/', $result)) {
            $decoded = @mb_convert_encoding($result, 'UTF-8', 'CP1251');
            if ($decoded && mb_check_encoding($decoded, 'UTF-8')) {
                return $decoded;
            }
        }

        return $result;
    }
}

if (!function_exists('getAvatarUrl')) {
    function getAvatarUrl(string $name): string
    {
        $initials = '';
        $parts = explode(' ', trim($name));
        foreach ($parts as $part) {
            if ($part !== '') {
                $initials .= mb_strtoupper(mb_substr($part, 0, 1));
            }
            if (mb_strlen($initials) >= 2) {
                break;
            }
        }

        if ($initials === '') {
            $initials = 'U';
        }

        $hash = md5($name);
        $hue = hexdec(substr($hash, 0, 3)) % 360;

        return "https://ui-avatars.com/api/?name=" . urlencode($initials) . "&background=hsl({$hue},60%,50%)&color=fff&bold=true&size=128";
    }
}

if (!function_exists('country_flag')) {
    function country_flag(?string $code): string
    {
        if (!$code || strlen($code) !== 2) return '';
        $l = strtolower($code);
        return '<img src="https://flagcdn.com/48x36/' . $l . '.png" alt="' . strtoupper($code) . '" width="24" height="18" style="display:inline;vertical-align:middle;margin-right:4px;border-radius:3px" loading="lazy">';
    }
}

if (!function_exists('country_name')) {
    function country_name(?string $code): string
    {
        if (!$code) return '';
        $countries = get_countries();
        return $countries[$code] ?? $code;
    }
}

if (!function_exists('get_countries')) {
    function get_countries(): array
    {
        return [
            'AF'=>'Afghanistan','AL'=>'Albania','DZ'=>'Algeria','AD'=>'Andorra','AO'=>'Angola',
            'AG'=>'Antigua & Barbuda','AR'=>'Argentina','AM'=>'Armenia','AU'=>'Australia','AT'=>'Austria',
            'AZ'=>'Azerbaijan','BS'=>'Bahamas','BH'=>'Bahrain','BD'=>'Bangladesh','BB'=>'Barbados',
            'BY'=>'Belarus','BE'=>'Belgium','BZ'=>'Belize','BJ'=>'Benin','BT'=>'Bhutan',
            'BO'=>'Bolivia','BA'=>'Bosnia & Herzegovina','BW'=>'Botswana','BR'=>'Brazil','BN'=>'Brunei',
            'BG'=>'Bulgaria','BF'=>'Burkina Faso','BI'=>'Burundi','CV'=>'Cabo Verde','KH'=>'Cambodia',
            'CM'=>'Cameroon','CA'=>'Canada','CF'=>'Central African Rep.','TD'=>'Chad','CL'=>'Chile',
            'CN'=>'China','CO'=>'Colombia','KM'=>'Comoros','CG'=>'Congo','CD'=>'DR Congo',
            'CR'=>'Costa Rica','CI'=>"Côte d'Ivoire",'HR'=>'Croatia','CU'=>'Cuba','CY'=>'Cyprus',
            'CZ'=>'Czechia','DK'=>'Denmark','DJ'=>'Djibouti','DM'=>'Dominica','DO'=>'Dominican Republic',
            'EC'=>'Ecuador','EG'=>'Egypt','GQ'=>'Equatorial Guinea','ER'=>'Eritrea','EE'=>'Estonia',
            'SZ'=>'Eswatini','ET'=>'Ethiopia','FJ'=>'Fiji','FI'=>'Finland','FR'=>'France',
            'GA'=>'Gabon','GM'=>'Gambia','GE'=>'Georgia','DE'=>'Germany','GH'=>'Ghana',
            'GR'=>'Greece','GD'=>'Grenada','GT'=>'Guatemala','GN'=>'Guinea','GW'=>'Guinea-Bissau',
            'GY'=>'Guyana','HT'=>'Haiti','HN'=>'Honduras','HU'=>'Hungary','IS'=>'Iceland',
            'IN'=>'India','ID'=>'Indonesia','IR'=>'Iran','IQ'=>'Iraq','IE'=>'Ireland',
            'IL'=>'Israel','IT'=>'Italy','JM'=>'Jamaica','JP'=>'Japan','JO'=>'Jordan',
            'KZ'=>'Kazakhstan','KE'=>'Kenya','KI'=>'Kiribati','KP'=>'North Korea','KR'=>'South Korea',
            'KW'=>'Kuwait','KG'=>'Kyrgyzstan','LA'=>'Laos','LV'=>'Latvia','LB'=>'Lebanon',
            'LS'=>'Lesotho','LR'=>'Liberia','LY'=>'Libya','LI'=>'Liechtenstein','LT'=>'Lithuania',
            'LU'=>'Luxembourg','MG'=>'Madagascar','MW'=>'Malawi','MY'=>'Malaysia','MV'=>'Maldives',
            'ML'=>'Mali','MT'=>'Malta','MH'=>'Marshall Islands','MR'=>'Mauritania','MU'=>'Mauritius',
            'MX'=>'Mexico','FM'=>'Micronesia','MD'=>'Moldova','MC'=>'Monaco','MN'=>'Mongolia',
            'ME'=>'Montenegro','MA'=>'Morocco','MZ'=>'Mozambique','MM'=>'Myanmar','NA'=>'Namibia',
            'NR'=>'Nauru','NP'=>'Nepal','NL'=>'Netherlands','NZ'=>'New Zealand','NI'=>'Nicaragua',
            'NE'=>'Niger','NG'=>'Nigeria','MK'=>'North Macedonia','NO'=>'Norway','OM'=>'Oman',
            'PK'=>'Pakistan','PW'=>'Palau','PA'=>'Panama','PG'=>'Papua New Guinea','PY'=>'Paraguay',
            'PE'=>'Peru','PH'=>'Philippines','PL'=>'Poland','PT'=>'Portugal','QA'=>'Qatar',
            'RO'=>'Romania','RU'=>'Russia','RW'=>'Rwanda','KN'=>'St. Kitts & Nevis','LC'=>'St. Lucia',
            'VC'=>'St. Vincent','WS'=>'Samoa','SM'=>'San Marino','ST'=>'São Tomé & Príncipe','SA'=>'Saudi Arabia',
            'SN'=>'Senegal','RS'=>'Serbia','SC'=>'Seychelles','SL'=>'Sierra Leone','SG'=>'Singapore',
            'SK'=>'Slovakia','SI'=>'Slovenia','SB'=>'Solomon Islands','SO'=>'Somalia','ZA'=>'South Africa',
            'SS'=>'South Sudan','ES'=>'Spain','LK'=>'Sri Lanka','SD'=>'Sudan','SR'=>'Suriname',
            'SE'=>'Sweden','CH'=>'Switzerland','SY'=>'Syria','TW'=>'Taiwan','TJ'=>'Tajikistan',
            'TZ'=>'Tanzania','TH'=>'Thailand','TL'=>'Timor-Leste','TG'=>'Togo','TO'=>'Tonga',
            'TT'=>'Trinidad & Tobago','TN'=>'Tunisia','TR'=>'Turkey','TM'=>'Turkmenistan','TV'=>'Tuvalu',
            'UG'=>'Uganda','UA'=>'Ukraine','AE'=>'United Arab Emirates','GB'=>'United Kingdom','US'=>'United States',
            'UY'=>'Uruguay','UZ'=>'Uzbekistan','VU'=>'Vanuatu','VA'=>'Vatican City','VE'=>'Venezuela',
            'VN'=>'Vietnam','YE'=>'Yemen','ZM'=>'Zambia','ZW'=>'Zimbabwe',
        ];
    }
}
