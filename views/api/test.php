<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css"
          integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">

    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css"
          integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <!-- Latest compiled and minified JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"
            integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS"
            crossorigin="anonymous"></script>
    <title>除錯用</title>
</head>
<body>
<div class="container">
    <div class="row">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title">Request</h3>
            </div>
            <div class="panel-body">
                <div class="row">
                    <h4>Headers</h4>

                    <div class="well"><?= nl2br($request_head) ?></div>
                </div>
                <div class="row">
                    <h4>Body</h4>

                    <div class="well"><?= urldecode(is_array($request_body) ? http_build_query($request_body) :
                            $request_body) ?></div>
                </div>
            </div>
        </div>
        <div class="panel panel-success">
            <div class="panel-heading">
                <h3 class="panel-title">Response</h3>
            </div>
            <div class="panel-body">
                <div class="row">
                    <h4>Headers</h4>

                    <div class="well"><?= nl2br($response_head) ?></div>
                </div>
                <div class="row">
                    <h4>Body</h4>
                    <?php
                    $json = json_decode($response_body, JSON_PRETTY_PRINT);
                    if (json_last_error() == JSON_ERROR_NONE) { ?>
                        <pre><?= json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) ?></pre>
                    <?php } else { ?>
                        <div class="well"><?= $response_body ?></div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <?= $this->unit->report(); ?>
    </div>
</div>
<style>
    .well {
        word-break: break-all;
    }
</style>
</body>
</html>