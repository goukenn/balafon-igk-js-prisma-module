<?php
// @author: C.A.D. BONDJE DOUE
// @date: 20231219 16:05:11

require_once (function ($name) {
    // init environment    
    foreach (['IGK_BASE_DIR', 'IGK_TEST_CONTROLLER', 'IGK_APP_DIR'] as $m) {
        if (defined($m))
            continue;
        foreach ([$_SERVER, $_ENV] as $tab) {
            if (isset($tab[$m])) {
                define($m, $tab[$m]);
                break;
            }
        }
    }
    if (!defined('IGK_APP_DIR')) {
        $resolv_path = function ($dir, $value) {
            $p = realpath($value);
            if (empty($p)) {
                return str_replace("\\", "/", $dir . "/" . $value);
            }
            return $p;
        };
        // loading environment
        $bdir = isset($_SERVER["PWD"]) ? $_SERVER["PWD"] : getcwd();
        if (function_exists('simplexml_load_file')) {
            $tconfigFile = null;
            while (!empty($bdir)) {
                if (file_exists($configFile = $bdir . "/balafon.config.xml")) {
                    $tconfigFile = $configFile;
                    break;
                }
                $b = $bdir;
                $bdir = dirname($bdir);
                if ($b == $bdir) {
                    break;
                }
            }
            if (!is_null($tconfigFile)) {
                $wd = dirname($tconfigFile);
                $g = (array)simplexml_load_file($tconfigFile);
                if (key_exists('env', $g)) {
                    foreach ($g['env'] as $k) {
                        $n = "" . $k['name'];
                        $v = "" . $k['value'];
                        defined($n) || define(
                            $n,
                            preg_match("/_DIR$/", $n) ? $resolv_path($wd, $v) :
                                $v
                        );
                    }
                }
            }
        }
        !defined('IGK_APP_DIR') && define('IGK_APP_DIR', $bdir);
    }
    return constant($name);
})('IGK_APP_DIR') . "/Lib/igk/Lib/Tests/autoload.php";
