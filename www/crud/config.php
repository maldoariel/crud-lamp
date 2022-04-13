<?php

return [
  'db' => [
    'host' => 'db',
    'user' => 'root',
    'pass' => 'test',
    'name' => 'tutorial_crud',
    'options' => [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]
  ]
];
