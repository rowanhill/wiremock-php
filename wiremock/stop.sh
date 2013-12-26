#!/bin/sh

# This is executed from the tests, so the current working directory is the tests folder.
# Change to the wiremock directory
cd ../wiremock

if [ -e wiremock.pid ]; then
  kill -9 `cat wiremock.pid`
  rm wiremock.pid
else
  echo WireMock is not started 1&>2
  exit 1
fi

echo WireMock stopped