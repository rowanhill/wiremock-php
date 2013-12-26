#!/bin/sh

# This is executed from the tests, so the current working directory is the tests folder.
# Change to the wiremock directory
cd ../wiremock

# Ensure WireMock isn't already running
if [ -e wiremock.pid ]; then
    echo WireMock is already started: see process `cat wiremock.pid` 1>&2
    exit 1
fi

# Download the wiremock jar if we need it
if ! [ -e wiremock-standalone.jar ]; then
    echo WireMock standalone JAR missing. Downloading.
    curl http://repo1.maven.org/maven2/com/github/tomakehurst/wiremock/1.39/wiremock-1.39-standalone.jar -o wiremock-standalone.jar
    status = $?
    if [ $status -ne 0 ]; then
        echo curl could not download WireMock JAR 1>&2
        exit $status
    fi
fi

# Start WireMock in standalone mode (in a background process) and save its output to a log
java -jar wiremock-standalone.jar &> wiremock.log 2>&1 &
echo $! > wiremock.pid

echo WireMock started