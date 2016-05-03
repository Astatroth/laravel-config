<?php

namespace Astatroth\LaravelConfig;

use Exception;

class Rewrite
{
    public function toFile($filePath, $newValues, $useValidation = true)
    {
        $contents = file_get_contents($filePath);
        $contents = $this->toContent($contents, $newValues, $useValidation);

        file_put_contents($filePath, $contents);

        return $contents;
    }

    public function toContent($contents, $newValues, $useValidation = true)
    {
        $contents = $this->parseContent($contents, $newValues);

        if ($useValidation) {
            $result = eval('?>'.$contents);

            foreach ($newValues as $key => $expectedValue) {
                $parts = explode('.', $key);

                $array = $result;

                foreach ($parts as $part) {
                    if (!is_array($array) || !array_key_exists($part, $array)) {
                        throw new Exception(sprintf('Unable to rewrite key "%s" in config, does it exist?', $key));
                    }

                    $array = $array[$part];
                }

                $actualValue = $array;

                if ($actualValue != $expectedValue) {
                    throw new Exception(sprintf('Unable to rewrite key "%s", rewrite failed.', $key));
                }
            }
        }

        return $contents;
    }

    private function parseContent($contents, $newValues)
    {
        $patterns = [];
        $replacements = [];

        foreach ($newValues as $path => $value) {
            $items = explode('.', $path);
            $key = array_pop($items);

            if (is_string($value) && strpos($value, "'") === false) {
                $replaceValue = "'".$value."'";
            } elseif (is_string($value) && strpos($value, '"') === false) {
                $replaceValue = '"'.$value.'"';
            } elseif (is_bool($value)) {
                $replaceValue = ($value ? 'true' : 'false');
            } elseif (is_null($value)) {
                $replaceValue = 'null';
            } else {
                $replaceValue = $value;
            }

            $patterns[] = $this->buildStringExpression($key, $items);
            $replacements[] = '${1}${2}'.$replaceValue;

            $patterns[] = $this->buildStringExpression($key, $items, '"');
            $replacements[] = '${1}${2}'.$replaceValue;

            $patterns[] = $this->buildConstantExpression($key, $items);
            $replacements[] = '${1}${2}'.$replaceValue;
        }

        return preg_replace($patterns, $replacements, $contents, 1);
    }

    private function buildStringExpression($targetKey, $arrayItems = [], $quoteChar = "'")
    {
        $expression = [];

        $expression[] = $this->buildArrayOpeningExpression($arrayItems);

        $expression[] = '([\'|"]'.$targetKey.'[\'|"]\s*=>\s*)['.$quoteChar.']';

        $expression[] = '([^'.$quoteChar.']*)';

        $expression[] = '['.$quoteChar.']';

        return '/'.implode('', $expression).'/';
    }

    private function buildConstantExpression($targetKey, $arrayItems = [])
    {
        $expression = [];

        $expression[] = $this->buildArrayOpeningExpression($arrayItems);

        $expression[] = '([\'|"]'.$targetKey.'[\'|"]\s*=>\s*)';

        $expression[] = '([tT][rR][uU][eE]|[fF][aA][lL][sS][eE]|[nN][uU][lL]{2}|[\d]+)';

        return '/'.implode('', $expression).'/';
    }

    private function buildArrayOpeningExpression($arrayItems)
    {
        if (count($arrayItems)) {
            $itemOpen = [];

            foreach ($arrayItems as $item) {
                $itemOpen[] = '[\'|"]'.$item.'[\'|"]\s*=>\s*(?:[aA][rR]{2}[aA][yY]\(|[\[])';
            }

            $result = '('.implode('[\s\S]*', $itemOpen).'[\s\S]*?)';
        } else {
            $result = '()';
        }

        return $result;
    }
}