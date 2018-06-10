#!/bin/sh

# This is executed from the tests, so the current working directory is the tests folder.
# Change to the wiremock directory
cd ../wiremock

instance=1
if [ $# -gt 0 ]; then
    instance=$1
fi
pidFile=wiremock.$instance.pid


if [ -e $pidFile ]; then
  kill -9 `cat $pidFile`
  rm $pidFile
else
  echo WireMock is not started 2>&1
  exit 1
fi

echo WireMock $instance stopped