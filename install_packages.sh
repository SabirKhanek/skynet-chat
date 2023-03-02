#!/bin/bash
cd `dirname $0`
# Check if pip is installed
if ! command -v pip > /dev/null; then
    echo "pip not found, installing pip..."
    echo "pip not found, installing pip..." >> ./logs/requirements.logs
    sudo apt-get install python3-pip >> requirements.logs 2>&1
fi

# Check if virtualenv is installed
if ! command -v virtualenv > /dev/null; then
    echo "virtualenv not found, installing virtualenv..."
    echo "virtualenv not found, installing virtualenv..." >> ./logs/requirements.logs
    sudo pip3 install virtualenv >> ./logs/requirements.logs 2>&1
fi

# Create virtual environment
virtualenv skynet-py >> ./logs/requirements.logs 2>&1

# Change directory to virtual environment
cd skynet-py

# Activate virtual environment
source bin/activate

# Install packages from requirements.txt
pip install -r ./requirements.txt >> ../logs/requirements.logs 2>&1

echo "Done!"
python server.py
