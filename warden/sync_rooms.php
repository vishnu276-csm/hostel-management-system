<?php

require_once __DIR__ . '/../config/auth.php';

checkLogin('warden');

require_once __DIR__ . '/../config/database.php';

$rooms->updateMany(
    [],
    [
        '$set' => [
            'occupied' => 0
        ]
    ]
);

$studentList = $students->find([
    'room_no' => [
        '$exists' => true,
        '$ne' => ''
    ]
])->toArray();

foreach ($studentList as $student) {

    $roomNo = trim($student['room_no'] ?? '');

    if ($roomNo != "") {

        $rooms->updateOne(
            [
                'room_no' => $roomNo
            ],
            [
                '$inc' => [
                    'occupied' => 1
                ]
            ]
        );

    }
}

header("Location: rooms.php");
exit;
?>
