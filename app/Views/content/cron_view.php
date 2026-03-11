<div class="main">
    <div class="body_main">
        <div class="body_left">
            <div class="sidebar_widgit">
                <h2>Cron Sync Actions</h2>
                <div class="sidebar_con">
                    <button class="btn_1" style="margin-bottom: 10px; width: 100%;" onclick="syncToday()">
                        Sync Today's Devotionals
                    </button>
                    <button class="btn_1" style="margin-bottom: 10px; width: 100%;" onclick="syncHourly()">
                        Sync Last Hour
                    </button>
                    <button class="btn_1" style="margin-bottom: 10px; width: 100%;" onclick="syncWeekly()">
                        Sync Weekly
                    </button>
                    <button class="btn_1" style="margin-bottom: 10px; width: 100%;" onclick="testConnection()">
                        Test FastAPI Connection
                    </button>
                </div>
                
                <h2 style="margin-top: 30px;">Manual Sync</h2>
                <div class="sidebar_con">
                    <div class="form_row">
                        <label>Start Date:</label>
                        <input type="date" id="start_date" value="<?php echo date('Y-m-d', strtotime('-7 days')); ?>" class="form_input">
                    </div>
                    <div class="form_row">
                        <label>End Date:</label>
                        <input type="date" id="end_date" value="<?php echo date('Y-m-d'); ?>" class="form_input">
                    </div>
                    <button class="btn_1" style="margin-top: 10px; width: 100%;" onclick="syncManual()">
                        Sync Date Range
                    </button>
                </div>
            </div>
        </div>
        
        <div class="body_right">
            <h2>Cron Sync Dashboard</h2>
            
            <div class="stats_container">
                <?php foreach ($stats as $key => $stat): ?>
                <div class="stat_box">
                    <div class="stat_label"><?php echo $stat['label']; ?></div>
                    <div class="stat_value"><?php echo $stat['count']; ?></div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <div id="sync_results" style="margin-top: 30px;"></div>
            
            <div id="loading" style="display: none; text-align: center; padding: 20px;">
                <div style="font-size: 18px; color: #333; margin-bottom: 10px;">Processing...</div>
                <div class="spinner"></div>
            </div>
            
            <!-- Log output area -->
            <div id="log_output" style="margin-top: 20px; display: none;">
                <h3>Debug Logs</h3>
                <div id="log_content" style="background: #f5f5f5; border: 1px solid #ddd; padding: 10px; max-height: 200px; overflow-y: auto; font-family: monospace; font-size: 12px;"></div>
            </div>
        </div>
        <div class="clear"></div>
    </div>
</div>

<!-- Load jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<style>
.stats_container {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    margin-top: 20px;
}

.stat_box {
    background: #f5f5f5;
    border: 1px solid #ddd;
    border-radius: 5px;
    padding: 15px;
    min-width: 150px;
    text-align: center;
}

.stat_label {
    font-size: 14px;
    color: #666;
    margin-bottom: 5px;
}

.stat_value {
    font-size: 24px;
    font-weight: bold;
    color: #333;
}

#sync_results {
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 5px;
    padding: 20px;
}

.result_item {
    padding: 10px;
    border-bottom: 1px solid #eee;
}

.result_item:last-child {
    border-bottom: none;
}

.status_synced {
    color: green;
    font-weight: bold;
}

.status_skipped {
    color: orange;
    font-weight: bold;
}

.status_failed {
    color: red;
    font-weight: bold;
}

