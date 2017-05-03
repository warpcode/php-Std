<?php
namespace Warpcode\test\PhpUnit\Listeners;

class PHPUnitTestListener implements \PHPUnit\Framework\TestListener {
    private $time;
    private $timeLimit = 0;

    public function startTest(\PHPUnit\Framework\Test $test) {
          $this->time = microtime(true);
    }
    public function endTest(\PHPUnit\Framework\Test $test, $time) {
        $current = microtime(true);
        $took = $current - $this->time;
        echo "\nName: ".$test->getName()." took ".$took . " second(s)\n";
    }
    public function addError(\PHPUnit\Framework\Test $test, \Exception $e, $time) {}
    public function addFailure(\PHPUnit\Framework\Test $test, \PHPUnit\Framework\AssertionFailedError $e, $time) {}
    public function addWarning(\PHPUnit\Framework\Test $test, \PHPUnit\Framework\Warning $e, $time){}
    public function addRiskyTest(\PHPUnit\Framework\Test $test, \Exception $e, $time){}
    public function addIncompleteTest(\PHPUnit\Framework\Test $test, \Exception $e, $time){}
    public function addSkippedTest(\PHPUnit\Framework\Test $test, \Exception $e, $time) {}
    public function startTestSuite(\PHPUnit\Framework\TestSuite $suite) {}
    public function endTestSuite(\PHPUnit\Framework\TestSuite $suite) {}

}
