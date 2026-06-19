<?php
if (function_exists("opcache_reset")) {
    opcache_reset();
    echo "OPcache reseteado correctamente";
} else {
    echo "OPcache no disponible";
}
