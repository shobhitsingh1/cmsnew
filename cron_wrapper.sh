#!/bin/bash

# ===========================================
# CRON WRAPPER SCRIPT
# ===========================================

# Set paths
PHP_PATH="/usr/bin/php"
PROJECT_PATH="/var/www/html/cmsnew"
LOG_FILE="$PROJECT_PATH/writable/logs/cron.log"

# Create log directory if it doesn't exist
mkdir -p "$(dirname "$LOG_FILE")"

# Function to log messages
log_message() {
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] $1" >> "$LOG_FILE"
}

# Function to run cron command
run_cron() {
    cd "$PROJECT_PATH"
    log_message "Starting cron: $1"
    
    # Run the command and capture output
    OUTPUT=$($PHP_PATH index.php "$1" 2>&1)
    EXIT_CODE=$?
    
    # Log output
    log_message "Output: $OUTPUT"
    log_message "Exit code: $EXIT_CODE"
    
    if [ $EXIT_CODE -eq 0 ]; then
        log_message "Cron $1 completed successfully"
    else
        log_message "Cron $1 failed with exit code $EXIT_CODE"
    fi
    
    log_message "----------------------------------------"
}

# Check which cron to run based on argument
case "$1" in
    "daily")
        run_cron "cronjob/daily"
        ;;
    "hourly")
        run_cron "cronjob/hourly"
        ;;
    "weekly")
        run_cron "cronjob/weekly"
        ;;
    "test")
        run_cron "cronjob/test"
        ;;
    "manual")
        # For manual with dates: ./cron_wrapper.sh manual 2024-01-01 2024-01-31
        if [ -n "$2" ] && [ -n "$3" ]; then
            run_cron "cronjob/manual/$2/$3"
        else
            echo "Usage: $0 manual [start_date] [end_date]"
            echo "Example: $0 manual 2024-01-01 2024-01-31"
            exit 1
        fi
        ;;
    *)
        echo "Usage: $0 {daily|hourly|weekly|test|manual}"
        echo "Examples:"
        echo "  $0 daily"
        echo "  $0 hourly"
        echo "  $0 weekly"
        echo "  $0 test"
        echo "  $0 manual 2024-01-01 2024-01-31"
        exit 1
        ;;
esac