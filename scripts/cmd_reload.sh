#!/bin/bash

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

"$DIR"/cmd_stop.sh
"$DIR"/cmd_start.sh

exit 0

