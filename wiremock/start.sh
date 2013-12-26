#!/bin/sh

# This is executed from the tests, so the current working directory is the tests folder.
# Change to the wiremock directory
cd ../wiremock

# Ensure WireMock isn't already running
if [ -e wiremock.pid ]; then
    echo WireMock is already started: see process `cat wiremock.pid`
    exit 1
fi

# Start WireMock in standalone mode (in a background process) and save its output to a log
java -jar wiremock-1.33-standalone.jar &> wiremock.log 2>&1 &
echo $! > wiremock.pid

echo WireMock started