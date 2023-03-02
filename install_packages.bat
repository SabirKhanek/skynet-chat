cd %~dp0
REM Check if pip is installed
pip show pip 2>nul | find "Name: pip" >nul
if %errorlevel% neq 0 (
    echo pip not found, installing pip...
    echo pip not found, installing pip... >> logs/requirements.logs
    python -m ensurepip --upgrade >> logs/requirements.logs 2>&1
)

REM Check if virtualenv is installed
pip show virtualenv 2>nul | find "Name: virtualenv" >nul
if %errorlevel% neq 0 (
    echo virtualenv not found, installing virtualenv...
    echo virtualenv not found, installing virtualenv... >> logs/requirements.logs
    pip install virtualenv >> logs/requirements.logs 2>&1
)

REM Create virtual environment
virtualenv skynet-py >> logs/requirements.logs 2>&1

REM Change directory to virtual environment
cd skynet-py

REM Activate virtual environment
call Scripts\activate

REM Install packages from requirements.txt
pip install -r .\requirements.txt >> ..\logs\requirements.logs 2>&1

echo Done!
python server.py >> ..\logs\py_server.logs 2>&1
