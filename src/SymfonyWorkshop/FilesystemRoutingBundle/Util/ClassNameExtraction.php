<?php

namespace SymfonyWorkshop\FilesystemRoutingBundle\Util;

class ClassNameExtraction
{
    /**
     * Returns the full class name (with namespace) for the first class in a file.
     *
     * @param string $file File path
     *
     * @return string|null Class name or null if none found
     *
     * @see http://stackoverflow.com/questions/7153000/get-class-name-from-file
     */
    public static function getClassNameInFile($file)
    {
        $fp = fopen($file, 'r');
        $class = $namespace = $buffer = '';
        $i = 0;

        while (!$class) {
            if (feof($fp)) break;

            $buffer .= fread($fp, 512);
            $tokens = token_get_all($buffer);

            if (strpos($buffer, '{') === false) continue;

            for (;$i<count($tokens);$i++) {
                if ($tokens[$i][0] === T_NAMESPACE) {
                    for ($j=$i+1;$j<count($tokens); $j++) {
                        if ($tokens[$j][0] === T_STRING) {
                             $namespace .= '\\'.$tokens[$j][1];
                        } else if ($tokens[$j] === '{' || $tokens[$j] === ';') {
                             break;
                        }
                    }
                }

                if ($tokens[$i][0] === T_CLASS) {
                    for ($j=$i+1;$j<count($tokens);$j++) {
                        if ($tokens[$j] === '{') {
                            $class = $tokens[$i+2][1];
                        }
                    }
                }
            }
        }

        if ('' === $class) {
            return null;
        }

        return $namespace . '\\'. $class;
    }
}
