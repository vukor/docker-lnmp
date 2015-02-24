#!/bin/sh

DIR="$( cd "$( dirname "$0" )" && pwd )"

"$DIR"/cmd_stop.sh
"$DIR"/cmd_start.sh

exit 0

