<?php
if ($argc < 2) {
    echo "Usage: php github-activity.php <username>\n";
    exit(1);
}

$username = $argv[1];
$url = "https://api.github.com/users/$username/events";

// Create a stream context with a user-agent header
$options = [
    "http" => [
        "header" => "User-Agent: PHP\r\n"
    ]
];

$context = stream_context_create($options);

// Fetch user's activity from Github API
$response = file_get_contents($url, false, $context);

if ($response === false) {
    echo "Error fetching data for user: $username\n";
    exit(1);
}

// Check if there are any events
if (empty($events)) {
    echo "No events found for user: $username\n";
    exit(0);
}

// Display fetched Activity
foreach ($events as $event) {
    $type = $event["type"];
    $repo = $event["repo"]["name"];

    switch ($type) {
        case "PushEvent":
            $commits = $event["payload"]["commits"];
            $count = count($commits);
            echo "Pushed $count commits to $repo\n";
            break;
        case "IssuesEvent":
            $action = $event["payload"]["action"];
            $issue = $event["payload"]["issue"]["number"];
            echo "$action issue #$issue on $repo\n";
            break;
        case "IssueCommentEvent":
            $issue = $event["payload"]["issue"]["number"];
            echo "Commented on issue #$issue on $repo\n";
            break;
        case "PullRequestEvent":
            $action = $event["payload"]["action"];
            $pr = $event["payload"]["pull_request"]["number"];
            echo "$action pull request #$pr on $repo\n";
            break;
        case "PullRequestReviewCommentEvent":
            $pr = $event["payload"]["pull_request"]["number"];
            echo "Commented on pull request #$pr on $repo\n";
            break;
        case "WatchEvent":
            echo "Starred $repo\n";
            break;
        case "ForkEvent":
            echo "Forked $repo\n";
            break;
        case "ReleaseEvent":
            $release = $event["payload"]["release"]["tag_name"];
            echo "Released $release at $repo\n";
            break;
        default:
            echo "Unknown event: $type\n";
    }
}
