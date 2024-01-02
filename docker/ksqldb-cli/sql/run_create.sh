#!/bin/bash

SCRIPT_PATH="/etc/sql/source_connectors.sql"

echo "Running script with ksql"
echo "Script path: ${SCRIPT_PATH}"

ksql <<EOF
RUN SCRIPT '${SCRIPT_PATH}';
EOF