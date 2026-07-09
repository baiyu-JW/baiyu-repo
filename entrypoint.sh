#!/bin/sh
set -e

# If a log file path is passed (e.g. `docker run log-analyzer access.log`),
# override the log_file in config.json so analyze.php reads it.
if [ "$#" -gt 0 ] && [ -f "$1" ]; then
    php -r '
        $config = json_decode(file_get_contents("config.json"), true);
        $config["log_file"] = $argv[1];
        file_put_contents("config.json", json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    ' "$1"
    shift
fi

exec php analyze.php "$@"
