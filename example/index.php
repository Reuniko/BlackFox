<?php

require_once("BlackFox/includes.php");

$config = require('config.php');

\BlackFox\Engine::I()->Init($config);
\BlackFox\Engine::I()->Work();