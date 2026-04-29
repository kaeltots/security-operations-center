<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Logs - Security Dashboard</title>
    <link rel="stylesheet" href="/secure_web_app/assets/css/dashboard.css">
    <style>
        .upload-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        .upload-form {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #334155;
        }

        select,
        textarea,
        input[type="file"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            font-family: 'Courier New', monospace;
        }

        textarea {
            min-height: 300px;
            resize: vertical;
        }

        .btn {
            background: #1e40af;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            font-size: 15px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .btn:hover {
            background: #1e3a8a;
        }

        .form-hint {
            font-size: 12px;
            color: #94a3b8;
            margin-top: 5px;
        }

        .upload-tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        .tab-btn {
            padding: 10px 20px;
            border: 2px solid #cbd5e1;
            background: white;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .tab-btn.active {
            background: #1e40af;
            color: white;
            border-color: #1e40af;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        .sample-logs {
            background: #f8fafc;
            padding: 15px;
            border-radius: 6px;
            margin-top: 15px;
        }

        .sample-logs h4 {
            margin-bottom: 10px;
        }

        .sample-logs code {
            display: block;
            font-size: 12px;
            white-space: pre-wrap;
            word-break: break-word;
            color: #475569;
        }
    </style>
</head>
<body>
    <?php include('../includes/header.php'); ?>
    <?php include('../includes/auth_check.php'); ?>

    <div class="upload-container">
        <h1 style="margin-bottom: 20px;">📤 Upload Logs</h1>

        <div class="upload-form">
            <div class="upload-tabs">
                <button class="tab-btn active" onclick="switchTab('text')">Paste Text</button>
                <button class="tab-btn" onclick="switchTab('file')">Upload File</button>
            </div>

            <!-- Text Input Tab -->
            <div id="text" class="tab-content active">
                <form method="POST" action="/secure_web_app/api/upload-logs.php">
                    <div class="form-group">
                        <label for="log-source">Log Source Type</label>
                        <select name="source" id="log-source" required>
                            <option value="">Select a log source...</option>
                            <option value="apache">Apache Web Server</option>
                            <option value="system">System Logs</option>
                            <option value="firewall">Firewall</option>
                            <option value="ssh">SSH</option>
                            <option value="database">Database</option>
                            <option value="auth">Authentication</option>
                            <option value="custom">Custom Application</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="log-content">Log Content</label>
                        <textarea 
                            name="logs" 
                            id="log-content" 
                            placeholder="Paste your log content here..." 
                            required></textarea>
                        <div class="form-hint">Paste one or more log entries. Each line will be parsed separately.</div>
                    </div>

                    <button type="submit" class="btn">Process Logs</button>
                </form>

                <div class="sample-logs">
                    <h4>Sample Apache Log:</h4>
                    <code>192.168.1.100 - - [23/Apr/2026:14:33:22 +0000] "GET /admin.php HTTP/1.1" 200 5082</code>
                </div>

                <div class="sample-logs">
                    <h4>Sample SSH Log:</h4>
                    <code>Apr 23 14:33:22 server sshd[12345]: Failed password for invalid user admin from 192.168.1.50 port 54321 ssh2</code>
                </div>
            </div>

            <!-- File Upload Tab -->
            <div id="file" class="tab-content">
                <form method="POST" action="/secure_web_app/api/upload-logs.php" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="log-source-file">Log Source Type</label>
                        <select name="source" id="log-source-file" required>
                            <option value="">Select a log source...</option>
                            <option value="apache">Apache Web Server</option>
                            <option value="system">System Logs</option>
                            <option value="firewall">Firewall</option>
                            <option value="ssh">SSH</option>
                            <option value="database">Database</option>
                            <option value="auth">Authentication</option>
                            <option value="custom">Custom Application</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="log-file">Select Log File</label>
                        <input type="file" name="logfile" id="log-file" accept=".log,.txt" required>
                        <div class="form-hint">Supported formats: .log, .txt (max 10MB)</div>
                    </div>

                    <button type="submit" class="btn">Upload and Process</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function switchTab(tabName) {
            // Hide all tabs
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
            });
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('active');
            });

            // Show selected tab
            document.getElementById(tabName).classList.add('active');
            event.target.classList.add('active');
        }
    </script>
</body>
</html>
