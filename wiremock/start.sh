#!/bin/sh

# This is executed from the tests, so the current working directory is the tests folder.
# Change to the wiremock directory
cd ../wiremock

instance=1
port=8080
if [ $# -gt 0 ]; then
    instance=$1
    port=$2
fi
pidFile=wiremock.$instance.pid
logFile=wiremock.$instance.log

# Ensure WireMock isn't already running
if [ -e $pidFile ]; then
    echo WireMock is already started: see process `cat $pidFile` 1>&2
    exit 1
fi

# Download the wiremock jar if we need it
if ! [ -e wiremock-standalone.jar ]; then
    echo WireMock standalone JAR missing. Downloading.
    curl http://repo1.maven.org/maven2/com/github/tomakehurst/wiremock-standalone/2.9.0/wiremock-standalone-2.9.0.jar -o wiremock-standalone.jar
    status=$?
    if [ ${status} -ne 0 ]; then
        echo curl could not download WireMock JAR 1>&2
        exit ${status}
    fi
fi

# Start WireMock in standalone mode (in a background process) and save its output to a log
java -jar wiremock-standalone.jar --port $port --root-dir $instance --verbose &> $logFile 2>&1 &
echo $! > $pidFile

echo WireMock $instance started on port $port