.spinner {
    border: 4px solid #f3f3f3;
    border-top: 4px solid #3498db;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    animation: spin 1s linear infinite;
    margin: 0 auto;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.btn_1 {
    background: #4CAF50;
    color: white;
    border: none;
    padding: 10px 15px;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
}

.btn_1:hover {
    background: #45a049;
}

.log-entry {
    margin-bottom: 5px;
    padding: 3px;
    border-bottom: 1px solid #eee;
}

.log-error {
    color: red;
}

.log-success {
    color: green;
}

.log-info {
    color: blue;
}
</style>

<script>
// Check if jQuery is loaded
if (typeof jQuery == 'undefined') {
    console.error('jQuery is not loaded! Loading now...');
    // Fallback: Load jQuery dynamically
    var script = document.createElement('script');
    script.src = 'https://code.jquery.com/jquery-3.6.0.min.js';
    script.onload = function() {
        console.log('jQuery loaded successfully');
        initializeFunctions();
    };
    document.head.appendChild(script);
} else {
    console.log('jQuery already loaded');
    initializeFunctions();
}

function initializeFunctions() {
    console.log('Cron functions initialized');
}

function addLog(message, type = 'info') {
    const logDiv = document.getElementById('log_content');
    const logOutput = document.getElementById('log_output');
    if (!logDiv) return;
    
    const timestamp = new Date().toLocaleTimeString();
    const logEntry = document.createElement('div');
    logEntry.className = 'log-entry log-' + type;
    logEntry.innerHTML = `<strong>[${timestamp}]</strong> ${message}`;
    logDiv.appendChild(logEntry);
    logDiv.scrollTop = logDiv.scrollHeight;
    
    // Show log output area
    logOutput.style.display = 'block';
}

function showLoading() {
    document.getElementById('loading').style.display = 'block';
    document.getElementById('sync_results').innerHTML = '';
    addLog('Loading started...', 'info');
}

function hideLoading() {
    document.getElementById('loading').style.display = 'none';
    addLog('Loading completed', 'info');
}

function showResults(data) {
    let html = '';
    
    if (data.success) {
        html += `<div style="color: green; font-weight: bold; margin-bottom: 15px;">${data.message}</div>`;
        addLog('Operation successful: ' + data.message, 'success');
        
        if (data.stats) {
            html += `<div style="margin-bottom: 20px;">`;
            html += `<div><strong>Total Found:</strong> ${data.stats.total_found}</div>`;
            html += `<div><strong>Synced:</strong> <span class="status_synced">${data.stats.synced}</span></div>`;
            html += `<div><strong>Skipped:</strong> <span class="status_skipped">${data.stats.skipped}</span></div>`;
            html += `<div><strong>Failed:</strong> <span class="status_failed">${data.stats.failed}</span></div>`;
            html += `</div>`;
            
            if (data.stats.details && data.stats.details.length > 0) {
                html += `<h3>Details:</h3>`;
                html += `<div style="max-height: 300px; overflow-y: auto;">`;
                
                data.stats.details.forEach(item => {
                    html += `<div class="result_item">`;
                    html += `<div><strong>ID:</strong> ${item.id} | <strong>Title:</strong> ${item.title}</div>`;
                    html += `<div><strong>Status:</strong> <span class="status_${item.status}">${item.status}</span></div>`;
                    if (item.reason) {
                        html += `<div><strong>Reason:</strong> ${item.reason}</div>`;
                        addLog(`Devotional ${item.id} (${item.title}): ${item.status} - ${item.reason}`, item.status === 'failed' ? 'error' : 'info');
                    }
                    if (item.message) {
                        html += `<div><strong>Message:</strong> ${item.message}</div>`;
                    }
                    html += `</div>`;
                });
                
                html += `</div>`;
            }
        }
    } else {
        html += `<div style="color: red; font-weight: bold; margin-bottom: 15px;">${data.message}</div>`;
        addLog('Operation failed: ' + data.message, 'error');
        
        if (data.error) {
            html += `<div style="background: #ffe6e6; padding: 10px; border-radius: 5px; margin-top: 10px;">`;
            html += `<strong>Error Details:</strong><br>`;
            html += `<pre style="white-space: pre-wrap; word-wrap: break-word;">${data.error}</pre>`;
            html += `</div>`;
            addLog('Error details: ' + data.error, 'error');
        }
    }
    
    document.getElementById('sync_results').innerHTML = html;
}

function syncToday() {
    addLog('Starting sync_today...', 'info');
    showLoading();
    
    $.ajax({
        url: '<?php echo base_url("cron/sync_today"); ?>',
        type: 'POST',
        dataType: 'json',
        success: function(data) {
            addLog('AJAX success for sync_today', 'success');
            hideLoading();
            showResults(data);
        },
        error: function(xhr, status, error) {
            addLog('AJAX error for sync_today: ' + error, 'error');
            addLog('Status: ' + status, 'error');
            addLog('Response: ' + xhr.responseText, 'error');
            hideLoading();
            showResults({
                success: false,
                message: 'AJAX Error: ' + error,
                error: xhr.responseText
            });
        }
    });
}

function syncHourly() {
    addLog('Starting sync_hourly...', 'info');
    showLoading();
    
    $.ajax({
        url: '<?php echo base_url("cron/sync_hourly"); ?>',
        type: 'POST',
        dataType: 'json',
        success: function(data) {
            addLog('AJAX success for sync_hourly', 'success');
            hideLoading();
            showResults(data);
        },
        error: function(xhr, status, error) {
            addLog('AJAX error for sync_hourly: ' + error, 'error');
            hideLoading();
            showResults({
                success: false,
                message: 'AJAX Error: ' + error
            });
        }
    });
}

function syncWeekly() {
    addLog('Starting sync_weekly...', 'info');
    showLoading();
    
    $.ajax({
        url: '<?php echo base_url("cron/sync_weekly"); ?>',
        type: 'POST',
        dataType: 'json',
        success: function(data) {
            addLog('AJAX success for sync_weekly', 'success');
            hideLoading();
            showResults(data);
        },
        error: function(xhr, status, error) {
            addLog('AJAX error for sync_weekly: ' + error, 'error');
            hideLoading();
            showResults({
                success: false,
                message: 'AJAX Error: ' + error
            });
        }
    });
}

function syncManual() {
    const startDate = document.getElementById('start_date').value;
    const endDate = document.getElementById('end_date').value;
    
    if (!startDate || !endDate) {
        addLog('Validation failed: Start and end dates required', 'error');
        alert('Please select both start and end dates');
        return;
    }
    
    addLog('Starting sync_manual with dates: ' + startDate + ' to ' + endDate, 'info');
    showLoading();
    
    $.ajax({
        url: '<?php echo base_url("cron/sync_manual"); ?>',
        type: 'POST',
        data: {
            start_date: startDate,
            end_date: endDate
        },
        dataType: 'json',
        success: function(data) {
            addLog('AJAX success for sync_manual', 'success');
            hideLoading();
            showResults(data);
        },
        error: function(xhr, status, error) {
            addLog('AJAX error for sync_manual: ' + error, 'error');
            hideLoading();
            showResults({
                success: false,
                message: 'AJAX Error: ' + error
            });
        }
    });
}

function testConnection() {
    addLog('Testing FastAPI connection...', 'info');
    showLoading();
    
    $.ajax({
        url: '<?php echo base_url("cron/test_connection"); ?>',
        type: 'POST',
        dataType: 'json',
        success: function(data) {
            addLog('AJAX success for test_connection', 'success');
            hideLoading();
            showResults(data);
        },
        error: function(xhr, status, error) {
            addLog('AJAX error for test_connection: ' + error, 'error');
            hideLoading();
            showResults({
                success: false,
                message: 'AJAX Error: ' + error
            });
        }
    });
}

// Initialize on page load
$(document).ready(function() {
    addLog('Page loaded successfully', 'success');
    addLog('jQuery version: ' + $.fn.jquery, 'info');
    addLog('Base URL: <?php echo base_url(); ?>', 'info');
});
</script>