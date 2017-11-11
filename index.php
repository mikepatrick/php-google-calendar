<?php
require_once __DIR__.'/vendor/autoload.php';

session_start();

$client = new Google_Client();
$client->setAuthConfig('client_secrets.json');
$client->addScope(Google_Service_Calendar::CALENDAR_READONLY);

if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
  $client->setAccessToken($_SESSION['access_token']);

  // Print the next 10 events on the user's calendar.
  $calendarId = 'primary';
  $optParams = array(
    'maxResults' => 10,
    'orderBy' => 'startTime',
    'singleEvents' => TRUE,
    'timeMin' => date('c'),
  );
  
  $service = new Google_Service_Calendar($client);
  $results = $service->events->listEvents($calendarId, $optParams);

  if (count($results->getItems()) == 0) {
    print "No upcoming events found.\n";
  } else {
    print "Upcoming events:\n";
    foreach ($results->getItems() as $event) {
      $start = $event->start->dateTime;
      if (empty($start)) {
        $start = $event->start->date;
      }
      printf("%s (%s)\n", $event->getSummary(), $start);
    }
  }

} else {
  $redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . '/oauth2callback.php';
  header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
}