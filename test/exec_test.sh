#!/bin/bash

# Do something quick, without redirecting output anywhere
cd ../wiremock

# Simulate doing something expensive, but redirect output to a log
sleep 5s &> sleep.log 2>&1 &