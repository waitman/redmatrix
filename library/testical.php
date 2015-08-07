<?php

include('ical.php');

//require 'class.iCalReader.php';

$ical   = new ICal('test.ics');
$events = $ical->events();

$date = $events[0]['DTSTART'];
echo "The ical date: ";
echo $date;
echo "\n";

echo "The Unix timestamp: ";
echo $ical->iCalDateToUnixTimestamp($date);
echo "\n";

echo "The number of events: ";
echo $ical->event_count;
echo "\n";

echo "The number of todos: ";
echo $ical->todo_count;
echo "\n";
echo "<hr/><hr/>";

var_dump($events);

foreach ($events as $event) {
    echo "SUMMARY: ".$event['SUMMARY']."\n";
    echo "DTSTART: ".$event['DTSTART']." - UNIX-Time: ".$ical->iCalDateToUnixTimestamp($event['DTSTART'])."\n";
    echo "DTEND: ".$event['DTEND']."\n";
    echo "DTSTAMP: ".$event['DTSTAMP']."\n";
    echo "UID: ".$event['UID']."\n";
    echo "CREATED: ".$event['CREATED']."\n";
    echo "DESCRIPTION: ".$event['DESCRIPTION']."\n";
    echo "LAST-MODIFIED: ".$event['LAST-MODIFIED']."\n";
    echo "LOCATION: ".$event['LOCATION']."\n";
    echo "SEQUENCE: ".$event['SEQUENCE']."\n";
    echo "STATUS: ".$event['STATUS']."\n";
    echo "TRANSP: ".$event['TRANSP']."\n";
    echo "\n\n";
}


