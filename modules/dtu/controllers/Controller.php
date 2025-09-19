<?php

namespace controllers;
interface Controller {

  function control(): void;

  static function resolve(string $path, string $meth): bool;
}
