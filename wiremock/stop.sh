#!/bin/sh

# This is executed from the tests, so the current working directory is the tests folder.
# Change to the wiremock directory
cd ../wiremock

if [ -e wiremock.pid ]; then
  kill -9 `cat wiremock.pid`
  rm pidfile
else
  echo WireMock is not started
  exit 1
fi