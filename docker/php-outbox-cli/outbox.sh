#!/bin/bash

while true; do
    php bin/console m:c scheduler_outbox
    sleep 1
done