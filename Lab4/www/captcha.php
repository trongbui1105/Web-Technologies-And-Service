<?php


use Gregwar\Captcha\CaptchaBuilder;

$builder = new CaptchaBuilder;
$builder->build();

$_SESSION['phrase'] = $builder->getPhrase();

