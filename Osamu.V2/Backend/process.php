<?php
set_time_limit(300);

function logError($message) {
    error_log($message, 3, 'error_log.txt');
}

function executePythonScript($script, $data) {
    $python = escapeshellcmd('python3'); // Use python3 if that's your Python version
    $descriptorspec = [
        0 => ['pipe', 'r'],
        1 => ['pipe', 'w'],
        2 => ['pipe', 'w'],
    ];
    $process = proc_open("$python $script", $descriptorspec, $pipes);
    
    if (is_resource($process)) {
        fwrite($pipes[0], $data);
        fclose($pipes[0]);
        $response = stream_get_contents($pipes[1]);
        fclose($pipes[1]);
        $error = stream_get_contents($pipes[2]);
        fclose($pipes[2]);
        $return_value = proc_close($process);

        if ($return_value !== 0) {
            logError("Python script error: $error");
            return json_encode(['error' => "Python script error: $error"]);
        }

        return $response;
    } else {
        $errorMessage = 'Failed to open process.';
        logError($errorMessage);
        return json_encode(['error' => $errorMessage]);
    }
}

$message = isset($_POST['message']) ? $_POST['message'] : '';
if (!$message) {
    $errorMessage = 'No message content provided';
    echo json_encode(['error' => $errorMessage]);
    logError($errorMessage);
    exit;
}

$messageData = ['message' => $message];
$jsonData = json_encode($messageData);

$command = escapeshellarg('gemini.py');
$response = executePythonScript($command, $jsonData);

if ($response !== false) {
    echo $response;
} else {
    $errorMessage = 'An error occurred while processing the message.';
    echo json_encode(['error' => $errorMessage]);
    logError($errorMessage);
}
?>
