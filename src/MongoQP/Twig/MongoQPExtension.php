<?php

namespace MongoQP\Twig;

class MongoQPExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return [
            'json_encode_pretty' => new \Twig_Filter_Method($this, 'jsonEncodePretty'),
            'ksort' => new \Twig_Filter_Method($this, 'ksort'),
        ];
    }

    public function jsonEncodePretty($value, $indent = 4)
    {
        $json = json_encode($value, JSON_PRETTY_PRINT);

        if ("[\n\n]" === $json) {
            $json = '{}';
        }

        if (4 !== $indent) {
            $json = str_replace('    ', str_repeat(' ', $indent), $json);
        }

        return $json;
    }

    public function ksort(array $array)
    {
        ksort($array);
        return $array;
    }

    public function getName()
    {
        return 'mongoqp';
    }
}
